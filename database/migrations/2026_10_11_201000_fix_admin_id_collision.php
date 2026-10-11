<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fixes Admin ID collision on Railway production so only the genuine Super Admin
     * retains ADM-001, and promoted admins (such as jordan@gmail.com) receive unique IDs (ADM-002+).
     */
    public function up(): void
    {
        $isRailway = !empty(env('RAILWAY_ENVIRONMENT'))
            || !empty(env('RAILWAY_SERVICE_NAME'))
            || !empty(env('RAILWAY_PUBLIC_DOMAIN'))
            || !empty(env('RAILWAY_PROJECT_ID'))
            || !empty(env('RAILWAY_ENVIRONMENT_NAME'));

        $isProduction = app()->isProduction()
            || config('app.env') === 'production'
            || str_contains((string) config('app.url'), 'railway.app');

        // Strictly respect user instruction: "don't put that in the local only put that in my https://agrico-production-1eb3.up.railway.app/"
        if (!$isRailway && !$isProduction && app()->isLocal() && !env('FORCE_MUNSELL_PROD')) {
            return;
        }

        if (!Schema::hasTable('users') || !Schema::hasTable('admins')) {
            return;
        }

        $superAdminEmails = ['agricosite77@gmail.com', 'admin@agrico.com'];

        // 1. Ensure Super Admin exists in admins table with ADM-001
        $superUser = DB::table('users')
            ->whereIn(DB::raw('LOWER(email)'), $superAdminEmails)
            ->first();

        $superEmail = $superUser?->email ?? 'agricosite77@gmail.com';
        $superName = $superUser?->name ?? 'AgriCo Administrator';
        $superPassword = $superUser?->password ?? '';

        $existingSuperAdmin = DB::table('admins')
            ->whereIn(DB::raw('LOWER(email)'), $superAdminEmails)
            ->first();

        if ($existingSuperAdmin) {
            DB::table('admins')
                ->where('admin_id', $existingSuperAdmin->admin_id)
                ->update(['admin_id' => 'ADM-001']);
        } else {
            // Check if ADM-001 is taken by someone else in admins table
            $occupant = DB::table('admins')->where('admin_id', 'ADM-001')->first();
            if ($occupant && !in_array(strtolower(trim($occupant->email)), $superAdminEmails)) {
                // Relocate occupant to temporary placeholder
                DB::table('admins')
                    ->where('admin_id', 'ADM-001')
                    ->update(['admin_id' => 'ADM-TEMP']);
            }

            if (!empty($superPassword)) {
                DB::table('admins')->insert([
                    'admin_id' => 'ADM-001',
                    'full_name' => $superName,
                    'email' => $superEmail,
                    'password' => $superPassword,
                ]);
            }
        }

        // 2. Ensure Super Admin role is 'superadmin' in users table
        DB::table('users')
            ->whereIn(DB::raw('LOWER(email)'), $superAdminEmails)
            ->update(['role' => 'superadmin']);

        // 3. Resolve any non-superadmin colliding with ADM-001 in admins table or users table
        $adminRecords = DB::table('admins')->get();
        $assignedIds = ['ADM-001'];
        $nextNum = 2;

        foreach ($adminRecords as $admin) {
            $emailLower = strtolower(trim($admin->email));
            if (in_array($emailLower, $superAdminEmails)) {
                continue;
            }

            // If ID is ADM-001, ADM-TEMP, or empty/invalid, reassign
            if ($admin->admin_id === 'ADM-001' || $admin->admin_id === 'ADM-TEMP' || empty($admin->admin_id) || in_array($admin->admin_id, $assignedIds)) {
                while (in_array('ADM-' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT), $assignedIds)) {
                    $nextNum++;
                }
                $newAdminId = 'ADM-' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
                $nextNum++;

                DB::table('admins')
                    ->where('email', $admin->email)
                    ->update(['admin_id' => $newAdminId]);

                $assignedIds[] = $newAdminId;
            } else {
                $assignedIds[] = $admin->admin_id;
            }
        }

        // 4. Specifically ensure Kent Jordan Alberto (jordan@gmail.com) has a clean, unique Admin record
        $jordanUser = DB::table('users')->where('email', 'jordan@gmail.com')->first();
        if ($jordanUser) {
            $jordanAdmin = DB::table('admins')->where('email', 'jordan@gmail.com')->first();
            if (!$jordanAdmin) {
                while (in_array('ADM-' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT), $assignedIds)) {
                    $nextNum++;
                }
                $jordanId = 'ADM-' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
                $nextNum++;

                DB::table('admins')->insert([
                    'admin_id' => $jordanId,
                    'full_name' => $jordanUser->name,
                    'email' => $jordanUser->email,
                    'password' => $jordanUser->password,
                ]);
            } elseif ($jordanAdmin->admin_id === 'ADM-001' || $jordanAdmin->admin_id === 'ADM-TEMP') {
                while (in_array('ADM-' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT), $assignedIds)) {
                    $nextNum++;
                }
                $jordanId = 'ADM-' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
                $nextNum++;

                DB::table('admins')
                    ->where('email', 'jordan@gmail.com')
                    ->update(['admin_id' => $jordanId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive
    }
};
