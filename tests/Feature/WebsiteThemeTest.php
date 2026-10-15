<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteThemeTest extends TestCase
{
    use RefreshDatabase;
    public function test_authenticated_user_can_update_theme_preference(): void
    {
        $user = User::factory()->create([
            'status' => 'Active',
            'theme'  => 'light',
        ]);

        $response = $this->actingAs($user, 'web')
            ->postJson(route('user.update-theme'), [
                'theme' => 'dark',
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'theme'  => 'dark',
            ]);

        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'theme'   => 'dark',
        ]);
    }

    public function test_theme_update_validates_input(): void
    {
        $user = User::factory()->create([
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user, 'web')
            ->postJson(route('user.update-theme'), [
                'theme' => 'invalid-theme',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['theme']);
    }

    public function test_guest_can_post_theme_without_error(): void
    {
        $response = $this->postJson(route('user.update-theme'), [
            'theme' => 'dark',
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'guest',
                'theme'  => 'dark',
            ]);
    }

    public function test_profile_page_contains_accessible_theme_switch(): void
    {
        $user = User::factory()->create([
            'status' => 'Active',
            'theme'  => 'dark',
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('website.profile'));

        $response->assertOk();
        $response->assertSee('role="switch"', false);
        $response->assertSee('aria-label="Toggle dark and light appearance mode"', false);
        $response->assertSee('toggleTheme()', false);
        $response->assertSee('profile-theme-switch', false);
    }
}
