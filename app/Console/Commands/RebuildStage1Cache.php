<?php

namespace App\Console\Commands;

use App\Services\PythonProcessRunner;
use Illuminate\Console\Command;

class RebuildStage1Cache extends Command
{
    protected $signature = 'soil:rebuild-stage1-cache {--full : Ignore the old cache and re-read every image}';

    protected $description = 'Rebuild the Stage 1 reference feature cache from Real_Soil/ and Fake_Soil/';

    public function handle(PythonProcessRunner $runner): int
    {
        $args = $this->option('full') ? [escapeshellarg('--full')] : [];
        $result = $runner->run(base_path('python/build_stage1_reference_cache.py'), $args);

        if (!$result || !($result['success'] ?? false)) {
            $this->error('Cache rebuild failed: ' . ($result['error'] ?? 'no output (see storage/logs/laravel.log)'));
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Stage 1 cache OK: %d real, %d fake (reused %d, computed %d, failed %d) in %ss',
            $result['real'], $result['fake'], $result['reused'], $result['computed'], $result['failed'], $result['seconds']
        ));
        return self::SUCCESS;
    }
}
