<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('soil_scans', function (Blueprint $table) {
            if (!Schema::hasColumn('soil_scans', 'verification_status')) {
                $table->string('verification_status', 30)->default('Verified')->after('scan_note');
                $table->index('verification_status');
            }
            if (!Schema::hasColumn('soil_scans', 'moderated_by_admin_id')) {
                $table->string('moderated_by_admin_id', 50)->nullable()->after('verification_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soil_scans', function (Blueprint $table) {
            if (Schema::hasColumn('soil_scans', 'moderated_by_admin_id')) {
                $table->dropColumn('moderated_by_admin_id');
            }
            if (Schema::hasColumn('soil_scans', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
        });
    }
};
