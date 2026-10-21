<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonalizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonalizeController extends Controller
{
    /** Onboarding Langkah 2: personalisasi. */
    public function create(Request $request): View
    {
        $user = $request->user();

        return view('onboarding.personalize', [
            'experience' => old('experience_level', $user->experience_level ?? 'beginner'),
            'tools'      => old('tools', $user->tools ?? []),
            'budget'     => old('budget', $user->budget),
        ]);
    }

    public function store(StorePersonalizationRequest $request): RedirectResponse
    {
        $user  = $request->user();
        $tools = $request->validated('tools') ?? [];

        if (in_array('none', $tools, true)) {
            $tools = ['none'];
        }

        $user->update([
            'experience_level' => $request->validated('experience_level'),
            'tools'            => array_values(array_unique($tools)),
            'budget'           => $request->validated('budget'),
            'onboarding_step'  => max($user->onboarding_step, 3),
        ]);

        return redirect()->route('onboarding.hobbies');
    }

    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['onboarding_step' => max($user->onboarding_step, 3)]);

        return redirect()->route('onboarding.hobbies');
    }
}
