<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Admin\Application\UploadApplication;

class UploadApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_mount_upload_application_even_with_zero_applications(): void
    {
        Application::query()->delete();
        $this->assertEquals(0, Application::count());

        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'email' => 'agricosite77@gmail.com',
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(UploadApplication::class)
            ->assertStatus(200)
            ->assertSee('Upload Application');

        // Verify it seeded a default version without throwing 500
        $this->assertGreaterThan(0, Application::count());
    }

    public function test_superadmin_can_access_upload_application_screen_via_route(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'email' => 'admin@agrico.com',
        ]);

        $response = $this->actingAs($superAdmin)->get(route('application.upload'));

        $response->assertStatus(200);
        $response->assertSee('Upload Application');
    }

    public function test_regular_admin_without_superadmin_privileges_is_forbidden(): void
    {
        $admin = User::factory()->create([
            'user_id' => 'ADM-002',
            'role' => 'admin',
            'email' => 'staff@agrico.com',
        ]);

        $response = $this->actingAs($admin)->get(route('application.upload'));

        $response->assertStatus(403);
    }
}
