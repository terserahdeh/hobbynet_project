<?php

namespace App\Http\Controllers;

use App\Models\StarterPack;
use App\Models\Badge;
use App\Services\BadgeService;
use App\Services\CourseContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function show(Request $request, StarterPack $starterPack)
    {
        $starterPack->load(['project', 'items']);
        abort_unless($starterPack->project, 404);
        if ($starterPack->is_premium && ! $request->user()->isPlus()) {
            return redirect()->route('plus')->with('status', 'Course ini khusus member HobbyNet Plus. Aktifkan Plus untuk melanjutkan (mode demo).');
        }
        $lessons = CourseContent::lessons($starterPack);
        $completed = DB::table('course_progress')->where('user_id', $request->user()->id)
            ->where('starter_pack_id', $starterPack->id)->pluck('lesson_number')->map(fn ($n) => (int) $n)->all();
        // Pulihkan lencana untuk course yang sudah tuntas sebelum patch V3.
        if (count($completed) >= count($lessons) && count($lessons) > 0) {
            $this->giveCompletionBadge($request->user(), $starterPack);
        }
        $active = collect($lessons)->first(fn ($l) => ! in_array($l['number'], $completed))['number'] ?? count($lessons);
        return view('courses.show', compact('starterPack', 'lessons', 'completed', 'active'));
    }

    public function complete(Request $request, StarterPack $starterPack, int $lesson)
    {
        abort_if($starterPack->is_premium && ! $request->user()->isPlus(), 403);
        $lessons = CourseContent::lessons($starterPack->load('project'));
        abort_unless($lesson >= 1 && $lesson <= count($lessons), 404);
        $previousCompleted = DB::table('course_progress')->where('user_id', $request->user()->id)
            ->where('starter_pack_id', $starterPack->id)->pluck('lesson_number')->map(fn ($n) => (int) $n)->all();
        abort_if($lesson > 1 && ! in_array($lesson - 1, $previousCompleted), 403, 'Selesaikan pelajaran sebelumnya dulu.');
        DB::table('course_progress')->insertOrIgnore([
            'user_id' => $request->user()->id, 'starter_pack_id' => $starterPack->id,
            'lesson_number' => $lesson, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $progressCount = DB::table('course_progress')->where('user_id', $request->user()->id)
            ->where('starter_pack_id', $starterPack->id)->count();
        if ($progressCount >= count($lessons)) {
            $this->giveCompletionBadge($request->user(), $starterPack);
            return redirect()->route('courses.show', $starterPack)->with('status', 'Selamat! Course selesai dan lencana sudah masuk profilmu.');
        }
        return redirect()->route('courses.show', $starterPack)->with('status', 'Pelajaran terselesaikan. Lanjutkan ke tahap berikutnya!');
    }
    public function submission(Request $request, StarterPack $starterPack)
    {
        $this->ensureCourseAccess($request, $starterPack);
        $lessons = CourseContent::lessons($starterPack);
        $finished = DB::table('course_progress')->where('user_id', $request->user()->id)
            ->where('starter_pack_id', $starterPack->id)->count();
        abort_unless($finished >= count($lessons) && count($lessons) > 0, 403, 'Selesaikan seluruh lesson terlebih dahulu.');
        $submission = \App\Models\CourseSubmission::where('user_id', $request->user()->id)
            ->where('starter_pack_id', $starterPack->id)->first();
        return view('courses.submission', compact('starterPack', 'submission'));
    }

    public function saveSubmission(Request $request, StarterPack $starterPack)
    {
        $this->ensureCourseAccess($request, $starterPack);
        $lessons = CourseContent::lessons($starterPack);
        $finished = DB::table('course_progress')->where('user_id', $request->user()->id)
            ->where('starter_pack_id', $starterPack->id)->count();
        abort_unless($finished >= count($lessons) && count($lessons) > 0, 403);
        $data = $request->validate([
            'action' => 'required|in:draft,submit',
            'title' => 'required_if:action,submit|nullable|string|max:120',
            'reflection' => 'nullable|string|max:2000',
            'photos' => 'nullable|array|max:1',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
        $submission = \App\Models\CourseSubmission::firstOrNew([
            'user_id' => $request->user()->id, 'starter_pack_id' => $starterPack->id,
        ]);
        // Satu karya hanya memiliki satu foto. Upload baru otomatis mengganti foto lama.
        $previous = $submission->photos ?? [];
        $newFile = $request->file('photos', [])[0] ?? null;
        $oldPhoto = $previous[0] ?? null;
        if ($request->input('action') === 'submit' && ! $newFile && ! $oldPhoto) {
            throw \Illuminate\Validation\ValidationException::withMessages(['photos' => 'Wajib mengunggah satu foto hasil karya.']);
        }
        $stored = null;
        try {
            if ($newFile) {
                $stored = $newFile->store('course-submissions/'.$request->user()->id.'/'.$starterPack->id, 'public');
            }
            $submission->fill([
                'title' => $data['title'] ?? null,
                'reflection' => $data['reflection'] ?? null,
                'photos' => $stored ? [$stored] : ($oldPhoto ? [$oldPhoto] : []),
                'status' => $request->input('action') === 'submit' ? 'submitted' : 'draft',
                'submitted_at' => $request->input('action') === 'submit' ? ($submission->submitted_at ?? now()) : null,
            ]);
            $submission->save();
        } catch (\Throwable $e) {
            if ($stored) \Illuminate\Support\Facades\Storage::disk('public')->delete($stored);
            throw $e;
        }
        if ($stored) {
            foreach ($previous as $path) {
                if ($path !== $stored) \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }
        return $submission->status === 'submitted'
            ? redirect()->route('courses.work.show', $submission)->with('status', 'Karya berhasil dikirim dan ditampilkan di profil!')
            : redirect()->route('courses.submission', $starterPack)->with('status', 'Draft tersimpan.');
    }

    public function work(Request $request, \App\Models\CourseSubmission $submission)
    {
        abort_unless($submission->user_id === $request->user()->id, 403);
        abort_unless($submission->status === 'submitted', 404);
        $submission->load('starterPack.project');
        return view('courses.work', compact('submission'));
    }

    /** Stream an owned image directly from the public disk, independent of symlink status. */
    public function photo(Request $request, \App\Models\CourseSubmission $submission, int $index)
    {
        abort_unless((int) $submission->user_id === (int) $request->user()->id, 403);
        $paths = $submission->photos ?? [];
        abort_unless(array_key_exists($index, $paths), 404);
        $path = $paths[$index];
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        abort_unless(is_string($path) && $disk->exists($path), 404, 'Foto tidak ditemukan di storage/app/public. Upload ulang lewat Edit Karya.');
        return $disk->response($path, null, ['Cache-Control' => 'private, max-age=300']);
    }

    private function ensureCourseAccess(Request $request, StarterPack $starterPack): void
    {
        abort_if($starterPack->is_premium && ! $request->user()->isPlus(), 403);
        abort_unless($starterPack->project, 404);
    }

    private function giveCompletionBadge(\App\Models\User $user, StarterPack $starterPack): void
    {
        $badge = Badge::firstOrCreate(
            ['slug' => 'course-selesai-' . $starterPack->id],
            ['name' => 'Lulus: ' . \Illuminate\Support\Str::limit($starterPack->project->title, 65, ''),
             'description' => 'Menuntaskan seluruh materi course di Jelajah.',
             'icon' => 'star', 'color' => 'sage']
        );
        app(BadgeService::class)->award($user, $badge, $starterPack->project_id);
    }

}
