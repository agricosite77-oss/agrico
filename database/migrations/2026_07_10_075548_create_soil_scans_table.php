<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soil_scans', function (Blueprint $table) {
            $table->id('soil_scan_id');
            $table->longText('scan_note')->nullable();
            $table->string('scan_image')->nullable();
            $table->dateTime('scan_date')->nullable();

            $table->string('user_id', 50)->nullable();
            $table->unsignedBigInteger('soil_id');

            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('soil_id')->references('soil_id')->on('soils')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soil_scans');
    }
};
