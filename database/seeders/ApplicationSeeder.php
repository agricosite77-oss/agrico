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
            $versions = [
                [
                    'application_name' => 'AgriCo Mobile App',
                    'version'          => 'v1.0.0',
                    'filename'         => 'agrico-app-v1.0.0.apk',
                    'file_size'        => '59.5 MB',
                    'release_notes'    => "• Initial official AgriCo mobile app release\n• Real-time soil scanning and GPS location mapping\n• Basic Munsell color identification",
                    'status'           => 'ARCHIVED',
                    'uploaded_by'      => 'Super Administrator',
                    'file_path'        => 'https://github.com/agricosite77-oss/agrico/releases/download/v1.0.0/agrico-app-v1.0.0.apk',
                    'created_at'       => '2026-10-11 07:31:00',
                    'updated_at'       => '2026-10-11 07:31:00',
                ],
                [
                    'application_name' => 'AgriCo Mobile App',
                    'version'          => 'v1.0.1',
                    'filename'         => 'agrico-app-v1.0.1.apk',
                    'file_size'        => '59.5 MB',
                    'release_notes'    => "• Soil scan stability improvements\n• GPS coordinate precision enhancements\n• Minor UI bug fixes and performance optimizations",
                    'status'           => 'ARCHIVED',
                    'uploaded_by'      => 'Super Administrator',
                    'file_path'        => 'https://github.com/agricosite77-oss/agrico/releases/download/v1.0.1/agrico-app-v1.0.1.apk',
                    'created_at'       => '2026-10-11 09:15:00',
                    'updated_at'       => '2026-10-11 09:15:00',
                ],
                [
                    'application_name' => 'AgriCo Mobile App',
                    'version'          => 'v1.0.2',
                    'filename'         => 'agrico-app-v1.0.2.apk',
                    'file_size'        => '59.5 MB',
                    'release_notes'    => "• Full on-device offline soil scanning (6-Stage ML)\n• Complete offline Munsell color chart with 207 colors\n• Offline scan history with auto-sync\n• Check for Updates button in Profile",
                    'status'           => 'ARCHIVED',
                    'uploaded_by'      => 'Super Administrator',
                    'file_path'        => 'https://github.com/agricosite77-oss/agrico/releases/download/v1.0.2/agrico-app-v1.0.2.apk',
                    'created_at'       => '2026-10-12 10:20:00',
                    'updated_at'       => '2026-10-12 10:20:00',
                ],
                [
                    'application_name' => 'AgriCo Mobile App',
                    'version'          => 'v1.0.3',
                    'filename'         => 'agrico-app-v1.0.3.apk',
                    'file_size'        => '59.5 MB',
                    'release_notes'    => "• Automatic 30-second background update checker\n• In-app update notification modal\n• Offline token caching and authentication persistence",
                    'status'           => 'ARCHIVED',
                    'uploaded_by'      => 'Super Administrator',
                    'file_path'        => 'https://github.com/agricosite77-oss/agrico/releases/download/v1.0.3/agrico-app-v1.0.3.apk',
                    'created_at'       => '2026-10-12 22:20:00',
                    'updated_at'       => '2026-10-12 22:20:00',
                ],
                [
                    'application_name' => 'AgriCo Mobile App',
                    'version'          => 'v1.0.4',
                    'filename'         => 'agrico-app-v1.0.4.apk',
                    'file_size'        => '59.5 MB',
                    'release_notes'    => "• Full on-device offline soil scanning (6-Stage ML determination)\n• Complete offline Munsell color chart with 207 colors\n• Enhanced camera preview with auto-zoom & bounding box alignment\n• Automatic background history synchronization when online\n• In-app version checker with instant one-tap update",
                    'status'           => 'ARCHIVED',
                    'uploaded_by'      => 'Super Administrator',
                    'file_path'        => 'https://github.com/agricosite77-oss/agrico/releases/download/v1.0.4/agrico-app-v1.0.4.apk',
                    'created_at'       => '2026-10-13 10:08:00',
                    'updated_at'       => '2026-10-13 10:08:00',
                ],
                [
                    'application_name' => 'AgriCo Mobile App',
                    'version'          => 'v1.0.5',
                    'filename'         => 'agrico-app-v1.0.5.apk',
                    'file_size'        => '59.5 MB',
                    'release_notes'    => "• Profile Section & Picture Sync: Full live synchronization between mobile profile and website database\n• Stage 1 to Stage 6 Soil Gate: Immediate non-soil rejection at Stage 1, plus server-side calibration sync\n• Website 1:1 Result Card & Modal: Matches website layout with overlapping swatch crop, 3-column metric grids, Scan Note, and Pin Location",
                    'status'           => 'ACTIVE',
                    'uploaded_by'      => 'Super Administrator',
                    'file_path'        => 'https://github.com/agricosite77-oss/agrico/releases/download/v1.0.5/agrico-app-v1.0.5.apk',
                    'created_at'       => '2026-10-18 14:30:00',
                    'updated_at'       => '2026-10-18 14:30:00',
                ],
            ];

            foreach ($versions as $v) {
                Application::create($v);
            }
        }
    }
}
