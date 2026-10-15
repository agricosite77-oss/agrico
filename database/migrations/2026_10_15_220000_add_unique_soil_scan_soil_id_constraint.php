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
        // Unique index on (soil_scan_id, soil_id) ensuring a soil cannot be pinned twice for the same scan
        DB::statement('
            CREATE UNIQUE INDEX IF NOT EXISTS uq_loc_map_scan_soil_id 
            ON location_mappings (soil_scan_id, soil_id) 
            WHERE soil_scan_id IS NOT NULL AND soil_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS uq_loc_map_scan_soil_id');
    }
};
