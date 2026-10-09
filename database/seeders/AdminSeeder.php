<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'agricosite77@gmail.com');
        $password = env('ADMIN_PASSWORD');

        if (!$password) {
            $this->command?->warn('ADMIN_PASSWORD environment variable is not set. Skipping AdminSeeder.');
            return;
        }

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'admin_id' => 'ADM-001',
                'full_name' => env('ADMIN_NAME', 'AgriCo Administrator'),
                'email' => $email,
                'password' => Hash::make($password),
            ]
        );
    }
}

