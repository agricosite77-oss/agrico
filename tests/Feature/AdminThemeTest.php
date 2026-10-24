<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_update_theme_preference(): void
    {
        $admin = Admin::create([
            'admin_id' => 'ADM-999',
            'full_name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'theme' => 'light',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('user.update-theme'), [
                'theme' => 'dark',
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'theme' => 'dark',
            ]);

        $this->assertDatabaseHas('admins', [
            'admin_id' => 'ADM-999',
            'theme' => 'dark',
        ]);
    }

    public function test_admin_dashboard_renders_theme_toggle(): void
    {
        $admin = Admin::create([
            'admin_id' => 'ADM-001',
            'full_name' => 'Carl Ashley',
            'email' => 'agricosite77@gmail.com',
            'password' => bcrypt('password123'),
            'theme' => 'dark',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('data-theme', false);
        $response->assertSee('sidebar-bottom-profile-card', false);
    }
}
