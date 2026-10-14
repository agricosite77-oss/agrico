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
        if (Schema::hasTable('user_notifications') && !Schema::hasColumn('user_notifications', 'platform')) {
            Schema::table('user_notifications', function (Blueprint $table) {
                $table->string('platform', 30)->default('website')->index()->after('type');
            });

            // Backfill existing records cleanly based on domain type
            DB::table('user_notifications')
                ->whereIn('type', ['scan_approved', 'scan_rejected', 'pin_submitted'])
                ->update(['platform' => 'mobile']);

            DB::table('user_notifications')
                ->whereIn('type', ['account_approved', 'account_disapproved'])
                ->update(['platform' => 'website']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_notifications') && Schema::hasColumn('user_notifications', 'platform')) {
            Schema::table('user_notifications', function (Blueprint $table) {
                $table->dropColumn('platform');
            });
        }
    }
};
