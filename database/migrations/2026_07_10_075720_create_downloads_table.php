<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id('download_id');

            $table->foreignId('application_id')->constrained('applications', 'application_id')->cascadeOnDelete();
            $table->string('user_id', 50)->nullable();
            $table->string('download_type')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
