<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('location_mappings', function (Blueprint $table) {
            $table->integer('pinned_soil_index')->nullable()->after('soil_id');
            $table->string('soil_percentage', 20)->nullable()->after('pinned_soil_index');
            $table->string('soil_confidence', 20)->nullable()->after('soil_percentage');
            $table->jsonb('soil_layer_meta')->nullable()->after('soil_confidence');
        });

        // Partial unique index so a specific detected soil layer cannot be pinned twice for the same scan
        // Legacy pins with pinned_soil_index IS NULL are excluded and untouched
        DB::statement('
            CREATE UNIQUE INDEX IF NOT EXISTS uq_loc_map_scan_soil_idx 
            ON location_mappings (soil_scan_id, pinned_soil_index) 
            WHERE pinned_soil_index IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS uq_loc_map_scan_soil_idx');

        Schema::table('location_mappings', function (Blueprint $table) {
            $table->dropColumn([
                'pinned_soil_index',
                'soil_percentage',
                'soil_confidence',
                'soil_layer_meta',
            ]);
        });
    }
};
