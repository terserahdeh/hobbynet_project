<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    use WithoutModelEvents;
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            DemoProgressSeeder::class,
            ExplorePackSeeder::class,
            GuidedProjectSeeder::class,
            HobbySeeder::class,
            PremiumAccessSeeder::class,
            ProjectSeeder::class,
            ProjectVideoSeeder::class,
            RoadmapLevelSeeder::class,
            RoadmapSeeder::class,
            StarterPackSeeder::class,
            ProjectImageSeeder::class,
        ]);
        // firstOrCreate: aman dijalankan berulang (email unik).
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            User::factory()->make(['name' => 'Test User'])->getAttributes()
        );
    }
}