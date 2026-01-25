<?php

namespace App\Observers;

use App\Models\Topic;
use App\Models\InputConnection;
use App\Jobs\ExecuteInputConnectionJob;

class TopicObserver
{
    /**
     * Handle the Topic "updated" event.
     */
    public function updated(Topic $topic): void
    {
        // Verificar se houve alteração em campos que são triggers
        $original = $topic->getOriginal();
        $changes = $topic->getChanges();
        
        // Verificar alterações nos valores dos campos
        if (isset($changes['updated_at'])) {
            $this->checkForTriggerFields($topic);
        }
    }

    /**
     * Verifica campos trigger e executa conexões
     */
    private function checkForTriggerFields(Topic $topic): void
    {
        // Obter todas as conexões ativas para a estrutura do tópico
        $connections = InputConnection::where('structure_id', $topic->structure_id)
            ->where('is_active', true)
            ->with('triggerField')
            ->get();

        foreach ($connections as $connection) {
            // Se não tem campo trigger, ignora
            if (!$connection->triggerField) {
                continue;
            }

            // Verificar se o campo trigger foi alterado
            $record = $topic->records()->first();
            if (!$record) {
                continue;
            }

            $fieldValue = $record->getFieldValue($connection->triggerField->id);
            
            // Aqui você pode adicionar lógica para verificar se o valor
            // atende a critérios específicos para disparar a conexão
            
            // Executar conexão em background
            ExecuteInputConnectionJob::dispatch($connection, $topic);
        }
    }
}