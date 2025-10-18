<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TopicController extends Controller
{
    /**
     * Adiciona um novo tópico a um workspace.
     */
    public function store(Request $request)
    {
        try {
            // Valida a requisição.
            $validated = Validator::make($request->all(), Topic::$rules)->validate();
            
            $workspace = Workspace::find($validated['workspace_id']);
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'error' => 'not_found',
                    'message' => 'Workspace não encontrado'
                ], 404);
            }

            // VERIFICAÇÃO DE PERMISSÃO ATUALIZADA - DONO OU COLABORADOR COM PERMISSÃO
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para adicionar tópicos neste workspace'
                ], 403);
            }
            
            $topic = Topic::create($validated);
            
            // RETORNO ATUALIZADO PARA O JAVASCRIPT
            return response()->json([
                'success' => true,
                'message' => 'Tópico criado com sucesso',
                'data' => [
                    'topic' => [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'order' => $topic->order,
                        'fields_count' => 0
                    ]
                ]
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'validation_error',
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao criar tópico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza um tópico existente.
     */
    public function update(Request $request, Topic $topic)
    {
        try {
            $validated = $request->validate([
                'title' => 'string|max:100',
                'order' => 'integer',
            ]);

            $workspace = $topic->workspace;
            
            // VERIFICAÇÃO DE PERMISSÃO ATUALIZADA - DONO OU COLABORADOR COM PERMISSÃO
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para editar este tópico'
                ], 403);
            }

            $topic->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tópico atualizado com sucesso',
                'data' => [
                    'topic' => $topic
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'validation_error', 
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao atualizar tópico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exclui um tópico.
     */
    public function destroy(string $id)
    {
        try{
            $topic = Topic::with(['workspace', 'fields'])->findOrFail($id);
            $workspace = $topic->workspace;

            // VERIFICAÇÃO DE PERMISSÃO ATUALIZADA - DONO OU COLABORADOR COM PERMISSÃO
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para excluir este tópico'
                ], 403);
            }

            $deletedFieldsCount = $topic->fields->count();
            $topic->fields()->delete();
            $topic->delete();

            // RETORNO ATUALIZADO PARA O JAVASCRIPT
            return response()->json([
                'success' => true,
                'message' => 'Tópico e todos os seus campos foram excluídos com sucesso',
                'data' => [
                    'topic_id' => $id,
                    'deleted_fields' => $deletedFieldsCount
                ]
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'not_found',
                'message' => 'Tópico não encontrado'
            ], 404);
        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao excluir tópico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint específico para fazer o merge de tópicos
     */
    public function mergeTopics(string $workspaceid)
    {
        DB::beginTransaction();
        
        try {
            $workspace = Workspace::with(['topics.fields'])->findOrFail($workspaceid);
            
            // Verificar permissão
            if($workspace->user_id !== Auth::id()) {
                return response()->json(['error' => 'Você não tem permissão para executar esta ação.'], 403);
            }

            // Verificar se realmente precisa de merge
            if ($workspace->topics->count() <= 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Não há tópicos para merge.',
                    'stats' => ['topics_merged' => 0, 'fields_moved' => 0]
                ]);
            }

            // Executar merge
            $mergeStats = $this->mergeTopicsIntoSingle($workspace);

            // Atualizar tipo para Tópico Único
            $workspace->update(['type_workspace_id' => 1]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tópicos fundidos com sucesso!',
                'stats' => $mergeStats,
                'data' => [
                    'type_workspace_id' => 1,
                    'topics_count' => 1,
                    'fields_count' => $mergeStats['fields_moved']
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no merge de tópicos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao fundir tópicos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge automático de múltiplos tópicos em um único
     */
    private function mergeTopicsIntoSingle(Workspace $workspace): array
    {
        $topics = $workspace->topics()->with('fields')->orderBy('order')->get();
        $mergeStats = [
            'topics_merged' => 0,
            'fields_moved' => 0,
            'main_topic_id' => null
        ];

        if ($topics->count() <= 1) {
            return $mergeStats;
        }

        $mainTopic = $topics->first();
        $otherTopics = $topics->slice(1);

        // Coletar todos os campos ordenados
        $allFields = collect();
        $currentOrder = 1;

        foreach ($topics as $topic) {
            foreach ($topic->fields as $field) {
                $allFields->push([
                    'field' => $field,
                    'original_topic' => $topic->title,
                    'new_order' => $currentOrder++
                ]);
            }
        }

        // Atualizar todos os campos para o tópico principal
        foreach ($allFields as $fieldData) {
            $fieldData['field']->update([
                'topic_id' => $mainTopic->id,
                'order' => $fieldData['new_order']
            ]);
            $mergeStats['fields_moved']++;
        }

        // Deletar tópicos vazios (todos exceto o principal)
        foreach ($otherTopics as $topic) {
            $topic->delete();
            $mergeStats['topics_merged']++;
        }

        // Atualizar título do tópico principal
        $mainTopic->update([
            'title' => 'Principal',
            'order' => 1
        ]);

        $mergeStats['main_topic_id'] = $mainTopic->id;

        return $mergeStats;
    }
}