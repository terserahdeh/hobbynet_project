<?php

namespace App\Http\Controllers\Roadmap;

use App\Http\Controllers\Controller;
use App\Models\Hobby;
use App\Models\StepMaterial;
use App\Services\MaterialsChecklist;
use App\Services\RoadmapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoadmapMaterialController extends Controller
{
    public function __construct(private RoadmapService $roadmap, private MaterialsChecklist $materials)
    {
    }

    /** "Sudah punya" on/off for one material. */
    public function toggle(Request $request, Hobby $hobby, string $stepSlug, StepMaterial $material): JsonResponse|RedirectResponse
    {
        $project = $this->roadmap->starterProject($hobby);
        $summary = $this->roadmap->summary($request->user(), $project);
        $step = $summary['steps']->firstWhere('slug', $stepSlug);

        abort_if($step === null || $step->state === 'locked', 404);
        abort_unless($material->roadmap_step_id === $step->id, 404);

        $owned = $this->materials->toggle($request->user(), $material);

        if ($request->expectsJson()) {
            $step->load('materials');
            $state = $this->materials->forStep($step, $request->user());

            return response()->json([
                'owned' => $owned,
                'stats' => $state['stats'] + [
                    'budget_label' => MaterialsChecklist::rupiah($state['stats']['budget']),
                    'to_buy_label' => MaterialsChecklist::rupiah($state['stats']['to_buy']),
                ],
            ]);
        }

        return back();
    }
}
