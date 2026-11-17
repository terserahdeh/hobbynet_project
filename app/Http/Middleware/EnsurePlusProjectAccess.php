
<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Models\StarterPack;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlusProjectAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');

        if (is_string($project)) {
            $project = Project::where('slug', $project)->first();
        }

        if ($project instanceof Project) {
            $isPremium = StarterPack::where('project_id', $project->id)
                ->where('is_premium', true)
                ->exists();

            if ($isPremium && ! $request->user()?->isPlus()) {
                return redirect()->route('plus')
                    ->with('status', 'Upgrade ke HobbyNet Plus untuk membuka proyek ini.');
            }
        }

        return $next($request);
    }
}
