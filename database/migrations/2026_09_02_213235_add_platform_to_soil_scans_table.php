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
            if (!Schema::hasColumn('soil_scans', 'platform')) {
                $table->string('platform', 32)->nullable()->default('website')->after('scan_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soil_scans', function (Blueprint $table) {
            if (Schema::hasColumn('soil_scans', 'platform')) {
                $table->dropColumn('platform');
            }
        });
    }
};
