<?php

namespace App\Console\Commands;

use App\Models\MunsellColor;
use App\Services\MunsellRenotationService;
use Illuminate\Console\Command;

class ResyncMunsellHex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'munsell:resync-hex 
                            {--dry-run : Preview the hex changes without modifying the database}
                            {--hue= : Limit to a specific hue family (e.g. 10R, 2.5YR, 10YR)}
                            {--limit= : Limit the number of records to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resync stored hex_code values in munsell_colors using official ASTM D1535 renotation data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $hueFilter = $this->option('hue');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        $this->info($isDryRun ? '🔍 Running in DRY-RUN mode (no database changes will be made)...' : '⚡ Running Munsell hex resync...');

        $query = MunsellColor::query();
        if ($hueFilter) {
            $query->where('hue', $hueFilter);
            $this->line("Filtering by Hue: <comment>{$hueFilter}</comment>");
        }

        if ($limit) {
            $query->limit($limit);
            $this->line("Limiting to <comment>{$limit}</comment> records");
        }

        $records = $query->orderBy('hue')->orderBy('value')->orderBy('chroma')->get();

        if ($records->isEmpty()) {
            $this->warn('No Munsell color records found.');
            return self::SUCCESS;
        }

        $tableRows = [];
        $changedCount = 0;
        $unresolvedCount = 0;
        $identicalCount = 0;

        foreach ($records as $color) {
            $notation = trim($color->full_code ?: "{$color->hue} {$color->value}/{$color->chroma}");
            $currentHex = strtolower(trim($color->hex_code ?? ''));
            if (!empty($currentHex) && !str_starts_with($currentHex, '#')) {
                $currentHex = '#' . $currentHex;
            }

            $astmHex = MunsellRenotationService::notationToHex($color->hue, $color->value, $color->chroma);

            if (!$astmHex) {
                $unresolvedCount++;
                $tableRows[] = [
                    $color->munsell_color_id,
                    $notation,
                    $color->color_name,
                    $currentHex ?: '<empty>',
                    '<unresolved in ASTM>',
                    '⚠️ Skipped (no ASTM match)',
                ];
                continue;
            }

            if ($currentHex === $astmHex) {
                $identicalCount++;
                continue; // Already accurate
            }

            $changedCount++;
            $status = $isDryRun ? 'Would Update' : 'Updated';

            $tableRows[] = [
                $color->munsell_color_id,
                $notation,
                $color->color_name,
                $currentHex ?: '<empty>',
                $astmHex,
                $status,
            ];

            if (!$isDryRun) {
                $color->hex_code = $astmHex;
                $color->saveQuietly();
            }
        }

        if (!empty($tableRows)) {
            $headers = ['ID', 'Notation', 'Name', 'Current Hex', 'ASTM Hex', 'Action'];
            $this->table($headers, array_slice($tableRows, 0, 50));
            if (count($tableRows) > 50) {
                $this->line('... and ' . (count($tableRows) - 50) . ' more records.');
            }
        }

        $this->newLine();
        $this->info("Total Records Examined: {$records->count()}");
        $this->info("Colors with Hex Updates: {$changedCount}");
        $this->info("Colors Already Accurate: {$identicalCount}");
        if ($unresolvedCount > 0) {
            $this->warn("Colors Not in ASTM Palette: {$unresolvedCount}");
        }

        if ($isDryRun && $changedCount > 0) {
            $this->newLine();
            $this->comment("💡 Run without '--dry-run' to apply these {$changedCount} updates to the database.");
        }

        return self::SUCCESS;
    }
}
