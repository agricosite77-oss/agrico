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
            if (!Schema::hasColumn('soil_scans', 'before_after_image')) {
                $table->string('before_after_image', 1024)->nullable()->after('scan_image');
            }
            if (!Schema::hasColumn('soil_scans', 'thumbnail_image')) {
                $table->string('thumbnail_image', 1024)->nullable()->after('before_after_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soil_scans', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('soil_scans', 'before_after_image')) {
                $cols[] = 'before_after_image';
            }
            if (Schema::hasColumn('soil_scans', 'thumbnail_image')) {
                $cols[] = 'thumbnail_image';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
