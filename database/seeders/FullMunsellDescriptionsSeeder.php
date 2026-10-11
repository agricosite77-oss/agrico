<?php

namespace Database\Seeders;

use App\Livewire\Admin\MunsellColor\ManageMunsellColor;
use App\Services\MunsellFullDescriptionsData;
use App\Services\MunsellLookupService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FullMunsellDescriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds 207 complete Munsell soil descriptions from Full_Munsell_Code.md
     */
    public function run(): void
    {
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
}
