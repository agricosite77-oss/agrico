<?php

use App\Models\User;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Hash;

test('suspended user login triggers suspension modal with details and prevents login', function () {
    $user = User::create([
        'name' => 'Kathlyn Kuhn',
        'email' => 'test_suspended@agrico.com',
        'password' => Hash::make('Secret123!'),
        'status' => 'Suspended',
        'suspend_reason' => 'Violation of academic terms',
        'suspended_by_admin_id' => 'ADM-001',
        'suspended_until' => now()->addDays(3),
    ]);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'test_suspended@agrico.com',
        'password' => 'Secret123!',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('suspended_user_data');

    $sessionData = session('suspended_user_data');
    expect($sessionData['email'])->toBe('test_suspended@agrico.com')
        ->and($sessionData['suspend_reason'])->toBe('Violation of academic terms')
        ->and($sessionData['has_pending_reactivation'])->toBeFalse();

    // Verify when the login view is reloaded with the session, the popup is rendered
    $followUp = $this->withSession(['suspended_user_data' => $sessionData])->get(route('login'));
    $followUp->assertOk();
    $followUp->assertSee('Suspension Details');
    $followUp->assertSee('Violation of academic terms');
    $followUp->assertSee('Request Reactivation');
});

test('suspended user can request reactivation from the popup', function () {
    $user = User::create([
        'name' => 'Wade Wilderman',
        'email' => 'test_reactivate@agrico.com',
        'password' => Hash::make('Secret123!'),
        'status' => 'Suspended',
        'suspend_reason' => 'Inappropriate behavior',
        'suspended_by_admin_id' => 'ADM-001',
    ]);

    $response = $this->from(route('login'))->post(route('account.request-reactivation'), [
        'email' => 'test_reactivate@agrico.com',
        'appeal_note' => 'I would like to appeal my suspension.',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('suspended_user_data');

    $this->assertDatabaseHas('user_requests', [
        'user_email' => 'test_reactivate@agrico.com',
        'type' => 'Account Reactivation Request',
        'status' => 'Pending',
    ]);

    $sessionData = session('suspended_user_data');
    expect($sessionData['has_pending_reactivation'])->toBeTrue()
        ->and($sessionData['request_just_submitted'])->toBeTrue();

    // Verify modal shows pending status
    $followUp = $this->withSession(['suspended_user_data' => $sessionData])->get(route('login'));
    $followUp->assertOk();
    $followUp->assertSee('Reactivation Request Submitted');
    $followUp->assertSee('Reactivation Pending');
});

test('mobile api login blocks suspended user and returns structured suspension data', function () {
    $user = User::create([
        'name' => 'Mobile Suspended User',
        'email' => 'mobile_suspended@agrico.com',
        'password' => Hash::make('Secret123!'),
        'status' => 'Suspended',
        'suspend_reason' => 'Mobile policy breach',
        'suspended_by_admin_id' => 'ADM-002',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'mobile_suspended@agrico.com',
        'password' => 'Secret123!',
    ]);

    $response->assertStatus(403);
    $response->assertJson([
        'status' => 'suspended',
        'message' => 'Your account has been suspended.',
        'suspension' => [
            'reason' => 'Mobile policy breach',
            'has_pending_reactivation' => false,
        ],
    ]);
});
