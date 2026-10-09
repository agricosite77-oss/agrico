<?php

use App\Services\MunsellLookupService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('munsell_colors')) {
            Schema::table('munsell_colors', function (Blueprint $table) {
                if (!Schema::hasColumn('munsell_colors', 'soil_organic_index')) {
                    $table->string('soil_organic_index')->nullable()->after('full_code');
                }
                if (!Schema::hasColumn('munsell_colors', 'soil_type')) {
                    $table->string('soil_type')->nullable()->after('soil_organic_index');
                }
            });

            // Populate all records from lookup table
            $lookup = MunsellLookupService::getLookupTable();
            foreach ($lookup as $code => $data) {
                DB::table('munsell_colors')
                    ->where('full_code', $code)
                    ->update([
                        'soil_organic_index' => $data['soil_organic_index'],
                        'soil_type' => $data['soil_type'],
                    ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('munsell_colors')) {
            Schema::table('munsell_colors', function (Blueprint $table) {
                if (Schema::hasColumn('munsell_colors', 'soil_organic_index')) {
                    $table->dropColumn('soil_organic_index');
                }
                if (Schema::hasColumn('munsell_colors', 'soil_type')) {
                    $table->dropColumn('soil_type');
                }
            });
        }
    }
};
