<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_send_reset_code_to_unregistered_email(): void
    {
        $response = $this->postJson('/api/forgot-password/send-code', [
            'email' => 'nonexistent_user_98765@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'This email address is not registered in our records.',
            ]);
    }

    public function test_can_send_reset_code_to_registered_user(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test_pwd_reset@example.com'],
            [
                'name' => 'Test Reset User',
                'password' => Hash::make('OldPassword123!'),
                'role' => 'user',
            ]
        );

        $response = $this->postJson('/api/forgot-password/send-code', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_can_verify_valid_code(): void
    {
        $email = 'test_pwd_reset@example.com';
        $code = '123456';

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $code,
                'created_at' => now(),
            ]
        );

        // Invalid code check
        $invalidResponse = $this->postJson('/api/forgot-password/verify-code', [
            'email' => $email,
            'code' => '654321',
        ]);
        $invalidResponse->assertStatus(422);

        // Valid code check
        $validResponse = $this->postJson('/api/forgot-password/verify-code', [
            'email' => $email,
            'code' => $code,
        ]);
        $validResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'code' => $code,
            ]);
    }

    public function test_can_reset_password_with_valid_code(): void
    {
        $email = 'test_pwd_reset@example.com';
        $code = '888999';

        $user = User::create([
            'email' => $email,
            'name' => 'Reset Target User',
            'password' => Hash::make('OldPassword123!'),
            'role' => 'user',
        ]);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $code,
                'created_at' => now(),
            ]
        );

        $response = $this->postJson('/api/forgot-password/reset', [
            'email' => $email,
            'code' => $code,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        // Token should be removed
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $email,
        ]);

        // User password should be updated
        $user = User::where('email', $email)->first();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->password));

        // Clean up
        $user->delete();
    }
}
