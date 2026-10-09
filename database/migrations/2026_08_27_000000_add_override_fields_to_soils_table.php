<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soils', function (Blueprint $table) {
            if (!Schema::hasColumn('soils', 'soil_type')) {
                $table->string('soil_type', 100)->nullable()->after('full_code');
            }
            if (!Schema::hasColumn('soils', 'organic_matter')) {
                $table->string('organic_matter', 100)->nullable()->after('soil_type');
            }
            if (!Schema::hasColumn('soils', 'hex_color')) {
                $table->string('hex_color', 20)->nullable()->after('organic_matter');
            }
        });
    }

    public function down(): void
    {
        Schema::table('soils', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('soils', 'soil_type')) {
                $columns[] = 'soil_type';
            }
            if (Schema::hasColumn('soils', 'organic_matter')) {
                $columns[] = 'organic_matter';
            }
            if (Schema::hasColumn('soils', 'hex_color')) {
                $columns[] = 'hex_color';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
