<?php

namespace Database\Seeders;

use App\Models\DownloadCode;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            "name" => "Test User",
            "email" => "test@example.com",
        ]);

        DownloadCode::create([
            "code" => "WEDDING2025",
            "name" => "Wedding Download Access",
            "is_active" => true,
            "expires_at" => null, // Never expires
        ]);
    }
}
