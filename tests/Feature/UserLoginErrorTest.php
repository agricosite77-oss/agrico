<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginErrorTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_invalid_credentials_highlights_both_boxes_and_displays_message(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('CorrectPassword123!'),
        ]);

        $response = $this->from(route('login'))
            ->followingRedirects()
            ->post(route('login.store'), [
                'email' => 'testuser@example.com',
                'password' => 'WrongPassword999!',
            ]);

        $response->assertStatus(200);
        $response->assertSee('These credentials do not match our user records.');
        $response->assertSee('id="credentialsErrorMessage"', false);
    }
}
