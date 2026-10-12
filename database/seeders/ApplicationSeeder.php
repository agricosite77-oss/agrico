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
                'version' => 'v1.0.0',
                'filename' => 'agrico-app-v1.0.0.apk',
                'file_size' => '59.5 MB',
                'release_notes' => 'Official AgriCo mobile app release with soil scanning and GPS location mapping.',
                'status' => 'ACTIVE',
                'uploaded_by' => 'Super Administrator',
                'file_path' => 'https://github.com/agricosite77-oss/agrico-releases/releases/download/v1.0.0/agrico-app-v1.0.0.apk',
            ]);
        }
    }
}
