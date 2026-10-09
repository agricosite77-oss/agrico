<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->index()->after('status');
        });

        // One-time backfill matching current logic so existing data doesn't visually flip
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE users SET is_online = (MOD(CAST(RIGHT(user_id, 4) AS UNSIGNED), 2) = 1)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_online');
        });
    }
};
