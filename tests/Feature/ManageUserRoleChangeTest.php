<?php

namespace Tests\Feature;

use App\Livewire\Admin\User\ManageUser;
use App\Livewire\Admin\User\UserEditModal;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageUserRoleChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_change_user_role_to_student_and_back_to_user(): void
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-001',
            'full_name' => 'AgriCo Administrator',
            'email'     => 'agricosite77@gmail.com',
            'password'  => bcrypt('password'),
        ]);

        $user = User::create([
            'user_id'  => 'USR-001',
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'user',
            'status'   => 'Active',
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(ManageUser::class)
            ->call('openEditModal', $user->user_id)
            ->set('adminPassword', 'password')
            ->call('verifyAdminPassword')
            ->assertSet('isVerified', true)
            ->set('editRole', 'student')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertEquals('student', $user->fresh()->role);
        $this->assertNotEmpty($user->fresh()->student_id);

        // Now change back to user
        Livewire::actingAs($admin, 'admin')
            ->test(ManageUser::class)
            ->call('openEditModal', $user->user_id)
            ->set('adminPassword', 'password')
            ->call('verifyAdminPassword')
            ->set('editRole', 'user')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertEquals('user', $user->fresh()->role);
        $this->assertNull($user->fresh()->student_id);
    }

    public function test_superadmin_can_promote_user_to_admin_and_demote_to_user(): void
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-001',
            'full_name' => 'AgriCo Administrator',
            'email'     => 'agricosite77@gmail.com',
            'password'  => bcrypt('password'),
        ]);

        $user = User::create([
            'user_id'  => 'USR-002',
            'name'     => 'Jane Admin',
            'email'    => 'jane@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'user',
            'status'   => 'Active',
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(ManageUser::class)
            ->call('openEditModal', $user->user_id)
            ->set('adminPassword', 'password')
            ->call('verifyAdminPassword')
            ->set('editRole', 'admin')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertEquals('admin', $user->fresh()->role);
        $this->assertDatabaseHas('admins', [
            'email' => 'jane@example.com',
        ]);

        // Demote back to user
        Livewire::actingAs($admin, 'admin')
            ->test(ManageUser::class)
            ->call('openEditModal', $user->fresh()->user_id)
            ->set('adminPassword', 'password')
            ->call('verifyAdminPassword')
            ->set('editRole', 'user')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertEquals('user', $user->fresh()->role);
        $this->assertDatabaseMissing('admins', [
            'email' => 'jane@example.com',
        ]);
    }

    public function test_member_role_is_not_allowed_and_normalized_to_user(): void
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-001',
            'full_name' => 'AgriCo Administrator',
            'email'     => 'agricosite77@gmail.com',
            'password'  => bcrypt('password'),
        ]);

        $user = User::create([
            'user_id'  => 'USR-003',
            'name'     => 'Member Person',
            'email'    => 'member@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'user',
            'status'   => 'Active',
        ]);

        // Attempting to save role 'member' should fail validation in ManageUser
        Livewire::actingAs($admin, 'admin')
            ->test(ManageUser::class)
            ->call('openEditModal', $user->user_id)
            ->set('adminPassword', 'password')
            ->call('verifyAdminPassword')
            ->set('editRole', 'member')
            ->call('saveUser')
            ->assertHasErrors(['editRole']);

        // Test UserEditModal component as well
        Livewire::actingAs($admin, 'admin')
            ->test(UserEditModal::class)
            ->call('openModal', $user->user_id)
            ->set('adminPassword', 'password')
            ->call('verifyAdminPassword')
            ->set('role', 'student')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('student', $user->fresh()->role);
    }
}
