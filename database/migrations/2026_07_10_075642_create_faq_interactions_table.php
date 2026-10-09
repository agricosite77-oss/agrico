<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq_interactions', function (Blueprint $table) {
            $table->id('faq_interaction_id');
            $table->string('action_type');
            $table->dateTime('timestamp');

            $table->string('user_id', 50)->nullable();
            $table->string('admin_id', 50)->nullable();
            $table->foreignId('faq_id')->constrained('faqs', 'faq_id')->cascadeOnDelete();

            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();
            $table->foreign('admin_id')->references('admin_id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_interactions');
    }
};
