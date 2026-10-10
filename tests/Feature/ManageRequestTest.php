<?php

use App\Models\User;
use App\Models\Admin;

test('admin can visit user requests page', function () {
    $user = User::where('email', 'agricosite77@gmail.com')->first();
    if (!$user) {
        $user = User::create([
            'user_id' => 'ADM-001',
            'name' => 'AgriCo Administrator',
            'email' => 'agricosite77@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'status' => 'Active',
        ]);
    }
    $this->actingAs($user);

    $response = $this->get('/user-requests');
    $response->assertOk();
});

test('admin guard can visit user requests page', function () {
    $admin = Admin::where('email', 'agricosite77@gmail.com')->first();
    if (!$admin) {
        $admin = Admin::create([
            'admin_id' => 'ADM-001',
            'full_name' => 'AgriCo Administrator',
            'email' => 'agricosite77@gmail.com',
            'password' => bcrypt('password'),
        ]);
    }
    $this->actingAs($admin, 'admin');

    $response = $this->get('/user-requests');
    $response->assertOk();
});
