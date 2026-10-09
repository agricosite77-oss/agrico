<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('soils', function (Blueprint $table) {

            $table->id('soil_id');

            // Soil Information

            $table->string('soil_name');

            $table->text('description')
                ->nullable();

            $table->string('hue')
                ->nullable();

            $table->string('full_code')
                ->nullable();

            // Status

            $table->string('status')
                ->default('Active');

            // Scan Information

            $table->integer('scan_count')
                ->default(0);

            // Color Reference

            $table->unsignedBigInteger('color_id')
                ->nullable();

            // Admin Reference

            $table->string('admin_id', 50)
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('soils');
    }
};
