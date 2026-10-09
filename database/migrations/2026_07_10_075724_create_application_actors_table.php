<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_actors', function (Blueprint $table) {
            $table->id('application_actor_id');

            $table->string('admin_id', 50)->nullable();
            $table->foreignId('application_id')->constrained('applications', 'application_id')->cascadeOnDelete();
            $table->string('role');

            $table->timestamps();

            $table->foreign('admin_id')->references('admin_id')->on('admins')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_actors');
    }
};
