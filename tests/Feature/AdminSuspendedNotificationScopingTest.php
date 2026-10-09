<?php

namespace Tests\Feature;

use App\Livewire\Admin\NotificationBell;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSuspendedNotificationScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_only_sees_notifications_for_their_own_students()
    {
        $admin1 = Admin::create([
            'admin_id'  => 'ADM-001',
            'full_name' => 'AgriCo Admin',
            'email'     => 'agrico@test.com',
            'password'  => bcrypt('password'),
        ]);

        $admin2 = Admin::create([
            'admin_id'  => 'ADM-002',
            'full_name' => 'Second Admin',
            'email'     => 'admin2@test.com',
            'password'  => bcrypt('password'),
        ]);

        // Student belonging to ADM-001 is suspended
        $student1 = User::create([
            'user_id'       => 'USR-001',
            'name'          => 'Student of Admin 1',
            'email'         => 'student1@test.com',
            'role'          => 'student',
            'student_id'    => '2024-00001',
            'status'        => 'Suspended',
            'last_admin_id' => 'ADM-001',
            'suspended_until' => now()->addDays(5),
            'password'      => bcrypt('password'),
        ]);

        // Student belonging to ADM-002 is active
        $student2 = User::create([
            'user_id'       => 'USR-002',
            'name'          => 'Student of Admin 2',
            'email'         => 'student2@test.com',
            'role'          => 'student',
            'student_id'    => '2024-00002',
            'status'        => 'Active',
            'last_admin_id' => 'ADM-002',
            'password'      => bcrypt('password'),
        ]);

        // When ADM-002 views notifications, they should NOT see student1's suspension
        Livewire::actingAs($admin2, 'admin')
            ->test(NotificationBell::class)
            ->assertViewHas('suspendedStudentsCount', 0)
            ->assertDontSee('Student Suspended: Student of Admin 1');

        // When ADM-001 views notifications, they SHOULD see student1's suspension
        Livewire::actingAs($admin1, 'admin')
            ->test(NotificationBell::class)
            ->assertViewHas('suspendedStudentsCount', 1)
            ->assertSee('Student Suspended: Student of Admin 1');
    }

    public function test_when_student_of_adm_002_is_suspended_adm_002_gets_notification()
    {
        $admin2 = Admin::create([
            'admin_id'  => 'ADM-002',
            'full_name' => 'Second Admin',
            'email'     => 'admin2@test.com',
            'password'  => bcrypt('password'),
        ]);

        $student2 = User::create([
            'user_id'       => 'USR-002',
            'name'          => 'Student of Admin 2',
            'email'         => 'student2@test.com',
            'role'          => 'student',
            'student_id'    => '2024-00002',
            'status'        => 'Suspended',
            'last_admin_id' => 'ADM-002',
            'suspended_until' => now()->addDays(3),
            'password'      => bcrypt('password'),
        ]);

        Livewire::actingAs($admin2, 'admin')
            ->test(NotificationBell::class)
            ->assertViewHas('suspendedStudentsCount', 1)
            ->assertSee('Student Suspended: Student of Admin 2');
    }
}
