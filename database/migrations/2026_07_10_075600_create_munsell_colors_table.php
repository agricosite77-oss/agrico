<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('munsell_colors', function (Blueprint $table) {
            $table->id('munsell_color_id');
            $table->string('full_code');
            $table->string('hue');
            $table->integer('value')->nullable();
            $table->integer('chroma')->nullable();
            $table->string('value_chroma')->nullable();
            $table->string('color_name')->nullable();
            $table->longText('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('status')->default('Active');
            $table->string('admin_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('munsell_colors');
    }
};
