<?php

namespace Database\Seeders;

use App\Models\DailyActivity;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProject;
use Illuminate\Database\Seeder;

/**
 * Opsional, HANYA untuk database lokal: isi data contoh agar beranda terlihat seperti desain.
 * Dipakai untuk user dengan email DEMO_EMAIL (.env), atau user terakhir.
 *   php artisan db:seed --class=DemoProgressSeeder
 */
class DemoProgressSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', env('DEMO_EMAIL'))->first() ?? User::latest('id')->first();

        if (! $user) {
            $this->command?->warn('Belum ada pengguna. Daftar dulu lewat /onboarding/account.');
            return;
        }

        $this->call([HobbySeeder::class, ProjectSeeder::class]);

        $user->hobbies()->syncWithoutDetaching(Hobby::where('slug', 'crochet')->pluck('id'));
        $user->forceFill([
            'onboarding_step'            => 4,
            'experience_level'           => $user->experience_level ?? 'beginner',
            'default_roadmap_created_at' => now(),
        ])->save();

        $coaster = Project::where('slug', 'alas-gelas-rajut-katun')->first();

        UserProject::where('user_id', $user->id)->update(['is_roadmap' => false]);
        UserProject::updateOrCreate(
            ['user_id' => $user->id, 'project_id' => $coaster->id],
            ['steps_done' => 2, 'progress_percent' => 33, 'is_roadmap' => true, 'last_worked_at' => now()]
        );

        foreach ([0 => 30, 1 => 20, 2 => 25, 3 => 15] as $daysAgo => $minutes) {
            DailyActivity::updateOrCreate(
                ['user_id' => $user->id, 'date' => today()->subDays($daysAgo)->toDateString()],
                ['minutes' => $minutes]
            );
        }

        $this->command?->info("Data contoh dibuat untuk {$user->email}.");
    }
}
