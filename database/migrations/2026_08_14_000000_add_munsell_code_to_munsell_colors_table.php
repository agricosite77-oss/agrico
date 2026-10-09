<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('munsell_colors')) {
            Schema::table('munsell_colors', function (Blueprint $table) {
                if (!Schema::hasColumn('munsell_colors', 'munsell_code')) {
                    $table->string('munsell_code')->nullable()->after('full_code');
                }
                if (!Schema::hasColumn('munsell_colors', 'hex_code')) {
                    $table->string('hex_code')->nullable()->after('color_name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('munsell_colors')) {
            Schema::table('munsell_colors', function (Blueprint $table) {
                if (Schema::hasColumn('munsell_colors', 'munsell_code')) {
                    $table->dropColumn('munsell_code');
                }
                if (Schema::hasColumn('munsell_colors', 'hex_code')) {
                    $table->dropColumn('hex_code');
                }
            });
        }
    }
};
