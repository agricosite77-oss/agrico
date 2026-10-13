<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Application::count() === 0) {
            Application::create([
                'application_name' => 'AgriCo Mobile App',
                'version'          => 'v1.0.4',
                'filename'         => 'agrico-app-v1.0.4.apk',
                'file_size'        => '59.5 MB',
                'release_notes'    => "• Full on-device offline soil scanning (6-Stage ML determination)\n• Complete offline Munsell color chart with 207 colors\n• Enhanced camera preview with auto-zoom & bounding box alignment\n• Persistent offline scan history with auto-sync\n• In-app version checker with instant one-tap update",
                'status'           => 'ACTIVE',
                'uploaded_by'      => 'Super Administrator',
                'file_path'        => 'https://github.com/agricosite77-oss/agrico-releases/releases/download/v1.0.4/agrico-app-v1.0.4.apk',
            ]);
        }
    }
}
