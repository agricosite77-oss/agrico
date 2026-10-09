<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('munsell_colors', function (Blueprint $table) {
            $table->string('value')->nullable()->change();
            $table->string('chroma')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('munsell_colors', function (Blueprint $table) {
            $table->integer('value')->nullable()->change();
            $table->integer('chroma')->nullable()->change();
        });
    }
};
