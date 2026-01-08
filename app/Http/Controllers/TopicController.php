<?php

namespace App\Http\Controllers;

use App\Models\FieldValue; // Adicione esta linha
use App\Models\TopicRecord; // Adicione esta linha se necessário
use App\Models\RecordFieldValue;
use App\Models\Topic;
use App\Models\Field;
use App\Models\Workspace;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TopicController extends Controller
{

    
    /**
     * Adiciona um novo tópico a um workspace.
     */
    public function store(Request $request)
    {
        try {
            // Validação correta (sem usar Topic::$rules)
            $validated = $request->validate([
                'workspace_id' => 'required|exists:workspaces,id',
                'title' => 'required|string|max:255',
                'order' => 'required|integer',
                'structure_id' => 'nullable|exists:structures,id',
            ]);

            $workspace = Workspace::find($validated['workspace_id']);

            if (!$workspace) {
                return back()->with([
                    'type' => 'error',
                    'error' => 'not_found',
                    'message' => 'Workspace não encontrado'
                ], 404);
            }

            // Permissões
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->first();

            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'type' => 'error',
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para adicionar tópicos neste workspace'
                ], 403);
            }

            // Verifica se é tópico estruturado
            $isStructured = !empty($validated['structure_id']);

            if ($isStructured) {
                $structure = Structure::find($validated['structure_id']);
                if (!$structure || !$structure->canBeUsedBy($user)) {
                    return back()->with([
                        'type' => 'error',
                        'error' => 'structure_not_accessible',
                        'message' => 'Você não tem permissão para usar esta estrutura'
                    ], 403);
                }
            }

            // Limite de tópicos
            if (!$user->canCreateTopics($workspace->id)) {
                $topicsLimit = $user->getTopicsLimit();
                $currentTopics = $user->getCurrentTopicsCount($workspace->id);

                return back()->with([
                    'type' => 'error',
                    'error' => 'plan_limit_exceeded',
                    'message' => "Limite de tópicos atingido! Seu plano permite {$topicsLimit} tópicos. Você já tem {$currentTopics} tópicos.",
                    'limits' => [
                        'topics' => $topicsLimit,
                        'current_topics' => $currentTopics,
                        'remaining' => $user->getRemainingTopicsCount($workspace->id)
                    ]
                ], 422);
            }

            // Criar tópico
            $topic = Topic::create($validated);

            // Criar registro inicial se for estruturado
            if ($isStructured) {

                // Criar um registro vazio (agora usando TopicRecord)
                $record = TopicRecord::create([
                    'topic_id' => $topic->id,
                    'order' => 1,
                ]);

                // Carrega campos da estrutura
                $structure = Structure::with('fields')->find($validated['structure_id']);

                // Criar valores iniciais
                foreach ($structure->fields as $field) {
                    RecordFieldValue::create([
                        'record_id' => $record->id,
                        'structure_field_id' => $field->id,
                        'field_value' => $field->default_value ?? '',
                    ]);
                }
            }

            // Retornar tópico com relacionamentos
            $topic->load(['structure', 'records.fieldValues']);

            return back()->with([
                'type' => 'success',
                'message' => 'Tópico criado com sucesso',
                'data' => [
                    'topic' => [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'order' => $topic->order,
                        'structure_id' => $topic->structure_id,
                        'structure' => $topic->structure,
                        'records_count' => $topic->records()->count(),
                        'is_structured' => $isStructured
                    ],
                    'limits' => [
                        'remaining_topics' => $user->getRemainingTopicsCount($workspace->id)
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erro ao criar tópico: ' . $e->getMessage());

            return back()->with([
                'type' => 'error',
                'error' => 'server_error',
                'message' => 'Erro ao criar tópico: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Adiciona campos da estrutura vinculada como registros no tópico
     */
    public function addStructureFields(Topic $topic)
    {
        try {
            DB::beginTransaction();

            if (!$topic->structure) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Este tópico não possui uma estrutura vinculada.',
                ], 400);
            }

            // Verificar permissões
            $workspace = $topic->workspace;
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->first();

            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Você não tem permissão para adicionar campos neste tópico.',
                ], 403);
            }

            $fields = $topic->structure->fields;
            
            // 1. Criar um NOVO registro (TopicRecord)
            $lastOrder = $topic->topicRecords()->max('order') ?? 0;
            $record = TopicRecord::create([
                'topic_id' => $topic->id,
                'order' => $lastOrder + 1,
            ]);

            // 2. Criar valores para todos os campos da estrutura
            $addedCount = 0;
            
            foreach ($fields as $field) {
                RecordFieldValue::create([
                    'record_id' => $record->id,
                    'structure_field_id' => $field->id,
                    'field_value' => $field->default_value ?? '',
                ]);
                $addedCount++;
            }

            DB::commit();

            return redirect()->back()->with([
                'success' => "Novo registro criado com {$addedCount} campos.",
                'data' => [
                    'added_count' => $addedCount,
                    'record_id' => $record->id,
                    'total_fields' => $fields->count(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao adicionar estrutura:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'type' => 'error',
                'message' => 'Erro ao adicionar campos da estrutura: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function createFieldsFromStructure(Topic $topic, $structureId)
    {
        $structure = Structure::with('fields')->find($structureId);
        
        if (!$structure) {
            throw new \Exception('Estrutura não encontrada');
        }

        // Criar um registro inicial para o tópico
        $record = TopicRecord::create([
            'topic_id' => $topic->id,
            'order' => 1,
        ]);

        // Criar valores para cada campo da estrutura
        foreach ($structure->fields as $structureField) {
            FieldValue::create([
                'record_id' => $record->id,
                'structure_field_id' => $structureField->id,
                'field_value' => $structureField->default_value ?? '',
            ]);
        }
    }

    /**
     * Cria um novo registro de tópico seguindo o modelo A (Record + FieldValue)
     */
    public function storeRecord(Request $request)
    {
        try {
            $validated = $request->validate([
                'topic_id' => 'required|exists:topics,id',
                'record_order' => 'required|integer',
                'field_values' => 'sometimes|array', // valores opcionais
            ]);

            $topic = Topic::with('structure.fields')->findOrFail($validated['topic_id']);
            $workspace = $topic->workspace;

            // Permissões
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->first();

            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'type' => 'error',
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para adicionar registros neste tópico'
                ], 403);
            }

            // Limite de registros
            if (!$user->canAddMoreRecords($topic->id)) {
                return response()->json([
                    'type' => 'error',
                    'error' => 'plan_limit_exceeded',
                    'message' => 'Limite de registros atingido para este tópico'
                ], 422);
            }

            if (!$topic->structure || $topic->structure->fields->isEmpty()) {
                return response()->json([
                    'type' => 'error',
                    'error' => 'no_structure_fields',
                    'message' => 'A estrutura vinculada não possui campos definidos.'
                ], 422);
            }

            DB::beginTransaction();

            // Criar registro principal
            $record = TopicRecord::create([ // Mude Record::create para TopicRecord::create
                'topic_id' => $topic->id,
                'order' => $validated['record_order'],
            ]);

            // Criar valores iniciais de cada campo da estrutura
            $inputValues = $validated['field_values'] ?? [];

            foreach ($topic->structure->fields as $field) {

                FieldValue::create([
                    'record_id' => $record->id,
                    'structure_field_id' => $field->id,

                    // valor enviado pelo user OU valor padrão da estrutura OU vazio
                    'field_value' => $inputValues[$field->id]
                        ?? $field->default_value
                        ?? '',
                ]);
            }

            DB::commit();

            $record->load('field_values');

            return response()->json([
                'type' => 'success',
                'message' => 'Registro criado com sucesso',
                'record' => $record
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'error' => 'validation_error',
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar registro: ' . $e->getMessage());

            return response()->json([
                'type' => 'error',
                'error' => 'server_error',
                'message' => 'Erro ao criar registro: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Atualiza um tópico existente.
     */
    public function update(Request $request, int $topicId)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'order' => 'sometimes|integer',
            ]);

            // Encontrar o tópico pelo ID
            $topic = Topic::findOrFail($topicId);
            $workspace = $topic->workspace;

            // Verificação de permissão — dono ou colaborador com permissão
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;

            $collaborator = $workspace->collaborators()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->first();

            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return back()->with([
                    'alert' => [
                        'type' => 'error',
                        'message' => 'Você não tem permissão para editar este tópico.'
                    ]
                ]);
            }

            // Atualizar o tópico
            $topic->update($validated);
            $topic->refresh();

            return back()->with([
                'type' => 'success',
                'message' => 'Tópico atualizado com sucesso.',
                'data' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'order' => $topic->order,
                    'workspace_id' => $topic->workspace_id
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return back()->with([
                'alert' => [
                    'type' => 'error',
                    'message' => 'Tópico não encontrado.'
                ]
            ]);

        } catch (ValidationException $e) {

            return back()->with([
                'alert' => [
                    'type' => 'error',
                    'message' => 'Erro de validação. Verifique os campos enviados.',
                    'details' => $e->errors()
                ]
            ]);

        } catch (\Exception $e) {

            return back()->with([
                'alert' => [
                    'type' => 'error',
                    'message' => 'Erro ao atualizar tópico.',
                    'details' => $e->getMessage()
                ]
            ]);
        }
    }

    public function updateTopicStructure(Request $request, $topicId)
    {
        try {
            $validated = $request->validate([
                'structure_id' => 'required|exists:structures,id',
            ]);

            $topic = Topic::findOrFail($topicId);

            // Verificar permissão
            if ($topic->workspace->user_id !== Auth::id()) {
                return back()->with([
                    'type' => 'error',
                    'message' => 'Você não tem permissão para alterar este tópico.'
                ], 403);
            }

            // Atualiza a estrutura do tópico
            $topic->structure_id = $validated['structure_id'];
            $topic->save();

            return back()->with([
                'type' => 'success',
                'message' => 'Estrutura definida com sucesso!',
                'topic' => $topic->load('structure.fields'),
            ]);

        } catch (\Exception $e) {
            return back()->with([
                'type' => 'error',
                'message' => 'Erro ao definir estrutura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exclui um tópico.
     */
    public function destroy(string $id)
    {
        try {
            $topic = Topic::with(['workspace', 'records.fieldValues'])->findOrFail($id);
            $workspace = $topic->workspace;

            // PERMISSÃO
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->first();

            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return back()->with([
                    'type' => 'error',
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para excluir este tópico'
                ], 403);
            }

            // ⚠️ NÃO APAGA STRUCTURE NEM FIELDS DA STRUCTURE

            // Apagar valores
            foreach ($topic->records as $record) {
                $record->fieldValues()->delete();
            }

            // Apagar registros
            $topic->records()->delete();

            // Apagar tópico
            $topic->delete();

            return back()->with([
                'type' => 'success',
                'message' => 'Tópico e todos os valores vinculados foram excluídos com sucesso',
                'data' => [
                    'topic_id' => $id,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with([
                'type' => 'error',
                'error' => 'not_found',
                'message' => 'Tópico não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return back()->with([
                'type' => 'error',
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
                return back()->with([
                    'type' => 'success',
                    'message' => 'Não há tópicos para merge.',
                    'stats' => ['topics_merged' => 0, 'fields_moved' => 0]
                ]);
            }

            // Executar merge
            $mergeStats = $this->mergeTopicsIntoSingle($workspace);

            // Atualizar tipo para Tópico Único
            $workspace->update(['type_workspace_id' => 1]);

            DB::commit();

            return back()->with([
                'type' => 'success',
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
            
            return back()->with([
                'type' => 'success',
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
            $user = Auth::user();

            $topic->load([
                'workspace',
                'structure.fields',
                'records.fieldValues.structureField'
            ]);

            $workspace = $topic->workspace;

            // 🔐 Permissão
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->first();

            if (!$isOwner && (!$collaborator || !$collaborator->canView())) {
                return response()->json([
                    'type' => 'error',
                    'error' => 'permission_denied',
                    'message' => __('workspace.notifications.permission_denied')
                ], 403);
            }

            $exportData = [
                'version' => '2.0',
                'exported_at' => now()->toISOString(),

                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'type' => $workspace->type_workspace_id,
                ],

                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'order' => $topic->order,
                    'created_at' => $topic->created_at->toISOString(),
                    'updated_at' => $topic->updated_at->toISOString(),
                ],

                'structure' => [
                    'id' => $topic->structure->id,
                    'fields' => $topic->structure->fields->map(fn ($field) => [
                        'id' => $field->id,
                        'key_name' => $field->key_name,
                        'type' => $field->type,
                        'order' => $field->order,
                        'is_visible' => $field->is_visible,
                    ])->toArray(),
                ],

                'records' => $topic->records->sortBy('order')->map(fn ($record) => [
                    'id' => $record->id,
                    'order' => $record->order,
                    'created_at' => $record->created_at->toISOString(),
                    'updated_at' => $record->updated_at->toISOString(),

                    'values' => $record->fieldValues->map(fn ($value) => [
                        'structure_field_id' => $value->structure_field_id,
                        'value' => $value->field_value,
                    ])->toArray(),
                ])->values()->toArray(),
            ];

            return response()->streamDownload(function () use ($exportData) {
                echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }, 'topic_'.$workspace->title.'_'.now().'.json', [
                'Content-Type' => 'application/json',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'type' => 'error',
                'error' => 'export_error',
                'message' => $e->getMessage()
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

            // 🔐 Permissão
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->first();

            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'type' => 'error',
                    'error' => 'permission_denied',
                    'message' => __('workspace.notifications.permission_denied')
                ], 403);
            }

            if ($user->isFree()) {
                return response()->json([
                    'type' => 'error',
                    'error' => 'plan_restriction',
                    'message' => __('workspace.import_export.plan_restriction')
                ], 403);
            }

            // 📄 Validação básica
            $request->validate([
                'file' => 'required|file|mimes:json|max:10240',
                'topic_title' => 'nullable|string|max:200',
            ]);

            $importData = json_decode(
                file_get_contents($request->file('file')->getRealPath()),
                true
            );

            if (!is_array($importData)) {
                throw new \Exception('Invalid JSON');
            }

            // ==========================
            // 🏗️ STRUCTURE
            // ==========================
            $structure = Structure::create([
                'workspace_id' => $workspace->id,
                'name' => $importData['topic']['title'] ?? 'Imported Structure',
            ]);

            $fieldIdMap = [];

            foreach (($importData['structure']['fields'] ?? []) as $fieldData) {

                if (empty($fieldData['key_name'])) {
                    continue;
                }

                if (!StructureField::isTypeAllowed($fieldData['type'] ?? 'text', $user)) {
                    continue;
                }

                $field = StructureField::create([
                    'structure_id' => $structure->id,
                    'key_name' => StructureField::formatKeyName($fieldData['key_name']),
                    'type' => $fieldData['type'] ?? 'text',
                    'order' => $fieldData['order'] ?? 0,
                    'is_visible' => $fieldData['is_visible'] ?? true,
                ]);

                // 🔁 Mapeamento antigo → novo
                $fieldIdMap[$fieldData['id'] ?? uniqid()] = $field->id;
            }

            // ==========================
            // 📌 TOPIC
            // ==========================
            $topic = Topic::create([
                'workspace_id' => $workspace->id,
                'structure_id' => $structure->id,
                'title' => $request->topic_title
                    ?? $importData['topic']['title']
                    ?? 'Imported Topic',
                'order' => $importData['topic']['order'] ?? 1,
            ]);

            // ==========================
            // 📄 RECORDS
            // ==========================
            foreach (($importData['records'] ?? []) as $recordData) {

                $record = TopicRecord::create([
                    'topic_id' => $topic->id,
                    'order' => $recordData['order'] ?? 0,
                ]);

                foreach (($recordData['values'] ?? []) as $valueData) {

                    $oldFieldId = $valueData['structure_field_id'] ?? null;

                    if (!$oldFieldId || !isset($fieldIdMap[$oldFieldId])) {
                        continue;
                    }

                    RecordFieldValue::create([
                        'record_id' => $record->id,
                        'structure_field_id' => $fieldIdMap[$oldFieldId],
                        'field_value' => $valueData['value'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => __('workspace.notifications.import_success'),
                'data' => [
                    'topic_id' => $topic->id,
                    'structure_id' => $structure->id,
                    'records' => $topic->records()->count(),
                ]
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'type' => 'error',
                'error' => 'import_error',
                'message' => $e->getMessage()
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
                    'type' => 'error',
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
                'type' => 'error',
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
                'type' => 'success',
                'data' => [
                    'topics' => $topics
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'error' => 'server_error',
                'message' => 'Error loading topics: ' . $e->getMessage()
            ], 500);
        }
    }
}