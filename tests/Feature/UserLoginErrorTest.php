<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserLoginErrorTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        RateLimiter::clear(Str::transliterate('testuser@example.com|127.0.0.1'));
        RateLimiter::clear(Str::transliterate('lockeduser@example.com|127.0.0.1'));
        parent::tearDown();
    }

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

    public function test_five_failed_login_attempts_triggers_15_minute_lockout_notification(): void
    {
        $user = User::factory()->create([
            'email' => 'lockeduser@example.com',
            'password' => bcrypt('CorrectPassword123!'),
        ]);

        // First 4 failed attempts
        for ($i = 1; $i <= 4; $i++) {
            $resp = $this->from(route('login'))
                ->post(route('login.store'), [
                    'email' => 'lockeduser@example.com',
                    'password' => 'WrongPassword!',
                ]);

            $resp->assertSessionHasErrors(['email' => 'These credentials do not match our user records.']);
        }

        // 5th failed attempt: reaches maximum attempts, locks out for 15 minutes
        $fifthResp = $this->from(route('login'))
            ->followingRedirects()
            ->post(route('login.store'), [
                'email' => 'lockeduser@example.com',
                'password' => 'WrongPassword!',
            ]);

        $fifthResp->assertStatus(200);
        $fifthResp->assertSee('Too many failed login attempts. Please wait 15 minutes to log in again with your credentials.');
        $fifthResp->assertSee('id="credentialsErrorMessage"', false);

        // 6th attempt while locked out: blocked immediately by RateLimiter
        $sixthResp = $this->from(route('login'))
            ->followingRedirects()
            ->post(route('login.store'), [
                'email' => 'lockeduser@example.com',
                'password' => 'CorrectPassword123!', // Even with correct password, locked out!
            ]);

        $sixthResp->assertStatus(200);
        $sixthResp->assertSee('Too many failed login attempts.');
        $sixthResp->assertSee('to log in again with your credentials.');
        $this->assertGuest();
    }
}
