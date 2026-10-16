<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\LocationMapping;
use App\Models\MunsellColor;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use App\Models\UserRequest;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminRecordSeeder extends Seeder
{
    /**
     * Run all admin sample record seeders if tables are empty.
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedStudents();
        $this->seedSoilScans();
        $this->seedLocationMappings();
        $this->seedUserRequests();
    }

    /**
     * Seed realistic Soil Scan records if none exist.
     */
    public function seedSoilScans(): void
    {
        if (SoilScan::count() > 0) {
            return;
        }

        $admin = Admin::first();
        $adminId = $admin?->admin_id ?? 'ADM-001';
        $user = User::where('role', 'user')->first() ?? User::first();
        $userId = $user?->user_id ?? $adminId;

        $sampleScans = [
            [
                'soil_name'   => 'Dark Reddish Brown',
                'full_code'   => '5YR 3/4',
                'hue'         => '5YR',
                'note'        => 'Alluvial clay loam sample collected along the Cagayan River basin. High organic matter content. [Covered Area: 500 m²]',
                'status'      => 'Verified',
                'days_ago'    => 2,
            ],
            [
                'soil_name'   => 'Pale Brown',
                'full_code'   => '2.5Y 8/4',
                'hue'         => '2.5Y',
                'note'        => 'Sandy loam topsoil from corn crop rotation field trial. Well drained with moderate moisture. [Covered Area: 1,000 m²]',
                'status'      => 'Verified',
                'days_ago'    => 4,
            ],
            [
                'soil_name'   => 'Dark Grayish Brown',
                'full_code'   => '10YR 4/2',
                'hue'         => '10YR',
                'note'        => 'Lowland paddy soil collected during pre-planting soil conditioning. High moisture retention. [Covered Area: 2,500 m²]',
                'status'      => 'Verified',
                'days_ago'    => 6,
            ],
            [
                'soil_name'   => 'Reddish Yellow',
                'full_code'   => '7.5YR 8/6',
                'hue'         => '7.5YR',
                'note'        => 'Upland citrus grove soil sample with moderate gravel content. Tested for nitrogen replenishment. [Covered Area: 500 m²]',
                'status'      => 'Pending',
                'days_ago'    => 1,
            ],
            [
                'soil_name'   => 'Reddish Brown',
                'full_code'   => '5YR 4/4',
                'hue'         => '5YR',
                'note'        => 'Terraced vegetable farm topsoil with active compost integration. Neutral pH reading. [Covered Area: 1,000 m²]',
                'status'      => 'Verified',
                'days_ago'    => 8,
            ],
            [
                'soil_name'   => 'Very Pale Brown',
                'full_code'   => '10YR 8/4',
                'hue'         => '10YR',
                'note'        => 'Dry upland soil sample collected near foothill boundary. Low organic matter, high aeration. [Covered Area: 500 m²]',
                'status'      => 'Pending',
                'days_ago'    => 3,
            ],
            [
                'soil_name'   => 'Light Olive Brown',
                'full_code'   => '2.5Y 5/3',
                'hue'         => '2.5Y',
                'note'        => 'Riparian silt loam sample with fine grain structure and good microbial activity. [Covered Area: 2,500 m²]',
                'status'      => 'Verified',
                'days_ago'    => 10,
            ],
            [
                'soil_name'   => 'Strong Brown',
                'full_code'   => '7.5YR 5/6',
                'hue'         => '7.5YR',
                'note'        => 'Orchard boundary soil sample evaluated for seasonal soil erosion and nutrient runoff. [Covered Area: 500 m²]',
                'status'      => 'Verified',
                'days_ago'    => 12,
            ],
        ];

        foreach ($sampleScans as $data) {
            $mColor = MunsellColor::where('full_code', $data['full_code'])->first();
            $soil = Soil::firstOrCreate(
                ['full_code' => $data['full_code']],
                [
                    'soil_name'   => $data['soil_name'],
                    'hue'         => $data['hue'],
                    'full_code'   => $data['full_code'],
                    'description' => $data['note'],
                    'status'      => 'Active',
                    'scan_count'  => 1,
                    'color_id'    => $mColor?->munsell_color_id,
                    'admin_id'    => $adminId,
                ]
            );

            $scanDate = Carbon::now()->subDays($data['days_ago'])->setTime(rand(8, 17), rand(10, 55));

            SoilScan::create([
                'user_id'             => $userId,
                'soil_id'             => $soil->soil_id,
                'scan_note'           => $data['note'],
                'scan_date'           => $scanDate,
                'platform'            => 'web',
                'verification_status' => $data['status'],
                'moderated_by_admin_id' => $data['status'] === 'Verified' ? $adminId : null,
                'created_at'          => $scanDate,
                'updated_at'          => $scanDate,
            ]);

            $soil->increment('scan_count');
        }
    }

    /**
     * Seed realistic Location Mapping pins across Lal-lo if none exist.
     */
    public function seedLocationMappings(): void
    {
        if (LocationMapping::count() >= 10) {
            return;
        }

        $lalloPinLocations = [
            ['barangay' => 'Catayauan',          'lat' => 18.2155, 'lng' => 121.6789, 'coverage' => '500 m²',   'code' => '5YR 3/4'],
            ['barangay' => 'Bagumbayan',         'lat' => 18.2102, 'lng' => 121.6587, 'coverage' => '1,000 m²', 'code' => '2.5Y 8/4'],
            ['barangay' => 'Centro (Poblacion)', 'lat' => 18.2048, 'lng' => 121.6625, 'coverage' => '500 m²',   'code' => '10YR 4/2'],
            ['barangay' => 'Magapit',            'lat' => 18.1972, 'lng' => 121.6612, 'coverage' => '2,500 m²', 'code' => '7.5YR 8/6'],
            ['barangay' => 'Logac',              'lat' => 18.2256, 'lng' => 121.6498, 'coverage' => '1,000 m²', 'code' => '5YR 4/4'],
            ['barangay' => 'San Lorenzo',        'lat' => 18.2312, 'lng' => 121.6578, 'coverage' => '500 m²',   'code' => '10YR 8/4'],
            ['barangay' => 'Bangag',             'lat' => 18.2284, 'lng' => 121.6701, 'coverage' => '2,500 m²', 'code' => '2.5Y 5/3'],
            ['barangay' => 'Alaguia',            'lat' => 18.1520, 'lng' => 121.6720, 'coverage' => '500 m²',   'code' => '7.5YR 5/6'],
            ['barangay' => 'Santa Maria',        'lat' => 18.2234, 'lng' => 121.6687, 'coverage' => '1,000 m²', 'code' => '5YR 3/4'],
            ['barangay' => 'Tucalana',           'lat' => 18.2178, 'lng' => 121.6654, 'coverage' => '500 m²',   'code' => '10YR 4/2'],
        ];

        foreach ($lalloPinLocations as $idx => $loc) {
            if (LocationMapping::where('barangay', $loc['barangay'])->exists()) {
                continue;
            }

            $soil = Soil::where('full_code', $loc['code'])->first() ?? Soil::first();
            $admin = Admin::first();
            $user = User::where('role', 'user')->first() ?? User::first();
            $scanDate = Carbon::now()->subDays($idx * 2 + 1);

            $scan = SoilScan::create([
                'user_id'               => $user?->user_id ?? ($admin?->admin_id ?? 'ADM-001'),
                'soil_id'               => $soil?->soil_id ?? 1,
                'scan_note'             => "Soil sample collected in Brgy. {$loc['barangay']}, Lal-lo. [Covered Area: {$loc['coverage']}]",
                'scan_date'             => $scanDate,
                'platform'              => 'web',
                'verification_status'   => 'Verified',
                'moderated_by_admin_id' => $admin?->admin_id ?? 'ADM-001',
                'created_at'            => $scanDate,
                'updated_at'            => $scanDate,
            ]);

            LocationMapping::create([
                'soil_scan_id'      => $scan->soil_scan_id,
                'soil_id'           => $soil?->soil_id ?? 1,
                'latitude'          => $loc['lat'],
                'longitude'         => $loc['lng'],
                'barangay'          => $loc['barangay'],
                'timestamp'         => $scanDate,
                'status'            => 'Active',
                'pinned_soil_index' => 0,
                'soil_percentage'   => '100%',
                'soil_confidence'   => rand(94, 99) . '.' . rand(0, 9),
            ]);
        }
    }

    /**
     * Seed realistic Student records if none exist.
     */
    public function seedStudents(): void
    {
        if (User::where('role', 'student')->count() > 0) {
            return;
        }

        $admin = Admin::first();
        $adminId = $admin?->admin_id ?? 'ADM-001';

        $students = [
            [
                'name'       => 'Maria Santos Reyes',
                'email'      => 'reyes.maria@csu.edu.ph',
                'student_id' => '2024-0012',
                'section'    => 'BS Agriculture 3-A',
            ],
            [
                'name'       => 'Juan Carlos Dizon',
                'email'      => 'dizon.juancarlos@csu.edu.ph',
                'student_id' => '2024-0025',
                'section'    => 'BS Agriculture 3-A',
            ],
            [
                'name'       => 'Princess Mae Pascual',
                'email'      => 'pascual.princess@csu.edu.ph',
                'student_id' => '2024-0038',
                'section'    => 'BS Agriculture 3-B',
            ],
            [
                'name'       => 'Christian Paul Ramos',
                'email'      => 'ramos.christian@csu.edu.ph',
                'student_id' => '2024-0051',
                'section'    => 'BS Agriculture 3-B',
            ],
            [
                'name'       => 'Angela Nicole Aquino',
                'email'      => 'aquino.angela@csu.edu.ph',
                'student_id' => '2023-0084',
                'section'    => 'BS Agriculture 4-A',
            ],
        ];

        foreach ($students as $idx => $st) {
            $userId = 'STU-' . str_pad((string)($idx + 1), 3, '0', STR_PAD_LEFT);
            if (User::where('user_id', $userId)->exists()) {
                $userId = 'STU-' . strtoupper(Str::random(4));
            }

            User::create([
                'user_id'               => $userId,
                'name'                  => $st['name'],
                'email'                 => $st['email'],
                'password'              => Hash::make('password123'),
                'role'                  => 'student',
                'status'                => 'Active',
                'student_id'            => $st['student_id'],
                'section'               => $st['section'],
                'student_record_status' => 'Enrolled',
                'last_admin_id'         => $adminId,
            ]);
        }
    }

    /**
     * Seed realistic regular Users if none exist.
     */
    public function seedUsers(): void
    {
        if (User::where('role', 'user')->count() > 0) {
            return;
        }

        $admin = Admin::first();
        $adminId = $admin?->admin_id ?? 'ADM-001';

        $users = [
            [
                'name'  => 'Abby Joy Bautista',
                'email' => 'bautistaabbyjoy@gmail.com',
            ],
            [
                'name'  => 'Angelo Dela Cruz',
                'email' => 'delacruzang7@gmail.com',
            ],
            [
                'name'  => 'Patrisha Pablo',
                'email' => 'pablopatrisha6@gmail.com',
            ],
        ];

        foreach ($users as $idx => $u) {
            $userId = 'USR-' . str_pad((string)($idx + 1), 3, '0', STR_PAD_LEFT);
            if (User::where('user_id', $userId)->exists()) {
                $userId = 'USR-' . strtoupper(Str::random(4));
            }

            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'user_id'           => $userId,
                    'name'              => $u['name'],
                    'email'             => $u['email'],
                    'password'          => Hash::make('password123'),
                    'role'              => 'user',
                    'status'            => 'Active',
                    'last_admin_id'     => $adminId,
                    'email_verified_at' => now(),
                ]
            );
        }
    }

    /**
     * Seed realistic User Requests if none exist.
     */
    public function seedUserRequests(): void
    {
        if (UserRequest::count() > 0) {
            return;
        }

        $admin = Admin::first();
        $adminId = $admin?->admin_id ?? 'ADM-001';

        $sampleRequests = [
            [
                'request_code' => 'REQ-2026-101',
                'user_name'    => 'Maricar Santos',
                'user_email'   => 'maricar.santos@gmail.com',
                'type'         => 'Account Access Request',
                'description'  => 'New user account registration requested by Maricar Santos (maricar.santos@gmail.com).',
                'status'       => 'Pending',
                'days_ago'     => 1,
            ],
            [
                'request_code' => 'REQ-2026-102',
                'user_name'    => 'Rico Fernandez',
                'user_email'   => 'rico.fernandez@csu.edu.ph',
                'type'         => 'Account Access Request',
                'description'  => 'Agricultural researcher account access request for Lal-lo field soil mapping data.',
                'status'       => 'Approved',
                'days_ago'     => 3,
            ],
            [
                'request_code' => 'REQ-2026-103',
                'user_name'    => 'Jocelyn Valenzuela',
                'user_email'   => 'jocelyn.valenzuela@gmail.com',
                'type'         => 'Account Access Request',
                'description'  => 'Barangay agricultural coordinator registration request.',
                'status'       => 'Pending',
                'days_ago'     => 2,
            ],
        ];

        foreach ($sampleRequests as $req) {
            $submittedAt = Carbon::now()->subDays($req['days_ago'])->setTime(rand(9, 16), rand(10, 50));
            UserRequest::create([
                'request_code' => $req['request_code'],
                'user_name'    => $req['user_name'],
                'user_email'   => $req['user_email'],
                'type'         => $req['type'],
                'description'  => $req['description'],
                'status'       => $req['status'],
                'admin_id'     => $req['status'] === 'Approved' ? $adminId : null,
                'submitted_at' => $submittedAt,
                'created_at'   => $submittedAt,
                'updated_at'   => $submittedAt,
            ]);
        }
    }
}
