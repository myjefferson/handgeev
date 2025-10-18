<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupPasswordResetTokens extends Command
{
    protected $signature = 'tokens:cleanup';
    protected $description = 'Remove tokens de recuperação de senha expirados';

    public function handle()
    {
        $deleted = DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subMinutes(60))
            ->delete();

        $this->info("Tokens expirados removidos: {$deleted}");
        
        \Log::info("Tokens de recuperação expirados removidos: {$deleted}");
        
        return Command::SUCCESS;
    }
}