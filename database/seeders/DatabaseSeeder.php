<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User tetap seperti sekarang (kamu mau 2 user)
        User::factory()->create([
            'id' => 1,
            'name' => 'Risyam',
            'email' => 'risyam345@gmail.com',
            'password' => bcrypt('123.123A'),
            'email_verified_at' => time()
        ]);

        User::factory()->create([
            'id' => 2,
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'password' => bcrypt('123.321A'),
            'email_verified_at' => time()
        ]);

        // Ubah dari 30 jadi 2-3 project, masing-masing dengan 2-3 task
        Project::factory()
            ->count(3)             // Hanya 3 project
            ->hasTasks(3)          // Setiap project punya 3 task
            ->create();
    }
}