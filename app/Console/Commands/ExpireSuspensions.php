<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ExpireSuspensions extends Command
{
    protected $signature = 'users:expire-suspensions';
    protected $description = 'Auto-expire user suspensions that have passed their suspended_until timestamp';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = User::where('status', 'Suspended')
            ->whereNull('scheduled_deletion_at')
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '<=', now())
            ->update([
                'status' => 'Active',
                'suspended_until' => null,
                'suspend_reason' => null,
            ]);

        $this->info("Expired {$count} user suspensions.");
        return Command::SUCCESS;
    }
}
