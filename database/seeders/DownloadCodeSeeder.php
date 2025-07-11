<?php

namespace Database\Seeders;

use App\Models\DownloadCode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DownloadCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DownloadCode::create([
            'code' => 'WEDDING2025',
            'name' => 'Wedding Download Access',
            'is_active' => true,
            'expires_at' => null, // Never expires
        ]);

        DownloadCode::create([
            'code' => 'GUEST123',
            'name' => 'Guest Access Code',
            'is_active' => true,
            'expires_at' => now()->addMonths(6), // Expires in 6 months
        ]);
    }
}
