<?php

namespace Database\Seeders;

use App\Models\Project;
use Database\Seeders\Concerns\SeedsSteps;
use Illuminate\Database\Seeder;

/**
 * Roadmap starter untuk level MENENGAH dan MAHIR (merajut, melukis, fotografi).
 * Roadmap level pemula ada di RoadmapSeeder. Jalankan SETELAH RoadmapSeeder dan ProjectSeeder:
 *   php artisan db:seed --class=RoadmapLevelSeeder
 */
class RoadmapLevelSeeder extends Seeder
{
    use SeedsSteps;

    public function run(): void
    {
        foreach ($this->data() as $slug => $def) {
            $project = Project::where('slug', $slug)->first();

            if (! $project) {
                $this->command?->warn("Proyek \"{$slug}\" belum ada. Jalankan ProjectSeeder dulu.");
                continue;
            }

            $this->seedProjectSteps($project, $def, true); // true = ada langkah "Share"
        }
    }

    private function data(): array
    {
        return [
            'tas-jaring-belanja' => [
                'materials' => [
                    ['tool', '4.0 mm crochet hook', null, false],
                    ['material', 'Cotton cord or worsted cotton yarn', 'About 150 g', false],
                    ['tool', 'Stitch markers', null, false],
                    ['tool', 'Scissors and tapestry needle', null, false],
                ],
                'steps' => [
                    [
                        'lesson',
                        'Refresh Your Foundations',
                        'Warm up your chain and single crochet before the new stitch.',
                        'Crochet a small test swatch',
                        15,
                        ['Chain 15 and work a row of single crochet back along the chain.', 'Turn and work two more rows, keeping your edges straight.', 'Count your stitches at the end of each row to check your tension.'],
                        ['Chain 15 loosely', 'Finish three even rows', 'Count the stitches in each row'],
                        'If your swatch curls, switch to a hook one size larger.',
                        null,
                        null,
                    ],
                    [
                        'lesson',
                        'Learn the Mesh Stitch',
                        'Work chain spaces into an open, stretchy grid.',
                        'Work three rows of mesh',
                        25,
                        ['Work a double crochet, chain 2, skip 2 stitches, then double crochet again to make your first open square.', 'Repeat across the row. Each chain space is one square of the mesh.', 'Turn, and work a double crochet into each stitch with chain 2 over each space.'],
                        ['Make your first mesh square', 'Complete one full mesh row', 'Work three rows with even edges'],
                        'Keep your chain spaces the same loose length so the squares stay even.',
                        'stitch-master',
                        ['images/roadmap/mesh-stitch.jpg', 'Visual Guide: Mesh Stitch'],
                    ],
                    [
                        'practice',
                        'Practice a Mesh Circle',
                        'Use the new stitch to make a small round base.',
                        'Finish a small mesh round',
                        25,
                        ['Make a magic ring and work 12 double crochet into it.', 'On the next round, chain 2 and skip 1 stitch around to create spaces.', 'Join the round and check that the squares lie flat.'],
                        ['Start with a magic ring', 'Work one round of spaces', 'Check that your circle lies flat'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'project',
                        'Crochet Your Market Bag',
                        'Build the base, body and handles of your bag.',
                        'Finish a market bag',
                        60,
                        ['Work your base in mesh until it is about 20 cm wide.', 'Continue the mesh in rounds up the sides until the bag is about 30 cm tall.', 'Chain two long straps and sew them securely to the top edge.'],
                        ['Crochet a 20 cm wide base', 'Work the body to 30 cm tall', 'Attach two sturdy handles'],
                        'Test the straps by lifting the bag with a few books inside.',
                        null,
                        null,
                    ],
                ],
            ],
            'syal-pola-kabel' => [
                'materials' => [
                    ['tool', '5.0 mm crochet hook', null, false],
                    ['material', 'Worsted wool or acrylic yarn', 'About 300 g', false],
                    ['tool', 'Cable needle or spare hook', null, true],
                    ['tool', 'Stitch markers and tapestry needle', null, false],
                ],
                'steps' => [
                    [
                        'lesson',
                        'Warm Up with Post Stitches',
                        'Practice front and back post stitches, the base of every cable.',
                        'Make a ribbed swatch',
                        20,
                        ['Chain 20 and work a foundation row of double crochet.', 'Work a front post double crochet around the next stitch, then a back post on the following one.', 'Keep alternating for four rows and notice the raised ridges.'],
                        ['Complete a foundation row', 'Work front and back post stitches', 'Finish a four-row ribbed swatch'],
                        'Insert your hook around the post from the front, not through the top loops.',
                        null,
                        null,
                    ],
                    [
                        'lesson',
                        'Master the Cable Cross',
                        'Cross groups of post stitches to form a twisting cable.',
                        'Work one full cable cross',
                        30,
                        ['Skip the next two post stitches and work two front post stitches around the posts after them.', 'Work the two skipped stitches in front of the ones you just made.', 'Repeat the cross every fourth row and check that the twist leans the same way each time.'],
                        ['Skip and work the first pair', 'Cross the second pair in front', 'Repeat to form a second cross'],
                        'Use a stitch marker on the first stitch of each cross so you never lose your place.',
                        'stitch-master',
                        ['images/roadmap/cable-cross.jpg', 'Visual Guide: Cable Cross'],
                    ],
                    [
                        'practice',
                        'Read a Cable Chart',
                        'Follow a chart to work a short cable panel.',
                        'Finish a short cable panel',
                        30,
                        ['Read the chart from bottom to top, and from right to left on right-side rows.', 'Work the first six rows, saying each stitch out loud as you go.', 'Compare your swatch to the chart and fix any crossed stitches.'],
                        ['Read the chart key', 'Work six chart rows', 'Check your swatch against the chart'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'project',
                        'Crochet Your Cable Scarf',
                        'Work a long scarf with a cable panel down the middle.',
                        'Finish a cable scarf',
                        90,
                        ['Chain enough stitches for a 20 cm wide scarf and work the ribbed borders.', 'Work the cable panel in the center, repeating the chart until the scarf is long enough.', 'Finish with a matching border, weave in the ends, and block the scarf flat.'],
                        ['Work your ribbed borders', 'Repeat the cable chart to your length', 'Weave in ends and block the scarf'],
                        'Wet-block the scarf to open up the cables and even out your stitches.',
                        null,
                        null,
                    ],
                ],
            ],
            'lanskap-gunung-berkabut' => [
                'materials' => [
                    ['tool', 'Flat brush and round brush', 'Sizes 12 and 6', false],
                    ['material', 'Watercolor tubes', 'Blue, gray and a warm earth tone', false],
                    ['material', 'Cold-press watercolor paper', '300 gsm', false],
                    ['tool', 'Masking tape and a palette', null, false],
                ],
                'steps' => [
                    [
                        'lesson',
                        'Refresh Your Wash Skills',
                        'Revisit flat washes and gradients before layering.',
                        'Paint a flat and a graded wash',
                        15,
                        ['Tape your paper down and mix a generous puddle of blue-gray.', 'Paint a flat wash across the top of a small rectangle, then a graded one below it.', 'Let both dry and notice which wash stayed smoother.'],
                        ['Tape and prepare your paper', 'Paint one flat wash', 'Paint one graded wash'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'lesson',
                        'Learn Layering and Glazing',
                        'Build depth with thin, transparent layers.',
                        'Paint three layers of hills',
                        30,
                        ['Paint the farthest hill as a pale blue-gray wash and let it dry.', 'Add a slightly darker hill in front, overlapping the first.', 'Paint the nearest hill with the darkest mix to create depth.'],
                        ['Paint a pale far hill', 'Add a darker middle hill', 'Add the darkest front hill'],
                        'Let each layer dry completely before glazing over it, or the colors will turn muddy.',
                        'wash-master',
                        ['images/roadmap/glazing-layers.jpg', 'Visual Guide: Layering Hills'],
                    ],
                    [
                        'practice',
                        'Practice Misty Edges',
                        'Soften edges so the mountains fade into mist.',
                        'Make a soft mountain edge',
                        25,
                        ['Paint a mountain shape and, while it is still wet, touch the edge with a clean damp brush.', 'Pull the color gently downward so it fades into the paper.', 'Dab with a tissue to lift extra pigment and create the mist.'],
                        ['Paint one mountain shape', 'Soften its lower edge', 'Lift a little pigment for mist'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'project',
                        'Paint Your Misty Mountains',
                        'Combine sky, layered hills and mist into one landscape.',
                        'Finish a misty landscape',
                        50,
                        ['Wet the sky area and paint a soft gradient from pale yellow to blue-gray.', 'Add three layers of mountains from far to near, letting each dry in between.', 'Soften the lowest edges into mist and peel off the tape once everything is dry.'],
                        ['Paint a soft sky', 'Layer three mountain ranges', 'Peel the tape off when dry'],
                        'Step back from your painting now and then. Distance shows what needs fixing.',
                        null,
                        null,
                    ],
                ],
            ],
            'potret-wajah-cat-air' => [
                'materials' => [
                    ['tool', 'Round brushes', 'Sizes 4 and 8', false],
                    ['material', 'Artist-grade watercolors', 'Warm and cool red, yellow and blue', false],
                    ['material', 'Cotton watercolor paper', '300 gsm, A5 or larger', false],
                    ['material', 'Reference photo and soft pencil', null, false],
                ],
                'steps' => [
                    [
                        'lesson',
                        'Study Proportions and Values',
                        'Map the face with simple guidelines and three tones.',
                        'Sketch a portrait with a value map',
                        25,
                        ['Lightly draw an oval and mark the eyes halfway down, then the nose and mouth.', 'Squint at your reference photo to see the light, mid and dark areas.', 'Shade those three areas faintly with a soft pencil as your guide.'],
                        ['Sketch the face oval and guidelines', 'Mark the eyes, nose and mouth', 'Shade a simple three-value map'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'lesson',
                        'Mix Skin Tones with Glazes',
                        'Build natural skin color from thin layers.',
                        'Create a skin tone palette',
                        30,
                        ['Mix a light base of yellow, red and a touch of blue on your palette.', 'Test it on scrap paper, then warm or cool it with small additions.', 'Glaze a second, slightly darker mix over your test patch once the first is dry.'],
                        ['Mix a light base tone', 'Make one warmer and one cooler variation', 'Glaze a second layer on your test'],
                        'Skin looks alive when warm and cool colors sit next to each other.',
                        'wash-master',
                        ['images/roadmap/skin-glazing.jpg', 'Visual Guide: Skin Tone Glazes'],
                    ],
                    [
                        'practice',
                        'Paint a Single Eye Study',
                        'Practice the details on one eye before the full face.',
                        'Finish an eye study',
                        35,
                        ['Sketch one eye large on scrap paper and lay in a pale skin wash around it.', 'Paint the iris with a darker mix, leaving a small highlight of bare paper.', 'Add the lashes and the shadow under the brow with your smallest brush.'],
                        ['Lay a light skin wash', 'Paint the iris and keep a highlight', 'Add lashes and brow shadow'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'project',
                        'Paint Your Portrait',
                        'Bring your sketch, tones and details into one portrait.',
                        'Finish a watercolor portrait',
                        120,
                        ['Transfer your sketch lightly and lay a pale skin wash over the whole face.', 'Glaze the shadow shapes layer by layer, letting each one dry before the next.', 'Paint the eyes, lips and hair last, then lift small highlights with a damp brush.'],
                        ['Lay a light base wash', 'Glaze the shadow shapes', 'Finish eyes, lips and hair'],
                        'Leave small bits of white paper for the brightest highlights.',
                        null,
                        null,
                    ],
                ],
            ],
            'seri-foto-jalanan' => [
                'materials' => [
                    ['tool', 'Camera with manual controls', 'A phone with a pro mode also works', false],
                    ['tool', 'Comfortable walking shoes', null, true],
                    ['material', 'Small notebook', null, true],
                ],
                'steps' => [
                    [
                        'lesson',
                        'Refresh the Exposure Basics',
                        'Review aperture, shutter speed and ISO.',
                        'Take three photos with different settings',
                        20,
                        ['Set your camera to manual or pro mode and find a still subject.', 'Change only the aperture, then only the shutter speed, then only the ISO, taking a photo each time.', 'Compare the three results and note what changed.'],
                        ['Switch to manual or pro mode', 'Change one setting at a time', 'Compare your three photos'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'lesson',
                        'Learn the Decisive Moment',
                        'Wait for the instant when the scene comes together.',
                        'Capture three moments',
                        30,
                        ['Choose a spot with a clean background and wait for people to pass through it.', 'Frame your shot first, then press the shutter when someone enters the frame.', 'Take several shots of each moment and pick your favorite later.'],
                        ['Choose a spot and frame it', 'Wait and capture a passing subject', 'Take at least 10 frames'],
                        'Patience beats movement. Let the scene come to you.',
                        'light-spotter',
                        ['images/roadmap/decisive-moment.jpg', 'Visual Guide: The Decisive Moment'],
                    ],
                    [
                        'practice',
                        'Shoot Moving Subjects',
                        'Practice a fast shutter speed on people in motion.',
                        'Take sharp photos of moving people',
                        30,
                        ['Set your shutter speed to at least 1/500 s and aim at a busy walkway.', 'Follow one person with your camera and shoot a short burst.', 'Check your photos for sharpness and keep the best two.'],
                        ['Set a fast shutter speed', 'Shoot a burst of a moving subject', 'Keep your two sharpest photos'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'project',
                        'Shoot Your Street Series',
                        'Make a series of eight photos around one theme.',
                        'Finish an eight-photo series',
                        90,
                        ['Pick a theme, like reflections, shadows or colors, and walk a short route.', 'Take at least 30 photos with your theme in mind.', 'Choose your best eight and arrange them in a pleasing order.'],
                        ['Pick a theme for your series', 'Take at least 30 photos', 'Choose and order your best eight'],
                        'A strong series feels connected. Look for a repeating color or shape.',
                        null,
                        null,
                    ],
                ],
            ],
            'esai-foto-naratif' => [
                'materials' => [
                    ['tool', 'Camera with a lens you can control', 'Or a high-end phone', false],
                    ['tool', 'Tripod', null, true],
                    ['material', 'Notebook for your shot list', null, false],
                    ['tool', 'Editing software or app', 'Free options work well', false],
                ],
                'steps' => [
                    [
                        'lesson',
                        'Define Your Story Angle',
                        'Choose a subject and the point of view you want to tell.',
                        'Write a one-sentence story and a shot list',
                        25,
                        ['Pick a subject you can return to, like a market, a workshop or a neighborhood.', 'Write one sentence about what you want viewers to feel.', 'List eight shots: wide, medium, detail, portrait and a closing frame.'],
                        ['Choose your subject', 'Write your one-sentence story', 'Write an eight-shot list'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'lesson',
                        'Master Consistent Light and Style',
                        'Keep light and color consistent across your series.',
                        'Shoot three frames with the same look',
                        35,
                        ['Shoot at the same time of day so the light stays consistent.', 'Lock your white balance instead of using auto.', 'Edit all three frames with the same simple settings and compare them.'],
                        ['Pick one time of day', 'Set a fixed white balance', 'Edit three frames with the same settings'],
                        'A consistent look matters more than one perfect frame.',
                        'light-spotter',
                        ['images/roadmap/consistent-style.jpg', 'Visual Guide: Consistent Style'],
                    ],
                    [
                        'practice',
                        'Test Your Shot List',
                        'Shoot six frames from your list as a trial run.',
                        'Shoot six test frames',
                        40,
                        ['Take one frame for each of the first six items on your shot list.', 'Review them together and see which ones tell your story best.', 'Note what is missing and adjust the rest of your list.'],
                        ['Shoot six frames from your list', 'Review them as a group', 'Update your list'],
                        null,
                        null,
                        null,
                    ],
                    [
                        'project',
                        'Create Your Photo Essay',
                        'Shoot, choose and caption a ten-photo essay.',
                        'Finish a photo essay',
                        120,
                        ['Shoot your full list over one or two sessions, taking extra frames of key moments.', 'Choose ten photos that tell the story in order, from the opening to the closing frame.', 'Write a short caption for each photo and edit them to match.'],
                        ['Shoot your complete shot list', 'Choose and order ten photos', 'Write a caption for each photo'],
                        'Open with a wide frame and close with a quiet detail.',
                        null,
                        null,
                    ],
                ],
            ],
        ];
    }
}