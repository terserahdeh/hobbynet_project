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
            HobbySeeder::class,
            ProjectSeeder::class,
            RoadmapSeeder::class,
            StarterPackSeeder::class,
        ]);
        // firstOrCreate: aman dijalankan berulang (email unik).
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            User::factory()->make(['name' => 'Test User'])->getAttributes()
        );
    }
}