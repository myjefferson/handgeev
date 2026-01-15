<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Structure;
use App\Models\Field;
use App\Models\Topic;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Services\HashService;

class WorkspaceController extends Controller
{
    /**
     * Exibe a lista de todos os workspaces do usuário autenticado.
     */
    public function indexWorkspaces()
    {
        $user = auth()->user();
    
        $workspaces = $user->workspaces()
            ->withCount('topics')
            ->orderBy('created_at', 'desc')
            ->get();

        $collaborations = $user->collaborations()
            ->with(['workspace' => function($query) {
                $query->withCount('topics');
            }])
            ->orderBy('joined_at', 'desc')
            ->get();
            
        return Inertia::render('Dashboard/Workspace/MyWorkspaces', [
            'workspaces' => $workspaces,
            'collaborations' => $collaborations,
        ]);
    }
    
    /**
     * Exibe a lista de todos os workspaces do usuário autenticado.
     */
    public function index($id)
    {
        $workspace = Workspace::with([
            'topics.structure.fields',
            'topics.records.field_values',
            'topics.records.field_values.structureField',
        ])->findOrFail($id);

        // Carregar estruturas disponíveis para o usuário
        $availableStructures = Structure::where('user_id', auth()->id())
            ->withCount('fields')
            ->get();

        // Calcular limites para cada tópico
        $topicsWithLimits = [];
        foreach ($workspace->topics as $topic) {
            $fieldsCount = $topic->structure ? $topic->structure->fields->count() : 0;
            $fieldsLimit = auth()->user()->getPlan()->max_fields ?? 0;

            $topicsWithLimits[$topic->id] = [
                'currentFieldsCount' => $fieldsCount,
                'fieldsLimit' => $fieldsLimit,
                'remainingFields' => $fieldsLimit - $fieldsCount,
                'isUnlimited' => auth()->user()->getPlan()->max_fields === null,
            ];
        }

        return Inertia::render('Dashboard/Workspace/Workspace', [
            'workspace' => $workspace,
            'availableStructures' => $availableStructures,
            'topicsWithLimits' => $topicsWithLimits,
            'workspaceLimits' => [
                'currentTopics' => auth()->user()->current_topics_count,
                'topicsLimit' => auth()->user()->topics_limit,
                'canCreateTopics' => auth()->user()->canCreateTopics($id),
            ]
        ]);
    }

    /**
     * Armazena um novo workspace no banco de dados.
     */
    public function store(Request $request)
    {
        try {
            $workspaceData = $request->all();
            // Converte o checkbox para boolean
            $workspaceData['is_published'] = $request->has('is_published');
            $workspaceData['workspace_key_api'] = HashService::generateUniqueHash();            
            
            $validatedData = Validator::make($workspaceData, Workspace::$rules)->validate();
            $workspace = auth()->user()->workspaces()->create($validatedData);
            // 4. Cria um tópico padrão para o novo workspace.
            $workspace->topics()->create([
                'order' => 1,
                'title' => 'First Topic',
            ]);
            return redirect()->route('workspace.show', ['id' => $workspace->id])->with('success', 'Workspace criado com sucesso!');
        } catch (ValidationException $e) {
            // return redirect()->back()->withErrors($e->errors())->withInput();
            return $e->errors();
        }
    }

    /**
     * Atualiza os dados de um workspace existente.
     */
    public function update(Request $request, string $id)
    {        
        DB::beginTransaction();
        
        try {
            $workspace = Workspace::with(['topics.fields'])->findOrFail($id);
            
            // Verificar permissão
            if($workspace->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'Você não tem permissão para alterar este workspace.');
            }

            // Validação
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:100',
                'type_workspace_id' => 'required|integer|exists:type_workspaces,id',
                'is_published' => 'sometimes|boolean',
                'description' => 'nullable|string|max:250',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', 'Por favor, corrija os erros abaixo.')
                    ->withInput();
            }

            $validatedData = $validator->validated();

            // Se description estiver vazio, definir como null
            if (empty($validatedData['description'])) {
                $validatedData['description'] = null;
            }

            // Verificar se houve mudança no tipo do workspace
            $oldType = $workspace->type_workspace_id;
            $newType = $validatedData['type_workspace_id'];
            $typeChanged = $oldType != $newType;

            // // Se mudou para Tópico Único e tinha múltiplos tópicos
            // if ($typeChanged && $newType == 1 && $workspace->topics->count() > 1) {
            //     // Se for requisição AJAX, retornar info para o modal
            //     if ($request->ajax()) {
            //         DB::rollBack();
            //         return response()->json([
            //             'needs_merge_confirmation' => true,
            //             'topics_count' => $workspace->topics->count(),
            //             'fields_count' => $workspace->getFieldsCountAttribute(),
            //             'workspace_id' => $workspace->id,
            //             'new_type' => $newType
            //         ]);
            //     }
                
            //     $this->mergeTopicsIntoSingle($workspace);
            // }

            // Atualizar workspace
            $workspace->update($validatedData);

            DB::commit();

            // Se for requisição AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Workspace atualizado com sucesso!',
                    'data' => [
                        'title' => $workspace->title,
                        'type_workspace_id' => $workspace->type_workspace_id,
                        'description' => $workspace->description,
                        'updated_at' => $workspace->updated_at->format('d/m/Y H:i')
                    ]
                ]);
            }

            return redirect()->route('workspace.setting', ['id' => $id])
                ->with('success', 'Workspace atualizado com sucesso!');

        } catch (ValidationException $e) {
            DB::rollBack();
            
            $errorMessage = $this->formatValidationErrors($e->errors());
            if ($request->ajax()) {
                return response()->json(['error' => $errorMessage], 422);
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar workspace: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erro ao atualizar workspace: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Exclui um workspace e todos os topicos e recors vinculados.
     */
    public function destroy(string $id)
    {
        try {
            $workspace = Workspace::findOrFail($id);

            if ($workspace->user_id !== Auth::id()) {
                return response()->json([
                    'error' => 'Você não tem permissão para excluir este workspace'
                ], 403);
            }

            DB::transaction(function () use ($workspace) {

                // 1️⃣ Apagar record_field_values
                DB::table('record_field_values')
                    ->whereIn('record_id', function ($query) use ($workspace) {
                        $query->select('tr.id')
                            ->from('topic_records as tr')
                            ->join('topics as t', 't.id', '=', 'tr.topic_id')
                            ->where('t.workspace_id', $workspace->id);
                    })
                    ->delete();

                // 2️⃣ Apagar topic_records
                DB::table('topic_records')
                    ->whereIn('topic_id', function ($query) use ($workspace) {
                        $query->select('id')
                            ->from('topics')
                            ->where('workspace_id', $workspace->id);
                    })
                    ->delete();

                // 3️⃣ Apagar topics
                DB::table('topics')
                    ->where('workspace_id', $workspace->id)
                    ->delete();

                // 4️⃣ Apagar workspace
                DB::table('workspaces')
                    ->where('id', $workspace->id)
                    ->delete();
            });

            return redirect(route('workspaces.show'))->with([
                'success' => true,
                'message' => 'Workspace excluído com sucesso'
            ]);

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->withErrors('Workspace não encontrado');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors('Erro ao excluir workspace');
        }
    }
    
    public function stats(Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        $stats = [
            'topics_count' => $workspace->topics()->count(),
            'fields_count' => $workspace->topics()->withCount('fields')->get()->sum('fields_count'),
            'views_count' => $workspace->views_count,
            'collaborators_count' => $workspace->collaborators()->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Exibe a página de importação
     */
    public function showImportForm()
    {
        $user = Auth::user();
        
        if (
            !$user->isStart() &&
            !$user->isPro() && 
            !$user->isPremium() && 
            !$user->isAdmin()
        ) {
            return redirect()->route('workspaces.show')
                ->with('error', 'A importação de workspaces está disponível apenas para usuários Pro.');
        }

        return Inertia::render('Dashboard/Workspace/ImportWorkspace', [
            'lang' => __('import'),
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_start' => $user->isStart(),
                    'is_pro' => $user->isPro(),
                    'is_premium' => $user->isPremium(),
                    'is_admin' => $user->isAdmin(),
                ]
            ],
        ]);
    }

    /**
     * Exporta workspace completo no formato especificado
     */
    public function export($id)
    {
        try {
            $workspace = Workspace::with(['topics.fields' => function($query) {
                $query->orderBy('order');
            }])
            ->where('id', $id)
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('collaborators', function($q) {
                        $q->where('user_id', Auth::id())
                            ->where('status', 'accepted');
                    });
            })
            ->firstOrFail();

            $exportData = [
                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'type_workspace_id' => $workspace->type_workspace_id,
                    'is_published' => $workspace->is_published,
                    'api_enabled' => $workspace->api_enabled,
                    'created_at' => $workspace->created_at->toISOString(),
                    'updated_at' => $workspace->updated_at->toISOString(),
                    'topics' => $workspace->topics->map(function($topic) {
                        return [
                            'id' => $topic->id,
                            'title' => $topic->title,
                            'order' => $topic->order,
                            'created_at' => $topic->created_at->toISOString(),
                            'updated_at' => $topic->updated_at->toISOString(),
                            'fields' => $topic->fields->map(function($field) {
                                // Estrutura com key_name como chave principal
                                $fieldData = [
                                    'id' => $field->id,
                                    'is_visible' => $field->is_visible,
                                    'order' => $field->order,
                                    'created_at' => $field->created_at->toISOString(),
                                    'updated_at' => $field->updated_at->toISOString()
                                ];
                                
                                // Adiciona key_name como chave principal apenas se não estiver vazia
                                if (!empty($field->key_name)) {
                                    $fieldData[$field->key_name] = $field->value;
                                    $fieldData['type'] = $field->type;
                                }
                                
                                return $fieldData;
                            })->toArray()
                        ];
                    })->toArray()
                ],
                'export_info' => [
                    'exported_at' => now()->toISOString(),
                    'exported_by' => Auth::user()->email,
                    'handgeev_version' => env('APP_VERSION'),
                    'total_topics' => $workspace->topics->count(),
                    'total_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->count();
                    }),
                    'type' => 'full_export'
                ]
            ];

            $filename = "handgeev_workspace_{$workspace->title}_" . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($exportData, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Workspace não encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao exportar workspace: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exportação rápida - apenas dados essenciais no formato com key_name como chave
     */
    public function exportQuick($id)
    {
        try {
            $workspace = Workspace::with(['topics.fields' => function($query) {
                $query->orderBy('order');
            }])
            ->where('id', $id)
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('collaborators', function($q) {
                        $q->where('user_id', Auth::id())
                            ->where('status', 'accepted');
                    });
            })
            ->firstOrFail();

            $exportData = [
                'workspace' => [
                    'title' => $workspace->title,
                    'is_published' => $workspace->is_published,
                    'api_enabled' => $workspace->api_enabled,
                    'topics' => $workspace->topics->map(function($topic) {
                        return [
                            'title' => $topic->title,
                            'order' => $topic->order,
                            'fields' => $topic->fields->map(function($field) {
                                // Estrutura simplificada com key_name como chave principal
                                $fieldData = [
                                    'is_visible' => $field->is_visible,
                                    'order' => $field->order
                                ];
                                
                                // Adiciona key_name como chave principal apenas se não estiver vazia
                                if (!empty($field->key_name)) {
                                    $fieldData[$field->key_name] = $field->value;
                                }
                                
                                return $fieldData;
                            })->toArray()
                        ];
                    })->toArray()
                ],
                'export_info' => [
                    'exported_at' => now()->toISOString(),
                    'exported_by' => Auth::user()->email,
                    'handgeev_version' => env('APP_VERSION'),
                    'type' => 'quick_export'
                ]
            ];

            $filename = "handgeev_quick_{$workspace->title}_" . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($exportData, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao exportar workspace: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Importa um workspace a partir de um arquivo JSON
     */
    public function import(Request $request)
    {
        if (
            !auth()->user()->isStart() &&
            !auth()->user()->isPro() && 
            !auth()->user()->isPremium() && 
            !auth()->user()->isAdmin()
        ) {
            return redirect()->route('workspaces.show')
                ->with('error', 'A importação de workspaces está disponível apenas para usuários Pro.');
        }

        try {
            $validator = Validator::make($request->all(), [
                'workspace_file' => 'required|file|mimes:json|max:10240', // 10MB max
                'workspace_title' => 'sometimes|string|max:100',
                'import_mode' => 'sometimes|in:merge,replace' // Novo: modo de importação
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Verificar se o usuário pode criar mais workspaces
            if (!auth()->user()->canCreateWorkspace()) {
                return redirect()->back()->with('error', 'Você atingiu o limite de workspaces. Faça upgrade para importar mais.');
            }

            $file = $request->file('workspace_file');
            $jsonContent = file_get_contents($file->getRealPath());
            $importData = json_decode($jsonContent, true);

            // Validar estrutura do JSON
            if (!$this->validateImportStructure($importData)) {
                return redirect()->back()->with('error', 'Estrutura do arquivo JSON inválida. Verifique se segue o formato correto do HandGeev.');
            }

            return DB::transaction(function () use ($importData, $request) {
                $workspaceData = $importData['workspace'];
                
                // Criar novo workspace
                $newWorkspace = auth()->user()->workspaces()->create([
                    'title' => $request->workspace_title ?: ($workspaceData['title'] ?? 'Workspace Importado'),
                    'type_workspace_id' => $workspaceData['type_workspace_id'] ?? 1,
                    'is_published' => $workspaceData['is_published'] ?? false,
                    'api_enabled' => $workspaceData['api_enabled'] ?? false,
                    'workspace_key_api' => HashService::generateUniqueHash()
                ]);

                // Importar tópicos e campos
                $importStats = $this->importTopicsAndFields($newWorkspace, $workspaceData['topics'] ?? []);

                return redirect()->route('workspace.show', ['id' => $newWorkspace->id])
                    ->with('success', "Workspace importado com sucesso! {$importStats['topics']} tópicos e {$importStats['fields']} campos criados.");

            });

        } catch (\Exception $e) {
            Log::error('Erro na importação do workspace: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao importar workspace: ' . $e->getMessage());
        }
    }

    /**
     * Importa tópicos e campos do workspace
     */
    private function importTopicsAndFields($workspace, $topicsData)
    {
        $stats = ['topics' => 0, 'fields' => 0];

        foreach ($topicsData as $topicData) {
            $topic = $workspace->topics()->create([
                'title' => $topicData['title'] ?? 'Tópico Sem Título',
                'order' => $topicData['order'] ?? ($stats['topics'] + 1)
            ]);
            $stats['topics']++;

            foreach ($topicData['fields'] as $fieldData) {
                // Extrair key_name e value da nova estrutura
                $fieldInfo = $this->extractFieldData($fieldData);
                
                if ($fieldInfo) {
                    $topic->fields()->create([
                        'key_name' => $fieldInfo['key_name'],
                        'value' => $fieldInfo['value'],
                        'is_visible' => $fieldInfo['is_visible'],
                        'order' => $fieldInfo['order']
                    ]);
                    $stats['fields']++;
                }
            }
        }

        return $stats;
    }

    /**
     * Extrai dados do campo da nova estrutura
     */
    private function extractFieldData($fieldData)
    {
        // Campos fixos que sempre existem
        $fixedFields = ['id', 'is_visible', 'order', 'created_at', 'updated_at'];
        
        // Encontrar a chave dinâmica (key_name) e seu valor
        $keyName = null;
        $value = null;
        
        foreach ($fieldData as $key => $val) {
            if (!in_array($key, $fixedFields)) {
                $keyName = $key;
                $value = $val;
                break;
            }
        }
        
        // Se não encontrou key_name dinâmica, verifica se existe no formato antigo
        if (!$keyName && isset($fieldData['key_name'])) {
            $keyName = $fieldData['key_name'];
            $value = $fieldData['value'] ?? '';
        }
        
        // Se ainda não tem key_name, pular este campo
        if (!$keyName) {
            return null;
        }

        return [
            'key_name' => $keyName,
            'value' => $value ?? '',
            'is_visible' => $fieldData['is_visible'] ?? true,
            'order' => $fieldData['order'] ?? 1
        ];
    }

    /**
     * Valida a estrutura do JSON de importação (atualizada)
     */
    private function validateImportStructure($data): bool
    {
        if (!isset($data['workspace'])) {
            return false;
        }

        $workspace = $data['workspace'];
        
        // Validar campos básicos do workspace
        if (!isset($workspace['title'])) {
            return false;
        }

        // Validar estrutura de tópicos
        if (isset($workspace['topics']) && is_array($workspace['topics'])) {
            foreach ($workspace['topics'] as $topic) {
                if (!isset($topic['title']) || !isset($topic['fields']) || !is_array($topic['fields'])) {
                    return false;
                }

                // Validar estrutura dos campos na nova formatação
                foreach ($topic['fields'] as $field) {
                    if (!$this->isValidFieldStructure($field)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Valida a estrutura do campo na nova formatação
     */
    private function isValidFieldStructure($field): bool
    {
        // Deve ter pelo menos uma chave dinâmica (key_name) além dos campos fixos
        $fixedFields = ['id', 'is_visible', 'order', 'created_at', 'updated_at'];
        $dynamicKeys = array_diff(array_keys($field), $fixedFields);
        
        return count($dynamicKeys) > 0 || isset($field['key_name']);
    }
}
