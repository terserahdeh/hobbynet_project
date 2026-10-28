<?php

namespace App\Services;

use App\Models\AiMessage;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Coach AI HobbyNet.
 *
 * Yang membuat ini "AI inti" dan bukan chatbot biasa: setiap jawaban dibangun dari
 * konteks pengguna (level, alat, budget, target harian) dan posisinya di roadmap
 * (langkah aktif, instruksi, dan checklist). Tanpa API key, layanan otomatis memakai
 * mode demo berbasis aturan supaya aplikasi tetap bisa didemokan.
 */
class AiCoachService
{
    public const FREE_DAILY_LIMIT = 5;
    public const PLUS_DAILY_LIMIT = 100; // batas pemakaian wajar untuk Plus

    public function __construct(private RoadmapService $roadmap)
    {
    }

    /* ---------------------------------------------------------------- Kuota */

    public function limitFor(User $user): int
    {
        return $user->isPlus() ? self::PLUS_DAILY_LIMIT : self::FREE_DAILY_LIMIT;
    }

    public function usedToday(User $user): int
    {
        return AiMessage::where('user_id', $user->id)
            ->where('role', AiMessage::ROLE_USER)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
    }

    public function remaining(User $user): int
    {
        return max(0, $this->limitFor($user) - $this->usedToday($user));
    }

    /* ------------------------------------------------------------- Penyedia */

    /** Penyedia yang benar-benar dipakai: tanpa API key jatuh ke "mock". */
    public function provider(): string
    {
        $driver = config('services.ai.driver', 'anthropic');

        return match (true) {
            $driver === 'anthropic' && config('services.ai.anthropic.key') => 'anthropic',
            $driver === 'gemini' && config('services.ai.gemini.key') => 'gemini',
            default => 'mock',
        };
    }

    /* ----------------------------------------------------------- Percakapan */

    public function history(User $user, int $limit = 30)
    {
        return AiMessage::where('user_id', $user->id)
            ->orderByDesc('id')->limit($limit)->get()->reverse()->values();
    }

    /**
     * Kirim pertanyaan, simpan kedua pesan, kembalikan balasan.
     *
     * @return array{reply: string, provider: string, fallback: bool}
     */
    public function ask(User $user, string $message, ?string $mode = null): array
    {
        $context = $this->context($user);
        $context['mode'] = $mode;
        $history = $this->history($user, 10);

        $provider = $this->provider();
        $fallback = false;

        try {
            $reply = match ($provider) {
                'anthropic' => $this->callAnthropic($context, $history, $message),
                'gemini' => $this->callGemini($context, $history, $message),
                default => $this->mockReply($context, $mode === 'weekly_plan' ? 'rencana 7 hari' : $message),
            };
        } catch (Throwable $e) {
            // Jangan sampai demo mati hanya karena API bermasalah.
            Log::warning('Coach AI gagal, memakai mode demo: ' . $e->getMessage());
            $reply = $this->mockReply($context, $mode === 'weekly_plan' ? 'rencana 7 hari' : $message);
            $provider = 'mock';
            $fallback = true;
        }

        AiMessage::create(['user_id' => $user->id, 'role' => AiMessage::ROLE_USER, 'content' => $message]);
        AiMessage::create([
            'user_id' => $user->id, 'role' => AiMessage::ROLE_ASSISTANT,
            'content' => $reply, 'provider' => $provider,
        ]);

        return compact('reply', 'provider', 'fallback');
    }

    /* -------------------------------------------------------------- Konteks */

    /** @return array<string, mixed> */
    public function context(User $user): array
    {
        $ctx = [
            'name' => Str::before((string) $user->name, ' '),
            'level' => $user->experience_level ?: 'pemula',
            'tools' => is_array($user->tools) ? $user->tools : [],
            'budget' => $user->budget,
            'goal' => (int) ($user->daily_goal_minutes ?: 45),
            'hobbies' => $user->hobbies()->orderBy('sort')->get()->map(fn ($h) => $h->short_name)->all(),
            'hobby' => null, 'project' => null, 'step' => null,
        ];

        try {
            $hobby = $this->roadmap->defaultHobby($user);
            if ($hobby) {
                $project = $this->roadmap->starterProject($hobby);
                $summary = $this->roadmap->summary($user, $project);
                $active = $summary['active'];

                $ctx['hobby'] = $hobby->short_name;
                $ctx['project'] = $project->title;
                $ctx['percent'] = $summary['percent'];

                if ($active) {
                    $active->load(['instructions', 'checklistItems']);
                    $ctx['step'] = [
                        'title' => $active->title,
                        'objective' => $active->objective,
                        'instructions' => $active->instructions->pluck('body')->all(),
                        'checklist' => $active->checklistItems->pluck('label')->all(),
                        'minutes' => $active->estimated_minutes,
                    ];
                }
            }
        } catch (Throwable $e) {
            // Belum ada roadmap untuk hobi ini: coach tetap jalan tanpa konteks langkah.
        }

        return $ctx;
    }

    public function systemPrompt(array $ctx): string
    {
        $lines = [
            'Kamu adalah Coach AI HobbyNet, pendamping hangat untuk pemula yang baru belajar hobi kreatif.',
            'Jawab dalam bahasa Indonesia, santai tapi jelas, maksimal sekitar 150 kata.',
            'Beri langkah konkret yang bisa dicoba sekarang, satu atau dua saja, bukan ceramah panjang.',
            'Jangan mengarang fakta, harga pasti, atau merek tertentu. Tetap pada topik hobi dan belajar berkarya.',
            'Jika pengguna kesal atau ragu, akui dulu perasaannya, lalu beri langkah kecil.',
            '',
            'Konteks pengguna:',
            "- Nama panggilan: {$ctx['name']}",
            "- Level: {$ctx['level']}",
            '- Hobi dipilih: ' . ($ctx['hobbies'] ? implode(', ', $ctx['hobbies']) : 'belum memilih'),
            '- Alat yang dimiliki: ' . ($ctx['tools'] ? implode(', ', $ctx['tools']) : 'belum diisi'),
            '- Budget: ' . ($ctx['budget'] ?: 'belum diisi'),
            "- Target harian: {$ctx['goal']} menit",
        ];

        if ($ctx['project']) {
            $lines[] = "- Proyek saat ini: {$ctx['project']} ({$ctx['hobby']}), progres " . ($ctx['percent'] ?? 0) . '%';
        }
        if ($ctx['step']) {
            $lines[] = '- Langkah aktif: ' . $ctx['step']['title'] . ' (tujuan: ' . $ctx['step']['objective'] . ')';
            $lines[] = '- Instruksi langkah ini: ' . implode(' | ', $ctx['step']['instructions']);
            $lines[] = '- Checklist langkah ini: ' . implode(' | ', $ctx['step']['checklist']);
        }

        if (($ctx['mode'] ?? null) === 'weekly_plan') {
            $lines[] = '';
            $lines[] = 'TUGAS KHUSUS: susun rencana belajar 7 hari (Hari 1 sampai Hari 7), satu baris per hari, '
                . 'sesuai target harian, level, dan langkah aktif pengguna. Sertakan satu hari istirahat.';
        }

        return implode("\n", $lines);
    }

    /* ------------------------------------------------------ Panggilan API */

    /** @return array<int, array{role: string, content: string}> bergantian user/assistant, diawali user */
    private function turns($history, string $message): array
    {
        $turns = [];
        foreach ($history as $m) {
            $last = end($turns);
            if ($last && $last['role'] === $m->role) {
                $turns[count($turns) - 1]['content'] .= "\n" . $m->content;
            } else {
                $turns[] = ['role' => $m->role, 'content' => $m->content];
            }
        }
        while ($turns && $turns[0]['role'] !== 'user') {
            array_shift($turns);
        }
        $last = end($turns);
        if ($last && $last['role'] === 'user') {
            $turns[count($turns) - 1]['content'] .= "\n" . $message;
        } else {
            $turns[] = ['role' => 'user', 'content' => $message];
        }

        return $turns;
    }

    private function callAnthropic(array $ctx, $history, string $message): string
    {
        $cfg = config('services.ai.anthropic');

        $res = Http::withHeaders([
            'x-api-key' => $cfg['key'],
            'anthropic-version' => '2023-06-01',
        ])->timeout(40)->post('https://api.anthropic.com/v1/messages', [
            'model' => $cfg['model'],
            'max_tokens' => 600,
            'system' => $this->systemPrompt($ctx),
            'messages' => $this->turns($history, $message),
        ])->throw();

        $text = trim((string) $res->json('content.0.text'));
        if ($text === '') {
            throw new \RuntimeException('Balasan Anthropic kosong.');
        }

        return $text;
    }

    private function callGemini(array $ctx, $history, string $message): string
    {
        $cfg = config('services.ai.gemini');

        $contents = array_map(fn ($t) => [
            'role' => $t['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $t['content']]],
        ], $this->turns($history, $message));

        $res = Http::withHeaders(['x-goog-api-key' => $cfg['key']])
            ->timeout(40)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$cfg['model']}:generateContent", [
                'systemInstruction' => ['parts' => [['text' => $this->systemPrompt($ctx)]]],
                'contents' => $contents,
                'generationConfig' => ['maxOutputTokens' => 600],
            ])->throw();

        $text = trim((string) $res->json('candidates.0.content.parts.0.text'));
        if ($text === '') {
            throw new \RuntimeException('Balasan Gemini kosong.');
        }

        return $text;
    }

    /* ------------------------------------------------------------ Mode demo */

    /** Jawaban berbasis aturan yang memakai konteks nyata pengguna. */
    public function mockReply(array $ctx, string $message): string
    {
        $m = Str::lower($message);
        $name = $ctx['name'];
        $step = $ctx['step'];
        $hobby = $ctx['hobby'] ?? ($ctx['hobbies'][0] ?? 'hobi barumu');

        if (Str::contains($m, ['jadwal', 'rencana', 'minggu', '7 hari', 'seminggu'])) {
            $g = $ctx['goal'];
            return "Ini rencana 7 hari untuk {$hobby}, {$name}, dengan target {$g} menit per hari:\n"
                . "Hari 1: siapkan alat dan bahan.\nHari 2-3: latih dasar yang sama berulang-ulang.\n"
                . "Hari 4: lanjutkan proyek saat ini" . ($ctx['project'] ? " ({$ctx['project']})" : '') . ".\n"
                . "Hari 5: istirahat, lihat inspirasi saja.\nHari 6: selesaikan bagian yang tertinggal.\n"
                . "Hari 7: foto hasilmu dan bagikan. Kecil tidak apa-apa, yang penting jalan.";
        }

        if (Str::contains($m, ['rekomendasi', 'proyek', 'budget', 'murah', 'bahan', 'alat'])) {
            $budget = $ctx['budget'] ? "budget {$ctx['budget']}" : 'budget kecil';
            return "Untuk {$hobby} dengan {$budget}, mulai dari proyek yang bahannya sedikit dan hasilnya cepat kelihatan"
                . ($ctx['project'] ? ", seperti {$ctx['project']} yang sedang kamu jalani" : '')
                . ". Cek halaman Jelajahi untuk paket pemula: di sana ada daftar bahan wajib dan tambahan opsional, jadi kamu bisa beli yang esensial dulu.";
        }

        if ($step && Str::contains($m, ['bingung', 'susah', 'sulit', 'gagal', 'macet', 'stuck', 'tidak bisa', 'nggak bisa', 'gak bisa', 'berantakan', 'jelek', 'kendor', 'kencang'])) {
            $first = $step['instructions'][0] ?? 'ulangi pelan-pelan dari awal';
            return "Wajar kok, {$name}. Di langkah \"{$step['title']}\" hampir semua pemula tersangkut di sini.\n"
                . "Coba kembali ke bagian pertama: {$first}\n"
                . "Kerjakan pelan, tidak perlu rapi. Kalau sudah, centang satu item checklist dulu, tidak usah menunggu sempurna.";
        }

        if ($step && Str::contains($m, ['langkah', 'sekarang', 'lanjut', 'mulai', 'apa'])) {
            $todo = $step['checklist'][0] ?? $step['objective'];
            return "Kamu sedang di langkah \"{$step['title']}\" ({$step['minutes']} menit). Tujuannya: {$step['objective']}.\n"
                . "Mulai dari satu hal ini: {$todo}.";
        }

        if (Str::contains($m, ['semangat', 'malas', 'bosan', 'capek', 'males'])) {
            return "Boleh banget capek, {$name}. Coba versi paling kecil: 10 menit saja, lalu berhenti kalau mau. "
                . "Biasanya yang susah itu memulai, bukan melanjutkan.";
        }

        // Jawaban demo tetap relevan berdasarkan kata kunci; untuk respons bebas perlu API.
        if (Str::contains($m, ['crochet', 'rajut', 'tusuk', 'benang', 'hook', 'rapi'])) {
            return "Untuk merajut lebih rapi, {$name}, jaga ketegangan benang tetap konsisten: jangan menarik terlalu kencang setelah setiap tusukan. "
                . "Latihan 10 menit: buat 15 tusuk rantai, lalu satu baris tusuk tunggal. Cek apakah tinggi setiap tusuk hampir sama. "
                . "Kalau masih tidak rata, coba hook satu ukuran lebih besar dan pastikan benang melewati jari dengan tekanan stabil.";
        }

        if (Str::contains($m, ['lukis', 'cat air', 'warna', 'kuas'])) {
            return "Untuk melukis, mulai dari dua warna. Basahi kuas secukupnya, buat tiga sapuan dari warna paling muda ke paling pekat. "
                . "Tunggu lapisan pertama kering sebelum menambah detail agar tidak menjadi keruh. Coba latihan ini {$ctx['goal']} menit hari ini.";
        }

        if (Str::contains($m, ['foto', 'fotografi', 'kamera', 'cahaya'])) {
            return "Coba foto objek yang sama tiga kali: menghadap jendela, dari samping jendela, dan membelakangi jendela. "
                . "Bandingkan bayangan dan detailnya, lalu pilih mana yang paling membantu ceritamu. Bersihkan lensa dahulu agar hasil lebih tajam.";
        }

        return "Aku sedang dalam mode simulasi, {$name}, jadi belum bisa menjawab bebas seperti AI generatif. "
            . "Coba jelaskan masalah spesifik pada {$hobby}, misalnya alat, teknik, atau langkah yang macet. "
            . "Untuk jawaban yang benar-benar menyesuaikan pertanyaan, aktifkan provider Gemini atau Anthropic pada konfigurasi server.";
    }
}
