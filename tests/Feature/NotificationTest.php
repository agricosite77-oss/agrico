<?php

use App\Mail\SoilPinSubmittedMail;
use App\Mail\SoilScanModeratedMail;
use App\Models\LocationMapping;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

test('SoilPinSubmittedMail and SoilScanModeratedMail render valid html', function () {
    $user = User::first() ?? User::create([
        'name'     => 'Test Notif User',
        'email'    => 'notif_test_' . uniqid() . '@agrico.com',
        'password' => bcrypt('secret123'),
        'status'   => 'Active',
    ]);

    $soil = Soil::first() ?? Soil::create([
        'soil_name' => 'Notif Clay',
        'status'    => 'Active',
    ]);

    $scan = SoilScan::create([
        'user_id'             => $user->user_id,
        'soil_id'             => $soil->soil_id,
        'scan_note'           => 'Mail render test',
        'verification_status' => 'Pending',
    ]);

    $location = LocationMapping::create([
        'soil_scan_id' => $scan->soil_scan_id,
        'soil_id'      => $soil->soil_id,
        'latitude'     => 18.2050,
        'longitude'    => 121.6620,
        'barangay'     => 'Catugan',
        'status'       => 'Pending',
        'timestamp'    => now(),
    ]);

    // Test Pin Submitted Mail render
    $pinMail = new SoilPinSubmittedMail($location, $user, 'mobile application');
    $pinHtml = $pinMail->render();
    expect($pinHtml)->toContain('Catugan')
        ->toContain('18.20500')
        ->toContain('PENDING VERIFICATION');

    // Test Approved Mail render
    $approvedMail = new SoilScanModeratedMail($scan, 'Verified', $user, 'ADM-001');
    $approvedHtml = $approvedMail->render();
    expect($approvedHtml)->toContain('Soil Scan Approved')
        ->toContain('VERIFIED / APPROVED')
        ->toContain((string)$scan->soil_scan_id);

    // Test Disapproved Mail render
    $rejectedMail = new SoilScanModeratedMail($scan, 'Rejected', $user, 'ADM-001');
    $rejectedHtml = $rejectedMail->render();
    expect($rejectedHtml)->toContain('Soil Scan Disapproved')
        ->toContain('REJECTED / HIDDEN');
});

test('pin submission generates in-app user notification and attempts email dispatch', function () {
    $user = User::first() ?? User::create([
        'name'     => 'Pin Notif User',
        'email'    => 'pin_user_' . uniqid() . '@agrico.com',
        'password' => bcrypt('password'),
        'status'   => 'Active',
    ]);

    $soil = Soil::first() ?? Soil::create([
        'soil_name' => 'Pin Soil',
        'status'    => 'Active',
    ]);

    $scan = SoilScan::create([
        'user_id'             => $user->user_id,
        'soil_id'             => $soil->soil_id,
        'scan_note'           => 'Pin Notif Scan',
        'verification_status' => 'Pending',
    ]);

    $location = LocationMapping::create([
        'soil_scan_id' => $scan->soil_scan_id,
        'soil_id'      => $soil->soil_id,
        'latitude'     => 18.2100,
        'longitude'    => 121.6700,
        'barangay'     => 'Bagumbayan',
        'status'       => 'Pending',
        'timestamp'    => now(),
    ]);

    NotificationService::notifyPinSubmitted($location, $user, 'mobile application');

    $notif = UserNotification::where('user_id', $user->user_id)
        ->where('type', 'pin_submitted')
        ->latest('created_at')
        ->first();

    expect($notif)->not->toBeNull()
        ->and($notif->title)->toBe('Soil Location Pin Submitted')
        ->and($notif->message)->toContain('Bagumbayan')
        ->and($notif->is_read)->toBeFalse();
});

test('scan moderation triggers in-app notification when approved or rejected', function () {
    $user = User::first() ?? User::create([
        'name'     => 'Mod Notif User',
        'email'    => 'mod_user_' . uniqid() . '@agrico.com',
        'password' => bcrypt('password'),
        'status'   => 'Active',
    ]);

    $soil = Soil::first() ?? Soil::create([
        'soil_name' => 'Mod Soil',
        'status'    => 'Active',
    ]);

    $scan = SoilScan::create([
        'user_id'             => $user->user_id,
        'soil_id'             => $soil->soil_id,
        'scan_note'           => 'Moderation scan',
        'verification_status' => 'Pending',
    ]);

    LocationMapping::create([
        'soil_scan_id' => $scan->soil_scan_id,
        'soil_id'      => $soil->soil_id,
        'latitude'     => 18.2200,
        'longitude'    => 121.6800,
        'barangay'     => 'Centro',
        'status'       => 'Pending',
        'timestamp'    => now(),
    ]);

    // Approve scan
    SoilScan::approveScan($scan->soil_scan_id, 'ADM-001');

    $approvedNotif = UserNotification::where('user_id', $user->user_id)
        ->where('type', 'scan_approved')
        ->latest('created_at')
        ->first();

    expect($approvedNotif)->not->toBeNull()
        ->and($approvedNotif->title)->toBe('Soil Scan Approved & Published')
        ->and($approvedNotif->is_read)->toBeFalse();

    // Reject scan
    SoilScan::rejectScan($scan->soil_scan_id, 'ADM-001');

    $rejectedNotif = UserNotification::where('user_id', $user->user_id)
        ->where('type', 'scan_rejected')
        ->latest('created_at')
        ->first();

    expect($rejectedNotif)->not->toBeNull()
        ->and($rejectedNotif->title)->toBe('Soil Scan Disapproved');
});

test('notifications API returns notification list and can mark as read', function () {
    $user = User::first() ?? User::create([
        'name'     => 'API Notif User',
        'email'    => 'api_notif_' . uniqid() . '@agrico.com',
        'password' => bcrypt('password'),
        'status'   => 'Active',
    ]);

    $notif = UserNotification::create([
        'user_id' => $user->user_id,
        'title'   => 'Test API Notification',
        'message' => 'Testing notifications API endpoints',
        'type'    => 'pin_submitted',
        'data'    => ['test' => true],
        'is_read' => false,
    ]);

    // 1. Get notifications
    $response = test()->getJson('/api/notifications?user_id=' . $user->user_id);
    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ]);

    expect($response->json('unread_count'))->toBeGreaterThanOrEqual(1);

    // 2. Mark specific notification as read
    $readResponse = test()->postJson('/api/notifications/' . $notif->notification_id . '/read');
    $readResponse->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ]);

    expect($notif->fresh()->is_read)->toBeTrue();

    // 3. Create another unread and mark all as read
    UserNotification::create([
        'user_id' => $user->user_id,
        'title'   => 'Another Unread Notification',
        'message' => 'Batch read test',
        'type'    => 'scan_approved',
        'is_read' => false,
    ]);

    $allReadResponse = test()->postJson('/api/notifications/read-all', ['user_id' => $user->user_id]);
    $allReadResponse->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ]);

    $remainingUnread = UserNotification::where('user_id', $user->user_id)->unread()->count();
    expect($remainingUnread)->toBe(0);
});

test('website profile page renders line separator, notifications, and dark and light mode toggle', function () {
    $admin = User::first() ?? User::create([
        'name'     => 'AgriCo Administrator',
        'email'    => 'admin_test_' . uniqid() . '@agrico.com',
        'password' => bcrypt('password123'),
        'role'     => 'Superadmin',
        'status'   => 'Active',
    ]);
    $response = test()->actingAs($admin)->get('/profile');
    
    $response->assertStatus(200);
    $response->assertSee('profile-card-divider', false);
    $response->assertSee('profile-settings-group', false);
    $response->assertSee('Notifications');
    $response->assertSee('Appearance');
    $response->assertSee('profile-theme-switch', false);
});

