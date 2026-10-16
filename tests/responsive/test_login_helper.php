<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'admin')->first();
$admin = App\Models\Admin::first();

echo "User Admin: " . ($user ? $user->email : 'none') . "\n";
echo "Admin Model: " . ($admin ? $admin->email : 'none') . "\n";
