<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupApiLogs extends Command
{
    protected $signature = 'logs:cleanup {--days=30 : Number of days to keep logs}';
    protected $description = 'Clean up old API request logs';

    public function handle(): void
    {
        $days = $this->option('days');
        
        $deleted = DB::table('api_request_logs')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Deleted {$deleted} old API logs (older than {$days} days).");
    }
}