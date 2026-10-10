<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereRaw('LOWER(email) IN (?, ?)', ['agricosite77@gmail.com', 'admin@agrico.com'])
            ->update(['role' => 'superadmin']);
    }

    public function down(): void
    {
        // No down migration needed
    }
};
