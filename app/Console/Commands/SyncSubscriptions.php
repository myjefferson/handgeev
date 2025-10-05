<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SyncSubscriptions extends Command
{
    protected $signature = 'subscriptions:sync';
    protected $description = 'Sincroniza assinaturas com o Stripe';

    public function handle()
    {
        $users = User::whereNotNull('stripe_id')->get();
        
        foreach ($users as $user) {
            try {
                $user->syncStripeSubscription();
                $this->info("Sincronizado: {$user->email}");
            } catch (\Exception $e) {
                $this->error("Erro em {$user->email}: {$e->getMessage()}");
            }
        }
    }
}