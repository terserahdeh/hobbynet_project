<?php

namespace App\Http\Controllers;

use App\Models\AiMessage;
use App\Services\AiCoachService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class AiCoachController extends Controller
{
    public function __construct(private AiCoachService $coach)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('ai.coach', [
            'messages' => $this->coach->history($user),
            'context' => $this->coach->context($user),
            'limit' => $this->coach->limitFor($user),
            'remaining' => $this->coach->remaining($user),
            'isPlus' => $user->isPlus(),
            'provider' => $this->coach->provider(),
        ]);
    }

    public function ask(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:600'],
            'mode' => ['nullable', 'in:weekly_plan'],
        ]);

        $user = $request->user();

        // Rencana belajar 7 hari adalah fitur Plus.
        if (($data['mode'] ?? null) === 'weekly_plan' && ! $user->isPlus()) {
            $msg = 'Rencana belajar 7 hari adalah fitur HobbyNet Plus.';

            return $request->expectsJson()
                ? response()->json(['error' => $msg, 'upgrade' => true, 'remaining' => $this->coach->remaining($user)], 403)
                : back()->withErrors(['message' => $msg]);
        }

        if ($this->coach->remaining($user) <= 0) {
            $msg = $user->isPlus()
                ? 'Batas pemakaian wajar hari ini sudah tercapai. Coba lagi besok.'
                : 'Kuota Coach AI gratis hari ini habis. Upgrade ke HobbyNet Plus untuk tanya sepuasnya.';

            return $request->expectsJson()
                ? response()->json(['error' => $msg, 'upgrade' => ! $user->isPlus(), 'remaining' => 0], 429)
                : back()->withErrors(['message' => $msg]);
        }

        $result = $this->coach->ask($user, trim($data['message']), $data['mode'] ?? null);
        $remaining = $this->coach->remaining($user);

        if ($request->expectsJson()) {
            return response()->json($result + ['remaining' => $remaining]);
        }

        return back();
    }

    public function clear(Request $request): RedirectResponse
    {
        AiMessage::where('user_id', $request->user()->id)->delete();

        return redirect()->route('coach')->with('status', 'Riwayat percakapan dihapus.');
    }
}
