<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->string('application_name');
            $table->string('version')->nullable();
            $table->string('file_path')->nullable();
            $table->string('filename')->nullable();
            $table->string('file_size')->nullable();
            $table->text('release_notes')->nullable();
            $table->string('status')->default('ARCHIVED');
            $table->string('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
