<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\UserProject;
use App\Services\RoadmapService;
use App\Support\StepBlueprints;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private RoadmapService $roadmaps)
    {
    }

    /** Detail proyek: tempat menambah (atau menghapus) proyek dari daftar. */
    public function show(Request $request, Project $project): View
    {
        $user = $request->user();
        $project->load('hobby');

        $row = $this->rowFor($request, $project);

        $userLevel = $this->roadmaps->levelFor($user);
        $diff      = Project::levelRank($project->level) - Project::levelRank($userLevel);

        $levelHint = match (true) {
            $diff === 0 => 'Cocok dengan levelmu (' . Project::levelLabel($userLevel) . ').',
            $diff > 0   => 'Proyek ini lebih menantang dari levelmu. Tidak apa-apa, ambil pelan-pelan.',
            default     => 'Proyek ini lebih mudah dari levelmu, cocok untuk menyegarkan dasar.',
        };

        return view('projects.show', [
            'project'   => $project,
            'row'       => $row,
            'steps'     => $this->roadmaps->steps($user, $project, $row),
            'materials' => StepBlueprints::materialsFor($project, $user),
            'levelHint' => $levelHint,
        ]);
    }

    public function add(Request $request, Project $project): RedirectResponse
    {
        $this->roadmaps->addToList($request->user(), $project);

        return redirect()->route('projects.show', $project)
            ->with('status', 'Ditambahkan ke daftarmu. Kamu bisa mulai kapan pun siap.');
    }

    public function makeRoadmap(Request $request, Project $project): RedirectResponse
    {
        if (! $this->rowFor($request, $project)) {
            return redirect()->route('projects.show', $project);
        }

        $this->roadmaps->activate($request->user(), $project);

        return redirect()->route('projects.show', $project)->with('status', 'Proyek ini sekarang jadi roadmap utamamu.');
    }

    public function destroy(Request $request, Project $project): RedirectResponse
    {
        $this->roadmaps->remove($request->user(), $project);

        return redirect()->route('home')->with('status', '"' . $project->title . '" dihapus dari daftarmu.');
    }

    /** Halaman satu langkah (checkpoint). */
    public function step(Request $request, Project $project, int $position): View|RedirectResponse
    {
        $row = $this->rowFor($request, $project);

        if (! $row) {
            return redirect()->route('projects.show', $project)
                ->with('status', 'Tambahkan proyek ini ke daftarmu dulu untuk memulai.');
        }

        abort_unless($position >= 1 && $position <= RoadmapService::TOTAL, 404);

        if ($position > $row->steps_done + 1) {
            return $this->toCurrentStep($project, $row, 'Selesaikan langkah sebelumnya dulu ya.');
        }

        $steps = $this->roadmaps->steps($request->user(), $project, $row);

        return view('projects.step', [
            'project'   => $project,
            'steps'     => $steps,
            'step'      => $steps[$position - 1],
            'materials' => StepBlueprints::materialsFor($project, $request->user()),
        ]);
    }

    public function completeStep(Request $request, Project $project, int $position): RedirectResponse
    {
        $row = $this->rowFor($request, $project);

        if (! $row) {
            return redirect()->route('projects.show', $project);
        }

        if (! $this->roadmaps->completeStep($request->user(), $row, $position)) {
            return $this->toCurrentStep($project, $row, 'Langkah itu sudah selesai atau belum waktunya.');
        }

        $row->refresh();

        if ($row->steps_done >= RoadmapService::TOTAL) {
            return redirect()->route('projects.show', $project)
                ->with('status', 'Kamu membuat ' . $project->title . '! Semua langkah roadmap sudah selesai.');
        }

        return redirect()->route('projects.step', ['project' => $project, 'position' => $row->steps_done + 1])
            ->with('status', 'Langkah selesai. Satu langkah kecil lagi.');
    }

    private function rowFor(Request $request, Project $project): ?UserProject
    {
        return UserProject::where('user_id', $request->user()->id)->where('project_id', $project->id)->first();
    }

    private function toCurrentStep(Project $project, UserProject $row, string $message): RedirectResponse
    {
        if ($row->steps_done >= RoadmapService::TOTAL) {
            return redirect()->route('projects.show', $project)->with('status', $message);
        }

        return redirect()->route('projects.step', ['project' => $project, 'position' => $row->steps_done + 1])
            ->with('status', $message);
    }
}
