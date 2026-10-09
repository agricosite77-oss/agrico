<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_mappings', function (Blueprint $table) {
            $table->id('location_mapping_id');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('barangay')->nullable();
            $table->dateTime('timestamp');
            $table->string('status');

            $table->foreignId('soil_scan_id')->constrained('soil_scans', 'soil_scan_id')->cascadeOnDelete();
            $table->foreignId('soil_id')->constrained('soils', 'soil_id')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_mappings');
    }
};
