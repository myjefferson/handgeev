<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Field;
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

    /**
     * Exporta um tópico com todos os seus campos
     */
    public function export(Topic $topic)
    {
        try {
            $workspace = $topic->workspace;
            
            // Verificar permissão
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canView())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => __('workspace.notifications.permission_denied')
                ], 403);
            }

            // Carregar tópico com campos ordenados
            $topic->load(['fields' => function($query) {
                $query->orderBy('order');
            }]);

            // Estrutura de dados para exportação
            $exportData = [
                'version' => '1.0',
                'exported_at' => now()->toISOString(),
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'order' => $topic->order,
                    'created_at' => $topic->created_at->toISOString(),
                    'updated_at' => $topic->updated_at->toISOString(),
                ],
                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'type' => $workspace->type_workspace_id,
                ],
                'fields' => $topic->fields->map(function($field) {
                    return [
                        'id' => $field->id,
                        'key_name' => $field->key_name,
                        'value' => $field->value,
                        'type' => $field->type,
                        'is_visible' => $field->is_visible,
                        'order' => $field->order,
                        'created_at' => $field->created_at->toISOString(),
                        'updated_at' => $field->updated_at->toISOString(),
                    ];
                })->toArray()
            ];

            return response()->json([
                'success' => true,
                'data' => $exportData,
                'message' => __('workspace.notifications.export_success')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'export_error',
                'message' => __('workspace.notifications.export_error', ['message' => $e->getMessage()])
            ], 500);
        }
    }

    /**
     * Importa um tópico de um arquivo JSON
     */
    public function import(Request $request, Workspace $workspace)
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            // Verificar permissão de escrita no workspace
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => __('workspace.notifications.permission_denied')
                ], 403);
            }

            // Verificar plano do usuário - apenas Start, Pro, Premium e Admin podem importar
            if ($user->isFree()) {
                return response()->json([
                    'success' => false,
                    'error' => 'plan_restriction',
                    'message' => __('workspace.import_export.plan_restriction')
                ], 403);
            }

            // Validar requisição
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:json|max:10240', // 10MB max
                'topic_title' => 'required|string|max:200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'validation_error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ler e validar arquivo
            $file = $request->file('file');
            $fileContent = file_get_contents($file->getRealPath());
            $importData = json_decode($fileContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'error' => 'invalid_json',
                    'message' => __('workspace.notifications.file_invalid')
                ], 422);
            }

            // Validar estrutura do arquivo
            $validationResult = $this->validateImportFile($importData);
            if (!$validationResult['valid']) {
                return response()->json([
                    'success' => false,
                    'error' => 'invalid_structure',
                    'message' => $validationResult['message']
                ], 422);
            }

            // Verificar limites do plano
            $fieldsCount = count($importData['fields']);
            if (!$user->canAddMoreFields($workspace->id)) {
                $remaining = $user->getRemainingFieldsCount($workspace->id);
                return response()->json([
                    'success' => false,
                    'error' => 'plan_limit_exceeded',
                    'message' => __('workspace.import_export.plan_limit_exceeded', ['remaining' => $remaining])
                ], 422);
            }

            // Criar novo tópico
            $topic = Topic::create([
                'workspace_id' => $workspace->id,
                'title' => $request->topic_title,
                'order' => $workspace->topics()->max('order') + 1,
            ]);

            // Importar campos
            $importedFields = 0;
            $order = 1;

            foreach ($importData['fields'] as $fieldData) {
                // Validar tipo de campo baseado no plano do usuário
                if (!Field::isTypeAllowed($fieldData['type'], $user)) {
                    continue; // Pula campos com tipos não permitidos
                }

                // Criar campo
                Field::create([
                    'topic_id' => $topic->id,
                    'key_name' => Field::formatKeyName($fieldData['key_name']),
                    'value' => $fieldData['value'] ?? '',
                    'type' => $fieldData['type'],
                    'is_visible' => $fieldData['is_visible'] ?? true,
                    'order' => $order++,
                ]);

                $importedFields++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('workspace.notifications.import_success'),
                'data' => [
                    'topic' => [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'order' => $topic->order,
                    ],
                    'imported_fields' => $importedFields,
                    'total_fields' => count($importData['fields'])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => 'import_error',
                'message' => __('workspace.notifications.import_error', ['message' => $e->getMessage()])
            ], 500);
        }
    }

        /**
     * Download do tópico exportado como arquivo JSON
     */
    public function download(Topic $topic)
    {
        try {
            $workspace = $topic->workspace;
            
            // Verificar permissão
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canView())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => __('workspace.notifications.permission_denied')
                ], 403);
            }

            // Carregar tópico com campos
            $topic->load(['fields' => function($query) {
                $query->orderBy('order');
            }]);

            // Preparar dados para download
            $exportData = [
                'version' => '1.0',
                'exported_at' => now()->toISOString(),
                'source' => 'HandGeev',
                'topic' => [
                    'title' => $topic->title,
                    'order' => $topic->order,
                ],
                'fields' => $topic->fields->map(function($field) {
                    return [
                        'key_name' => $field->key_name,
                        'value' => $field->value,
                        'type' => $field->type,
                        'is_visible' => $field->is_visible,
                        'order' => $field->order,
                    ];
                })->toArray()
            ];

            $filename = "topic_{$topic->title}_" . now()->format('Y-m-d_H-i-s') . '.json';
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

            return response()->streamDownload(function() use ($exportData) {
                echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }, $filename, [
                'Content-Type' => 'application/json',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'download_error',
                'message' => __('workspace.notifications.export_error', ['message' => $e->getMessage()])
            ], 500);
        }
    }

    /**
     * Valida a estrutura do arquivo de importação
     */
    private function validateImportFile(array $data): array
    {
        // Verificar estrutura básica
        if (!isset($data['fields']) || !is_array($data['fields'])) {
            return [
                'valid' => false,
                'message' => __('workspace.import_export.invalid_file')
            ];
        }

        // Validar cada campo
        foreach ($data['fields'] as $index => $field) {
            if (!isset($field['key_name']) || empty(trim($field['key_name']))) {
                return [
                    'valid' => false,
                    'message' => __('workspace.import_export.field_required', [
                        'index' => $index,
                        'field' => 'key_name'
                    ])
                ];
            }

            if (!isset($field['type']) || empty(trim($field['type']))) {
                return [
                    'valid' => false,
                    'message' => __('workspace.import_export.field_required', [
                        'index' => $index,
                        'field' => 'type'
                    ])
                ];
            }

            // Validar tipos permitidos
            $allowedTypes = ['text', 'number', 'boolean', 'email', 'url', 'date', 'json'];
            if (!in_array($field['type'], $allowedTypes)) {
                return [
                    'valid' => false,
                    'message' => __('workspace.import_export.type_not_supported', [
                        'index' => $index,
                        'type' => $field['type']
                    ])
                ];
            }
        }

        return [
            'valid' => true,
            'message' => 'File is valid'
        ];
    }

    /**
     * Obtém a lista de tópicos para importação em outros workspaces
     */
    public function importableTopics(Request $request)
    {
        try {
            $user = Auth::user();
            $currentWorkspaceId = $request->input('current_workspace_id');

            // Buscar todos os workspaces do usuário (como owner ou colaborador)
            $ownedWorkspaces = Workspace::where('user_id', $user->id)
                ->with(['topics' => function($query) {
                    $query->orderBy('title');
                }])
                ->get();

            $collaboratorWorkspaces = Workspace::whereHas('collaborators', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'accepted')
                    ->where('can_edit', true);
            })
            ->with(['topics' => function($query) {
                $query->orderBy('title');
            }])
            ->get();

            $allWorkspaces = $ownedWorkspaces->merge($collaboratorWorkspaces);
            $topics = [];

            foreach ($allWorkspaces as $workspace) {
                // Pular o workspace atual
                if ($workspace->id == $currentWorkspaceId) {
                    continue;
                }

                foreach ($workspace->topics as $topic) {
                    $topics[] = [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'fields_count' => $topic->fields->count(),
                        'workspace' => [
                            'id' => $workspace->id,
                            'title' => $workspace->title,
                        ],
                        'created_at' => $topic->created_at->toISOString(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'topics' => $topics
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Error loading topics: ' . $e->getMessage()
            ], 500);
        }
    }
}