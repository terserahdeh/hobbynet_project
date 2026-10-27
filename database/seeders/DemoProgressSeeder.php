<?php

namespace Database\Seeders;

use App\Models\DailyActivity;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProject;
use Illuminate\Database\Seeder;

/**
 * Opsional: isi data contoh agar beranda terlihat seperti desain.
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

        $user->hobbies()->syncWithoutDetaching(
            Hobby::whereIn('slug', ['crochet', 'photography'])->pluck('id')
        );
        $user->update(['onboarding_step' => 4]);

        $crochet = Project::where('slug', 'alas-gelas-rajut-katun')->first();
        $photo   = Project::where('slug', 'cerita-foto-sehari-hari')->first();

        UserProject::updateOrCreate(
            ['user_id' => $user->id, 'project_id' => $crochet->id],
            ['progress_percent' => 60, 'last_worked_at' => now()]
        );
        UserProject::updateOrCreate(
            ['user_id' => $user->id, 'project_id' => $photo->id],
            ['progress_percent' => 0, 'last_worked_at' => now()->subDays(2)]
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
