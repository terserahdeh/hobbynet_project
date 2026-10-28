<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\RoadmapStep;
use Illuminate\Database\Seeder;

/**
 * Seeds badges and the Crochet Coaster roadmap.
 * Run: php artisan db:seed --class=RoadmapSeeder
 * Painting and Photography roadmaps follow the same shape; copy the $steps
 * array below and point it at their starter projects.
 */
class RoadmapSeeder extends Seeder
{
    public function run(): void
    {
        $badges = $this->seedBadges();

        $hobby = Hobby::where('slug', 'crochet')->first();
        if (! $hobby) {
            $this->command?->warn('No "crochet" hobby found. Seed hobbies first.');
            return;
        }

        $project = Project::where('hobby_id', $hobby->id)->where('is_starter', true)->first()
            ?? Project::create([
                'hobby_id' => $hobby->id,
                'slug' => 'beginner-crochet-coaster',
                'title' => 'Beginner Crochet Coaster',
                'category' => 'Crochet',
                'summary' => 'Make a soft, simple coaster with one basic stitch.',
                'level' => 'beginner',
                'duration_minutes' => 150,
                'color' => $hobby->color,
                'is_starter' => true,
            ]);

        foreach ($this->crochetSteps($badges) as $position => $data) {
            $step = RoadmapStep::updateOrCreate(
                ['project_id' => $project->id, 'slug' => $data['slug']],
                [
                    'position' => $position + 1,
                    'title' => $data['title'],
                    'summary' => $data['summary'],
                    'objective' => $data['objective'] ?? null,
                    'type' => $data['type'],
                    'estimated_minutes' => $data['minutes'],
                    'encouragement' => $data['encouragement'] ?? null,
                    'icon' => $data['icon'] ?? 'default',
                    'badge_id' => $data['badge'] ? $badges[$data['badge']]->id : null,
                ],
            );

            $step->materials()->delete();
            $step->instructions()->delete();
            $step->checklistItems()->delete();

            foreach ($data['materials'] ?? [] as $i => [$kind, $name, $spec, $optional]) {
                $step->materials()->create([
                    'position' => $i + 1, 'kind' => $kind, 'name' => $name,
                    'spec' => $spec, 'is_optional' => $optional,
                ]);
            }
            // 'image' => [public path, caption] is attached to the first instruction
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

    /** @return array<string, Badge> */
    private function seedBadges(): array
    {
        $rows = [
            'curious-explorer' => ['Curious Explorer', 'For getting your supplies ready and taking the first step.', 'compass', 'green'],
            'stitch-master' => ['Stitch Master', 'For learning the single crochet stitch.', 'stitch', 'blue'],
            'first-creation' => ['First Creation', 'For finishing and sharing your very first project.', 'sparkle', 'pink'],
            'course-completer' => ['Course Completer', 'For finishing every checkpoint on a roadmap.', 'check', 'green'],
            'plus-member' => ['Plus Member', 'Mendukung HobbyNet dengan paket Plus.', 'star', 'pink'],
        ];

        $badges = [];
        foreach ($rows as $slug => [$name, $description, $icon, $color]) {
            $badges[$slug] = Badge::updateOrCreate(
                ['slug' => $slug],
                compact('name', 'description', 'icon', 'color'),
            );
        }

        return $badges;
    }

    private function crochetSteps(array $badges): array
    {
        return [
            [
                'slug' => 'meet-your-materials',
                'title' => 'Meet Your Materials',
                'summary' => 'Get your hook and yarn ready.',
                'objective' => 'Know what you need and set it within reach',
                'type' => 'materials',
                'minutes' => 10,
                'icon' => 'yarn',
                'badge' => 'curious-explorer',
                'encouragement' => 'Great start! Having everything in one spot makes the rest feel easy.',
                'materials' => [
                    ['tool', 'Crochet hook', '4 mm', false],
                    ['material', 'Yarn', 'Medium weight (4), cotton works well', false],
                    ['tool', 'Scissors', null, false],
                    ['tool', 'Tapestry needle', 'For weaving in ends', true],
                ],
                'instructions' => [
                    'Lay out your hook, yarn and scissors on a table with good light.',
                    'Check the yarn label. It should say medium weight or the number 4.',
                    'Hold the hook the way a pencil feels comfortable to you. There is no wrong grip.',
                ],
                'checklist' => [
                    'Gather your hook and yarn',
                    'Check the yarn label says medium weight (4)',
                ],
            ],
            [
                'slug' => 'learn-your-first-stitches',
                'title' => 'Learn Your First Stitches',
                'summary' => 'Make a slip knot and a chain.',
                'objective' => 'Make a slip knot and chain 12',
                'type' => 'lesson',
                'minutes' => 20,
                'icon' => 'knot',
                'badge' => null,
                'encouragement' => 'Every crocheter started with a wobbly chain. Yours is allowed to be one too.',
                'materials' => [
                    ['tool', 'Crochet hook', '4 mm', false],
                    ['material', 'Yarn', 'Medium weight', false],
                ],
                'instructions' => [
                    'Make a loop in the yarn, pull a second loop through it, and slide it onto your hook. That is your slip knot.',
                    'Wrap the yarn over the hook and pull it through the loop on your hook. That makes one chain stitch.',
                    'Repeat until you have 12 chains. Keep them loose enough that the hook slides easily.',
                ],
                'checklist' => [
                    'Make a slip knot',
                    'Chain 12 stitches',
                    'Count your chains once to be sure',
                ],
            ],
            [
                'slug' => 'build-your-basic-skills',
                'title' => 'Build Your Basic Skills',
                'summary' => 'Learn the single crochet stitch.',
                'objective' => 'Learn single crochet',
                'type' => 'lesson',
                'minutes' => 30,
                'icon' => 'hook',
                'badge' => 'stitch-master',
                'encouragement' => 'Almost halfway there! The single crochet stitch is the heart of most projects.',
                'image' => ['images/roadmap/single-crochet.jpg', 'Visual Guide: Single Crochet Stitch'],
                'materials' => [
                    ['tool', 'Crochet hook', '4 mm', false],
                    ['material', 'Yarn', 'Medium weight', false],
                ],
                'instructions' => [
                    'Insert the hook into the next stitch, yarn over, and pull up a loop. You should now have two loops on your hook.',
                    'Yarn over again and pull through both loops on your hook. That completes your first single crochet.',
                    'Repeat across the row. Keep your tension even: not too tight, not too loose.',
                ],
                'checklist' => [
                    'Complete 10 rows of single crochet',
                    'Maintain consistent tension',
                    'Count your stitches every row (keep it at 12)',
                ],
            ],
            [
                'slug' => 'mini-practice-project',
                'title' => 'Follow a Mini Practice Project',
                'summary' => 'Try your new stitch on a small practice square.',
                'objective' => 'Finish a small practice square',
                'type' => 'practice',
                'minutes' => 30,
                'icon' => 'square',
                'badge' => null,
                'encouragement' => 'Practice squares are for learning, not for perfect. Wonky is welcome.',
                'instructions' => [
                    'Chain 12, then single crochet back across the row.',
                    'Turn your work, chain 1, and single crochet across again.',
                    'Keep going until your piece is roughly square, then cut the yarn and pull the tail through the last loop.',
                ],
                'checklist' => [
                    'Make a practice square about 8 cm wide',
                    'Fasten off your yarn',
                ],
            ],
            [
                'slug' => 'create-your-crochet-coaster',
                'title' => 'Create Your Crochet Coaster',
                'summary' => 'Work your coaster, round by round.',
                'objective' => 'Finish one coaster',
                'type' => 'project',
                'minutes' => 40,
                'icon' => 'coaster',
                'badge' => null,
                'encouragement' => 'This is the one! Take breaks whenever you like.',
                'materials' => [
                    ['tool', 'Crochet hook', '4 mm', false],
                    ['material', 'Cotton yarn', 'Medium weight', false],
                    ['tool', 'Tapestry needle', 'For weaving in ends', false],
                ],
                'instructions' => [
                    'Make a magic ring or chain 4 and join with a slip stitch to form a small circle.',
                    'Work single crochet stitches into the circle, increasing evenly each round so the coaster stays flat.',
                    'When it is about 10 cm across, fasten off and weave in the ends with your tapestry needle.',
                ],
                'checklist' => [
                    'Work your increase rounds',
                    'Fasten off and weave in the ends',
                    'Check that your coaster lies flat',
                ],
            ],
            [
                'slug' => 'share-your-first-creation',
                'title' => 'Share Your First Creation',
                'summary' => 'Add a photo and a few words about what you made.',
                'objective' => 'Celebrate your first finished project',
                'type' => RoadmapStep::TYPE_SHARE,
                'minutes' => 10,
                'icon' => 'sparkle',
                'badge' => 'first-creation',
                'encouragement' => 'Every master was once a beginner. This is your first masterpiece!',
            ],
        ];
    }
}
