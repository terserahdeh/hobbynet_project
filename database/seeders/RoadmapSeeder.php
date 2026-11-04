<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\RoadmapStep;
use Illuminate\Database\Seeder;

/**
 * Seeds badges and the beginner roadmap of every hobby (Crochet, Painting, Photography).
 * Safe to run again: steps are matched by (project, slug) and updated in place.
 * Run: php artisan db:seed --class=RoadmapSeeder
 */
class RoadmapSeeder extends Seeder
{
    public function run(): void
    {
        $badges = $this->seedBadges();

        // hobi => [metode langkah, proyek cadangan jika belum ada starter pemula]
        $roadmaps = [
            'crochet' => ['crochetSteps', [
                'slug' => 'beginner-crochet-coaster', 'title' => 'Beginner Crochet Coaster', 'category' => 'Crochet',
                'summary' => 'Make a soft, simple coaster with one basic stitch.', 'minutes' => 150,
            ]],
            'painting' => ['paintingSteps', [
                'slug' => 'beginner-watercolor-postcard', 'title' => 'Beginner Watercolor Postcard', 'category' => 'Painting',
                'summary' => 'Paint a small postcard with a soft wash of color.', 'minutes' => 120,
            ]],
            'photography' => ['photographySteps', [
                'slug' => 'beginner-photo-story', 'title' => 'A Photo Story of Everyday Life', 'category' => 'Photography',
                'summary' => 'Tell a short story about your day with five phone photos.', 'minutes' => 120,
            ]],
        ];

        foreach ($roadmaps as $hobbySlug => [$method, $fallback]) {
            $hobby = Hobby::where('slug', $hobbySlug)->first();

            if (! $hobby) {
                $this->command?->warn("No \"{$hobbySlug}\" hobby found. Seed hobbies first.");
                continue;
            }

            // Starter pemula milik hobi ini (bukan starter level lain).
            $project = Project::where('hobby_id', $hobby->id)->where('is_starter', true)->where('level', 'beginner')->first()
                ?? Project::where('hobby_id', $hobby->id)->where('is_starter', true)->first()
                ?? Project::create([
                    'hobby_id' => $hobby->id,
                    'slug' => $fallback['slug'],
                    'title' => $fallback['title'],
                    'category' => $fallback['category'],
                    'summary' => $fallback['summary'],
                    'level' => 'beginner',
                    'duration_minutes' => $fallback['minutes'],
                    'color' => $hobby->color,
                    'is_starter' => true,
                ]);

            $this->seedSteps($project, $this->{$method}($badges), $badges);
        }
    }

    private function seedSteps(Project $project, array $steps, array $badges): void
    {
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
            'wash-master' => ['Wash Master', 'For learning to paint a smooth, even watercolor wash.', 'brush', 'blue'],
            'light-spotter' => ['Light Spotter', 'For learning to see and use the light around you.', 'camera', 'blue'],
            'first-creation' => ['First Creation', 'For finishing and sharing your very first project.', 'sparkle', 'pink'],
            'course-completer' => ['Course Completer', 'For finishing every checkpoint on a roadmap.', 'check', 'green'],
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

    private function paintingSteps(array $badges): array
    {
        return [
            [
                'slug' => 'meet-your-painting-materials',
                'title' => 'Meet Your Materials',
                'summary' => 'Get your paints, brush and paper ready.',
                'objective' => 'Know what you need and set it within reach',
                'type' => 'materials',
                'minutes' => 10,
                'icon' => 'brush',
                'badge' => 'curious-explorer',
                'encouragement' => 'Great start! A tidy table makes painting feel calm and easy.',
                'materials' => [
                    ['tool', 'Round brush', 'Size 6', false],
                    ['material', 'Watercolor paints', 'A small pan set is enough', false],
                    ['material', 'Watercolor paper', '300 gsm, postcard size or A6', false],
                    ['tool', 'Jar of water and a paper towel', null, false],
                    ['tool', 'Masking tape', 'To hold the paper flat', true],
                ],
                'instructions' => [
                    'Clear a table near a window and lay out your paints, brush, paper and a jar of clean water.',
                    'Put a paper towel beside you. You will use it to dab extra water off your brush.',
                    'Tape the corners of your paper to the table so it stays flat while it gets wet.',
                ],
                'checklist' => [
                    'Gather your brush, paints and paper',
                    'Fill a jar with clean water',
                    'Tape your paper down',
                ],
            ],
            [
                'slug' => 'wet-your-brush-and-mix',
                'title' => 'Learn to Wet and Mix',
                'summary' => 'Get to know how water and color behave.',
                'objective' => 'Mix one color and make three strokes',
                'type' => 'lesson',
                'minutes' => 20,
                'icon' => 'drop',
                'badge' => null,
                'encouragement' => 'Watercolor loves to surprise you. Surprises are part of the fun.',
                'materials' => [
                    ['tool', 'Round brush', 'Size 6', false],
                    ['material', 'Watercolor paints', null, false],
                ],
                'instructions' => [
                    'Dip your brush in water, touch it to a paint pan, and swirl it on a spare corner of paper to make a puddle of color.',
                    'Add a little more water to make the color lighter, or a little more paint to make it stronger.',
                    'Make three short strokes: one pale, one medium, one dark. Notice how the color changes.',
                ],
                'checklist' => [
                    'Mix one color you like',
                    'Paint a pale, a medium and a dark stroke',
                    'Rinse your brush when you switch colors',
                ],
            ],
            [
                'slug' => 'build-your-wash-skills',
                'title' => 'Build Your Basic Skills',
                'summary' => 'Learn to paint a smooth, even wash.',
                'objective' => 'Paint one flat wash and one soft gradient',
                'type' => 'lesson',
                'minutes' => 30,
                'icon' => 'wave',
                'badge' => 'wash-master',
                'encouragement' => 'Halfway there! A good wash is the base of almost every watercolor.',
                'image' => ['images/roadmap/watercolor-wash.jpg', 'Visual Guide: Flat Wash and Gradient'],
                'materials' => [
                    ['tool', 'Round brush', 'Size 6', false],
                    ['material', 'Watercolor paints', null, false],
                    ['material', 'Practice paper', 'A spare sheet or the back of your paper', false],
                ],
                'instructions' => [
                    'Tilt your board slightly. Load your brush with plenty of color and paint a stripe across the top of a small rectangle.',
                    'While the stripe is still wet, paint the next stripe just under it so the two edges blend. Keep going to the bottom.',
                    'For a gradient, add a little more water to your brush on each stripe so the color fades as you go down.',
                ],
                'checklist' => [
                    'Paint one flat wash in a small rectangle',
                    'Paint one gradient from dark to light',
                    'Let both dry completely before you touch them',
                ],
            ],
            [
                'slug' => 'mini-painting-practice',
                'title' => 'Follow a Mini Practice Project',
                'summary' => 'Paint a tiny sunset to try your new skills.',
                'objective' => 'Finish one small practice painting',
                'type' => 'practice',
                'minutes' => 30,
                'icon' => 'sunset',
                'badge' => null,
                'encouragement' => 'Practice paintings are for learning, not for perfect. Wonky is welcome.',
                'instructions' => [
                    'Wet the whole practice sheet lightly with clean water.',
                    'Drop a warm color (yellow or orange) near the bottom and a cool color (blue or purple) near the top. Let them meet and blend.',
                    'Let it dry, then add a small dark shape, like a hill or a tree, along the bottom with a thicker color.',
                ],
                'checklist' => [
                    'Blend two colors into a sky',
                    'Add one small dark shape',
                    'Let the painting dry flat',
                ],
            ],
            [
                'slug' => 'create-your-watercolor-postcard',
                'title' => 'Create Your Watercolor Postcard',
                'summary' => 'Paint your postcard, layer by layer.',
                'objective' => 'Finish one postcard painting',
                'type' => 'project',
                'minutes' => 40,
                'icon' => 'postcard',
                'badge' => null,
                'encouragement' => 'This is the one! Take breaks and let each layer dry.',
                'materials' => [
                    ['tool', 'Round brush', 'Size 6', false],
                    ['material', 'Watercolor paints', null, false],
                    ['material', 'Watercolor paper', 'Postcard size', false],
                    ['tool', 'Fine pen', 'To add small details', true],
                ],
                'instructions' => [
                    'Pick a simple subject: a sky, a flower, or a few leaves. Lightly sketch it with a pencil if you like.',
                    'Paint the biggest, lightest areas first with a wash. Let them dry.',
                    'Add the darker colors and small details last. Peel the tape off slowly once the paper is fully dry.',
                ],
                'checklist' => [
                    'Paint your light washes first',
                    'Add darker shapes and details',
                    'Peel off the tape when fully dry',
                ],
            ],
            [
                'slug' => 'share-your-first-painting',
                'title' => 'Share Your First Creation',
                'summary' => 'Add a photo and a few words about what you painted.',
                'objective' => 'Celebrate your first finished project',
                'type' => RoadmapStep::TYPE_SHARE,
                'minutes' => 10,
                'icon' => 'sparkle',
                'badge' => 'first-creation',
                'encouragement' => 'Every master was once a beginner. This is your first masterpiece!',
            ],
        ];
    }

    private function photographySteps(array $badges): array
    {
        return [
            [
                'slug' => 'meet-your-camera',
                'title' => 'Meet Your Camera',
                'summary' => 'Get your phone or camera ready and clean.',
                'objective' => 'Know your camera and set it within reach',
                'type' => 'materials',
                'minutes' => 10,
                'icon' => 'camera',
                'badge' => 'curious-explorer',
                'encouragement' => 'Great start! The best camera is the one you already have with you.',
                'materials' => [
                    ['tool', 'Smartphone or camera', 'Any one you own is fine', false],
                    ['tool', 'Soft cloth', 'To wipe the lens', false],
                    ['material', 'Small notebook', 'For jotting down ideas', true],
                ],
                'instructions' => [
                    'Wipe your camera lens gently with a soft cloth. A clean lens makes every photo sharper.',
                    'Open your camera app and look for the grid setting. Turn it on. It will help you place things in the frame.',
                    'Take one test photo of anything near you, just to make sure everything works.',
                ],
                'checklist' => [
                    'Clean your camera lens',
                    'Turn on the grid in your camera app',
                    'Take one test photo',
                ],
            ],
            [
                'slug' => 'learn-to-see-light',
                'title' => 'Learn to See the Light',
                'summary' => 'Notice where light comes from and how it falls.',
                'objective' => 'Take three photos in three kinds of light',
                'type' => 'lesson',
                'minutes' => 20,
                'icon' => 'sun',
                'badge' => null,
                'encouragement' => 'Photography starts with noticing. You are already doing it.',
                'materials' => [
                    ['tool', 'Smartphone or camera', null, false],
                ],
                'instructions' => [
                    'Stand near a window and notice which side of an object is bright and which side is in shadow.',
                    'Photograph the same object three times: with the light from the side, from the front, and from behind it.',
                    'Look at all three. Which one feels the most interesting to you? There is no wrong answer.',
                ],
                'checklist' => [
                    'Take one photo with side light',
                    'Take one photo with front light',
                    'Take one photo with light from behind',
                ],
            ],
            [
                'slug' => 'build-your-composition-skills',
                'title' => 'Build Your Basic Skills',
                'summary' => 'Learn the rule of thirds and simple framing.',
                'objective' => 'Take photos using the rule of thirds',
                'type' => 'lesson',
                'minutes' => 30,
                'icon' => 'grid',
                'badge' => 'light-spotter',
                'encouragement' => 'Halfway there! Framing is what turns a snapshot into a photo.',
                'image' => ['images/roadmap/rule-of-thirds.jpg', 'Visual Guide: The Rule of Thirds'],
                'materials' => [
                    ['tool', 'Smartphone or camera', 'Grid turned on', false],
                ],
                'instructions' => [
                    'Look at the grid in your camera. It splits the picture into nine equal boxes. The four points where the lines cross are the strongest spots.',
                    'Place your subject on one of those points instead of the dead center, then take the photo.',
                    'Take a second photo of the same subject after moving closer, then a third one after moving to a different angle.',
                ],
                'checklist' => [
                    'Take 5 photos with your subject on a grid point',
                    'Try one photo from up close',
                    'Try one photo from a low or high angle',
                ],
            ],
            [
                'slug' => 'mini-photo-practice',
                'title' => 'Follow a Mini Practice Project',
                'summary' => 'Take a short walk and photograph one color.',
                'objective' => 'Finish a small set of practice photos',
                'type' => 'practice',
                'minutes' => 30,
                'icon' => 'walk',
                'badge' => null,
                'encouragement' => 'Practice photos are for learning, not for perfect. Keep your eyes curious.',
                'instructions' => [
                    'Pick a color, such as green or yellow, and go for a short walk indoors or outdoors.',
                    'Photograph every interesting thing you find in that color. Try to use the light and the grid you practiced.',
                    'Choose your three favorites. Notice what you were drawn to.',
                ],
                'checklist' => [
                    'Take at least 10 photos of your chosen color',
                    'Pick your 3 favorites',
                ],
            ],
            [
                'slug' => 'create-your-photo-story',
                'title' => 'Create Your Photo Story',
                'summary' => 'Tell a short story about your day in five photos.',
                'objective' => 'Finish a five-photo story',
                'type' => 'project',
                'minutes' => 40,
                'icon' => 'story',
                'badge' => null,
                'encouragement' => 'This is the one! Small everyday moments make the best stories.',
                'materials' => [
                    ['tool', 'Smartphone or camera', null, false],
                    ['material', 'Small notebook', 'To plan your five shots', true],
                ],
                'instructions' => [
                    'Choose a simple story: your morning, a walk, or a meal. Write down five moments you want to capture.',
                    'Take two or three photos for each moment, using the light and framing you practiced.',
                    'Pick the best photo for each moment and put them in order, from beginning to end.',
                ],
                'checklist' => [
                    'Plan five moments for your story',
                    'Pick the best photo for each moment',
                    'Put the five photos in order',
                ],
            ],
            [
                'slug' => 'share-your-first-photo-story',
                'title' => 'Share Your First Creation',
                'summary' => 'Add your photos and a few words about your story.',
                'objective' => 'Celebrate your first finished project',
                'type' => RoadmapStep::TYPE_SHARE,
                'minutes' => 10,
                'icon' => 'sparkle',
                'badge' => 'first-creation',
                'encouragement' => 'Every master was once a beginner. This is your first photo story!',
            ],
        ];
    }
}
