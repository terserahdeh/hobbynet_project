<?php

namespace Database\Seeders\Concerns;

use App\Models\Badge;
use App\Models\Project;
use App\Models\RoadmapStep;
use Illuminate\Support\Str;

/**
 * Mengubah data ringkas sebuah proyek menjadi baris roadmap_steps + bahan + instruksi + checklist,
 * dengan format yang sama seperti RoadmapSeeder. Aman dijalankan ulang.
 *
 * Data ringkas: ['materials' => [[kind, name, spec, optional], ...], 'steps' => [[type, title, summary,
 * objective, minutes, [instruksi], [checklist], tip|null, badge|null, [gambar, caption]|null], ...]]
 */
trait SeedsSteps
{
    private function encouragement(string $type): string
    {
        return [
            'materials' => 'Great start! A tidy, ready workspace makes everything easier.',
            'lesson' => "You've got this. Take your time with each step.",
            'practice' => 'Practice is for learning, not for perfect. Wonky is welcome.',
            'project' => "This is the part you've been working toward. Take breaks when you need them.",
            'share' => 'Every master was once a beginner. This is your creation!',
        ][$type] ?? 'One small step at a time.';
    }

    /** @param bool $share tambahkan langkah "Share" (khusus roadmap starter) */
    protected function seedProjectSteps(Project $project, array $def, bool $share): void
    {
        $mats = $def['materials'];
        $names = array_map(fn ($m) => $m[1], $mats);

        $steps = [[
            'slug' => 'meet-your-materials',
            'title' => 'Meet Your Materials',
            'summary' => 'Get your tools and materials ready.',
            'objective' => 'Know what you need and set it within reach',
            'type' => 'materials',
            'minutes' => 10,
            'badge' => 'curious-explorer',
            'tip' => 'Start with what you already own and add the rest as you go.',
            'materials' => $mats,
            'instructions' => [
                'Gather these items: ' . implode(', ', $names) . '.',
                'Set up a clean, well-lit workspace where you can work comfortably.',
                'Check each item off and note anything you still need to find.',
            ],
            'checklist' => ['Gather your tools and materials', 'Set up your workspace', 'Note anything still missing'],
        ]];

        foreach ($def['steps'] as [$type, $title, $summary, $objective, $minutes, $instructions, $checklist, $tip, $badge, $image]) {
            $steps[] = [
                'slug' => Str::slug($title),
                'title' => $title,
                'summary' => $summary,
                'objective' => $objective,
                'type' => $type,
                'minutes' => $minutes,
                'badge' => $badge,
                'tip' => $tip,
                'image' => $image,
                'materials' => array_slice($mats, 0, 2),
                'instructions' => $instructions,
                'checklist' => $checklist,
            ];
        }

        if ($share) {
            $steps[] = [
                'slug' => 'share-your-creation',
                'title' => 'Share Your Creation',
                'summary' => 'Add photos and a few words about what you made.',
                'objective' => 'Celebrate your finished project',
                'type' => RoadmapStep::TYPE_SHARE,
                'minutes' => 10,
                'badge' => 'first-creation',
            ];
        }

        $this->writeSteps($project, $steps);
    }

    private function writeSteps(Project $project, array $steps): void
    {
        // Buang langkah lama yang slug-nya tidak ada lagi di data baru.
        RoadmapStep::where('project_id', $project->id)
            ->whereNotIn('slug', array_column($steps, 'slug'))
            ->delete();

        foreach ($steps as $position => $data) {
            $step = RoadmapStep::updateOrCreate(
                ['project_id' => $project->id, 'slug' => $data['slug']],
                [
                    'position' => $position + 1,
                    'title' => $data['title'],
                    'summary' => $data['summary'],
                    'objective' => $data['objective'] ?? null,
                    'type' => $data['type'],
                    'estimated_minutes' => $data['minutes'],
                    'encouragement' => $this->encouragement($data['type']),
                    'icon' => 'default',
                    'badge_id' => ! empty($data['badge']) ? Badge::where('slug', $data['badge'])->value('id') : null,
                ],
            );

            // 'tip' ditulis lewat query builder, jadi tidak perlu mengubah $fillable di model.
            RoadmapStep::whereKey($step->id)->update(['tip' => $data['tip'] ?? null]);

            $step->materials()->delete();
            $step->instructions()->delete();
            $step->checklistItems()->delete();

            foreach ($data['materials'] ?? [] as $i => [$kind, $name, $spec, $optional]) {
                $step->materials()->create([
                    'position' => $i + 1, 'kind' => $kind, 'name' => $name, 'spec' => $spec, 'is_optional' => $optional,
                ]);
            }
            foreach ($data['instructions'] ?? [] as $i => $body) {
                $step->instructions()->create([
                    'position' => $i + 1,
                    'body' => $body,
                    'image' => $i === 0 ? ($data['image'][0] ?? null) : null,
                    'image_caption' => $i === 0 ? ($data['image'][1] ?? null) : null,
                ]);
            }
            foreach ($data['checklist'] ?? [] as $i => $label) {
                $step->checklistItems()->create(['position' => $i + 1, 'label' => $label]);
            }
        }
    }
}
