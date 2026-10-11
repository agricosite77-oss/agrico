<?php

use App\Livewire\Admin\MunsellColor\ManageMunsellColor;
use App\Services\MunsellFullDescriptionsData;
use App\Services\MunsellLookupService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Imports 207 complete Munsell color descriptions from Full_Munsell_Code.md
     * directly into the production database on Railway.
     */
    public function up(): void
    {
        $isRailway = !empty(env('RAILWAY_ENVIRONMENT'))
            || !empty(env('RAILWAY_SERVICE_NAME'))
            || !empty(env('RAILWAY_PUBLIC_DOMAIN'))
            || !empty(env('RAILWAY_PROJECT_ID'))
            || !empty(env('RAILWAY_ENVIRONMENT_NAME'));

        $isProduction = app()->isProduction()
            || config('app.env') === 'production'
            || str_contains((string) config('app.url'), 'railway.app');

        // Strictly respect user instruction: "don't put that in the local only put that in my https://agrico-production-1eb3.up.railway.app/"
        if (!$isRailway && !$isProduction && app()->isLocal() && !env('FORCE_MUNSELL_PROD')) {
            return;
        }

        if (!Schema::hasTable('munsell_colors')) {
            return;
        }

        $entries = MunsellFullDescriptionsData::getAll();
        $lookupTable = MunsellLookupService::getLookupTable();

        foreach ($entries as $fullCode => $item) {
            $lookup = $lookupTable[$fullCode] ?? null;
            $organicIndex = $lookup['soil_organic_index'] ?? null;
            $soilType = $lookup['soil_type'] ?? null;

            $existing = DB::table('munsell_colors')->where('full_code', $fullCode)->first();

            $hexCode = $existing?->hex_code ?? null;
            if (!$hexCode) {
                $hexCode = ManageMunsellColor::munsellToHex($item['hue'], $item['value'], $item['chroma']);
            }

            if ($existing) {
                DB::table('munsell_colors')
                    ->where('full_code', $fullCode)
                    ->update([
                        'munsell_code' => $fullCode,
                        'hue' => $item['hue'],
                        'value' => (string) $item['value'],
                        'chroma' => (string) $item['chroma'],
                        'value_chroma' => $item['value_chroma'],
                        'color_name' => $item['color_name'],
                        'description' => $item['description'],
                        'hex_code' => $hexCode,
                        'soil_organic_index' => $existing->soil_organic_index ?: $organicIndex,
                        'soil_type' => $existing->soil_type ?: $soilType,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('munsell_colors')->insert([
                    'full_code' => $fullCode,
                    'munsell_code' => $fullCode,
                    'hue' => $item['hue'],
                    'value' => (string) $item['value'],
                    'chroma' => (string) $item['chroma'],
                    'value_chroma' => $item['value_chroma'],
                    'color_name' => $item['color_name'],
                    'description' => $item['description'],
                    'hex_code' => $hexCode,
                    'soil_organic_index' => $organicIndex,
                    'soil_type' => $soilType,
                    'status' => 'Active',
                    'admin_id' => 'ADM-001',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive: preserve descriptions
    }
};
