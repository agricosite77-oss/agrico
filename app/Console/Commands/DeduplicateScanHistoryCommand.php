<?php

namespace App\Console\Commands;

use App\Models\SoilScan;
use App\Models\LocationMapping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeduplicateScanHistoryCommand extends Command
{
    protected $signature = 'scan:deduplicate {--dry-run : Only report duplicates without deleting}';
    protected $description = 'Consolidate duplicate SoilScan records from legacy multi-layer scans into a single record per scan';

    public function handle(): int
    {
        $this->info('Scanning for duplicate SoilScan records...');

        // Query only lightweight columns to avoid downloading large base64 image strings
        $scans = DB::table('soil_scans')
            ->select('soil_scan_id', 'user_id', 'before_after_image', 'scan_date', 'created_at', 'scan_note')
            ->orderBy('user_id')
            ->orderBy('soil_scan_id', 'desc')
            ->get();

        $seenGroups = [];
        $idsToDelete = [];
        $reassignMap = []; // duplicate_id => keeper_id

        foreach ($scans as $scan) {
            $groupKey = null;

            // Strategy 1: Shared before_after_image (contains scan UUID unique to this single scan event)
            $ba = trim((string)($scan->before_after_image ?? ''));
            if (!empty($ba) && $ba !== '—' && $ba !== '-') {
                $groupKey = 'ba_' . $scan->user_id . '_' . $ba;
            } elseif (!empty($scan->scan_date)) {
                // Strategy 2: User + scan_date down to the minute + scan note pattern
                $dateMinute = substr((string)$scan->scan_date, 0, 16);
                $groupKey = 'date_' . $scan->user_id . '_' . $dateMinute;
            }

            if (!$groupKey) {
                continue;
            }

            if (!isset($seenGroups[$groupKey])) {
                // First encountered with 'desc' order is the latest ID (dominant layer)
                $seenGroups[$groupKey] = $scan->soil_scan_id;
            } else {
                $keeperId = $seenGroups[$groupKey];
                $idsToDelete[] = $scan->soil_scan_id;
                $reassignMap[$scan->soil_scan_id] = $keeperId;
            }
        }

        $count = count($idsToDelete);
        if ($count === 0) {
            $this->info('No duplicate scan records found.');
            return 0;
        }

        $this->warn("Found {$count} duplicate SoilScan records to prune.");

        if ($this->option('dry-run')) {
            $this->info('Dry-run complete. No records were deleted.');
            return 0;
        }

        DB::transaction(function () use ($idsToDelete, $reassignMap) {
            // Re-point or prune any location mappings pointing to duplicate scans
            foreach ($reassignMap as $dupId => $keeperId) {
                $dupLocations = LocationMapping::where('soil_scan_id', $dupId)->get();
                foreach ($dupLocations as $dupLoc) {
                    $existsOnKeeper = LocationMapping::where('soil_scan_id', $keeperId)
                        ->where('pinned_soil_index', $dupLoc->pinned_soil_index)
                        ->exists();
                    if ($existsOnKeeper) {
                        $dupLoc->delete();
                    } else {
                        $dupLoc->update(['soil_scan_id' => $keeperId]);
                    }
                }
            }

            DB::table('soil_scans')->whereIn('soil_scan_id', $idsToDelete)->delete();
        });

        $this->info("Successfully pruned {$count} duplicate scan records.");

        return 0;
    }
}
