<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermanentAccountDeletion implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::withTrashed()->find($this->userId);

        if (!$user) {
            Log::info("PermanentAccountDeletion: Usuário {$this->userId} não encontrado.");
            return;
        }

        // Verificar se o usuário restaurou a conta
        if (!$user->trashed()) {
            Log::info("PermanentAccountDeletion: Usuário {$this->userId} restaurou a conta antes da exclusão permanente.");
            return;
        }

        // Verificar se ainda está dentro do período de 30 dias
        if ($user->deleted_at->diffInDays(now()) < 30) {
            Log::info("PermanentAccountDeletion: Usuário {$this->userId} ainda está no período de recuperação.");
            return;
        }

        try {
            DB::transaction(function () use ($user) {
                Log::info("PermanentAccountDeletion: Iniciando exclusão permanente do usuário {$this->userId}");

                // Remover permanentemente todos os dados relacionados
                $user->workspaces()->withTrashed()->get()->each(function ($workspace) {
                    // Remover campos dos tópicos
                    $workspace->topics()->withTrashed()->get()->each(function ($topic) {
                        $topic->fields()->withTrashed()->forceDelete();
                        $topic->forceDelete();
                    });
                    
                    // Remover colaboradores
                    $workspace->collaborators()->forceDelete();
                    
                    // Remover workspace
                    $workspace->forceDelete();
                });

                // Remover outras relações
                $user->allCollaborations()->withTrashed()->forceDelete();
                $user->activities()->forceDelete();
                
                // Remover dados do Stripe se existirem
                if ($user->stripe_id) {
                    try {
                        $user->subscriptions->each->delete();
                    } catch (\Exception $e) {
                        Log::error('PermanentAccountDeletion: Erro ao remover dados do Stripe: ' . $e->getMessage());
                    }
                }

                // Remover permissões e roles
                $user->roles()->detach();
                $user->permissions()->detach();

                // Forçar exclusão permanente do usuário
                $user->forceDelete();

                Log::info("PermanentAccountDeletion: Conta do usuário {$this->userId} foi permanentemente removida.");
            });

        } catch (\Exception $e) {
            Log::error("PermanentAccountDeletion: Erro ao excluir usuário {$this->userId}: " . $e->getMessage());
            
            // Relançar a exceção para que o job seja tentado novamente
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("PermanentAccountDeletion: Job falhou para o usuário {$this->userId}: " . $exception->getMessage());
    }
}