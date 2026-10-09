<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('soils') && !Schema::hasColumn('soils', 'last_admin_id')) {
            Schema::table('soils', function (Blueprint $table) {
                $table->string('last_admin_id')->nullable()->after('admin_id');
            });
        }

        if (Schema::hasTable('faqs') && !Schema::hasColumn('faqs', 'last_admin_id')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->string('last_admin_id')->nullable()->after('admin_id');
            });
        }

        if (Schema::hasTable('munsell_colors') && !Schema::hasColumn('munsell_colors', 'last_admin_id')) {
            Schema::table('munsell_colors', function (Blueprint $table) {
                $table->string('last_admin_id')->nullable()->after('admin_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('soils') && Schema::hasColumn('soils', 'last_admin_id')) {
            Schema::table('soils', function (Blueprint $table) {
                $table->dropColumn('last_admin_id');
            });
        }

        if (Schema::hasTable('faqs') && Schema::hasColumn('faqs', 'last_admin_id')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->dropColumn('last_admin_id');
            });
        }

        if (Schema::hasTable('munsell_colors') && Schema::hasColumn('munsell_colors', 'last_admin_id')) {
            Schema::table('munsell_colors', function (Blueprint $table) {
                $table->dropColumn('last_admin_id');
            });
        }
    }
};
