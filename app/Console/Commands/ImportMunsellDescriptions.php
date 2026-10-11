<?php

namespace App\Console\Commands;

use App\Livewire\Admin\MunsellColor\ManageMunsellColor;
use App\Models\MunsellColor;
use App\Services\MunsellFullDescriptionsData;
use App\Services\MunsellLookupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportMunsellDescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'munsell:import-descriptions 
                            {file? : Optional path to Full_Munsell_Code.md} 
                            {--force-local : Force execution in local development environment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import 207 Munsell soil descriptions from Full_Munsell_Code.md into munsell_colors table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isRailway = !empty(env('RAILWAY_ENVIRONMENT'))
            || !empty(env('RAILWAY_SERVICE_NAME'))
            || !empty(env('RAILWAY_PUBLIC_DOMAIN'))
            || !empty(env('RAILWAY_PROJECT_ID'))
            || !empty(env('RAILWAY_ENVIRONMENT_NAME'));

        $isProduction = app()->isProduction()
            || config('app.env') === 'production'
            || str_contains((string) config('app.url'), 'railway.app');

        if (!$isRailway && !$isProduction && app()->isLocal() && !$this->option('force-local')) {
            $this->warn('Skipping import on local environment per user instruction (target: https://agrico-production-1eb3.up.railway.app/).');
            $this->info('To run locally for testing, pass --force-local flag.');
            return Command::SUCCESS;
        }

        if (!Schema::hasTable('munsell_colors')) {
            $this->error('Table munsell_colors does not exist in the database.');
            return Command::FAILURE;
        }

        $filePath = $this->argument('file');
        $entries = [];

        if ($filePath && file_exists($filePath)) {
            $this->info("Parsing entries from specified file: {$filePath}");
            $content = file_get_contents($filePath);
            $pattern = '/^-\s+\*\*([^\(]+?)\s*\(([^\)]+)\):\*\*\s*(.+)$/m';
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $fullCode = trim($match[1]);
                    $colorName = trim($match[2]);
                    $description = trim($match[3]);

                    $parts = explode(' ', $fullCode, 2);
                    $hue = $parts[0] ?? '';
                    $vc = explode('/', $parts[1] ?? '', 2);
                    $val = $vc[0] ?? '';
                    $chr = $vc[1] ?? '';

                    $entries[$fullCode] = [
                        'full_code' => $fullCode,
                        'hue' => $hue,
                        'value' => $val,
                        'chroma' => $chr,
                        'value_chroma' => "{$val}/{$chr}",
                        'color_name' => $colorName,
                        'description' => $description,
                    ];
                }
            }
        }

        if (empty($entries)) {
            $this->info('Loading 207 reference descriptions from MunsellFullDescriptionsData...');
            $entries = MunsellFullDescriptionsData::getAll();
        }

        $lookupTable = MunsellLookupService::getLookupTable();
        $updatedCount = 0;
        $insertedCount = 0;

        $this->info("Processing " . count($entries) . " Munsell color descriptions...");

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
                $updatedCount++;
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
                $insertedCount++;
            }
        }

        $this->info("Munsell import completed successfully.");
        $this->info("Updated: {$updatedCount} records.");
        $this->info("Inserted: {$insertedCount} records.");
        $this->info("Total in sync: " . count($entries));

        return Command::SUCCESS;
    }
}
