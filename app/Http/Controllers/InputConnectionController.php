<?php

namespace App\Http\Controllers;

use App\Models\InputConnection;
use App\Models\Workspace;
use App\Models\Topic;
use App\Models\Structure;
use App\Models\StructureField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\InputConnectionService;

class InputConnectionController extends Controller
{
    protected $connectionService;

    public function __construct(InputConnectionService $connectionService)
    {
        $this->connectionService = $connectionService;
    }

    // /**
    //  * Lista todas as conexões do workspace
    //  */
    // public function index(Workspace $workspace)
    // {
    //     $this->authorize('view', $workspace);

    //     $connections = InputConnection::where('workspace_id', $workspace->id)
    //         ->with(['structure', 'triggerField', 'source', 'mappings.targetField', 'logs' => function($query) {
    //             $query->latest()->limit(5);
    //         }])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(20);

    //     return Inertia::render('Dashboard/InputConnections/Index', [
    //         'workspace' => $workspace,
    //         'connections' => $connections,
    //         'sourceTypes' => \App\Models\InputConnectionSource::getSourceTypes(),
    //         'transformations' => \App\Models\InputConnectionMapping::getTransformations(),
    //     ]);
    // }

    /**
     * Formulário de criação
     */
    public function create(Workspace $workspace)
    {
        if (Auth::id() !== $workspace->user_id) {
            abort(404);
        }

        // Obter tópicos do workspace que têm estrutura vinculada
        $topicsByStructure = Topic::where('workspace_id', $workspace->id)
            ->whereNotNull('structure_id')
            ->with(['structure', 'structure.fields'])
            ->orderBy('title')
            ->get()
            ->groupBy('structure_id')
            ->map(function ($topics) {
                return [
                    'structure' => $topics->first()->structure,
                    'topics' => $topics,
                ];
            })
            ->values();

        $hasStructures = $topicsByStructure->count() > 0;

        return Inertia::render('Dashboard/ApiManagement/GeevStudio/StudioInputConnectionsForm', [
            'workspace' => $workspace,
            'topicsByStructure' => $topicsByStructure,
            'hasStructures' => $hasStructures,
            'sourceTypes' => \App\Models\InputConnectionSource::getSourceTypes(),
            'transformations' => \App\Models\InputConnectionMapping::getTransformations(),
            'defaultConfig' => [
                'rest_api' => [
                    'url' => '',
                    'method' => 'GET',
                    'headers' => [],
                    'parameters' => [],
                    'authentication' => 'none',
                    'timeout' => 30,
                ],
                'webhook' => [
                    'url' => '',
                    'method' => 'POST',
                    'headers' => [],
                ],
                'csv' => [
                    'url' => '',
                    'delimiter' => ',',
                    'has_header' => true,
                ],
            ],
        ]);
    }

    /**
     * Armazena nova conexão
     */
    public function store(Request $request, Workspace $workspace)
    {
        if (Auth::id() !== $workspace->user_id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'topic_id' => 'required|exists:topics,id', // Mudou de structure_id para topic_id
            'trigger_field_id' => 'nullable|exists:structure_fields,id',
            'is_active' => 'boolean',
            'timeout_seconds' => 'integer|min:1|max:300',
            'prevent_loops' => 'boolean',
            
            'source' => 'required|array',
            'source.source_type' => 'required|in:rest_api,webhook,csv,excel,form',
            'source.config' => 'required|array',
            
            'mappings' => 'required|array|min:1',
            'mappings.*.source_field' => 'required|string',
            'mappings.*.target_field_id' => 'required|exists:structure_fields,id',
            'mappings.*.transformation_type' => 'nullable|string',
            'mappings.*.is_required' => 'boolean',
        ]);

        // Verificar se o tópico pertence ao workspace
        $topic = Topic::find($validated['topic_id']);
        if (!$topic || $topic->workspace_id !== $workspace->id) {
            return back()->withErrors(['topic_id' => 'Tópico não pertence a este workspace.']);
        }

        // Obter structure_id do tópico
        $structure_id = $topic->structure_id;
        if (!$structure_id) {
            return back()->withErrors(['topic_id' => 'Este tópico não tem uma estrutura vinculada.']);
        }

        // Criar conexão
        $connection = InputConnection::create([
            'workspace_id' => $workspace->id,
            'structure_id' => $structure_id,
            'topic_id' => $topic->id, // Novo campo
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'trigger_field_id' => $validated['trigger_field_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'timeout_seconds' => $validated['timeout_seconds'] ?? 30,
            'prevent_loops' => $validated['prevent_loops'] ?? true,
        ]);

        // Criar fonte
        $connection->source()->create([
            'source_type' => $validated['source']['source_type'],
            'config' => $validated['source']['config'],
        ]);

        // Criar mapeamentos
        foreach ($validated['mappings'] as $mapping) {
            $connection->mappings()->create([
                'source_field' => $mapping['source_field'],
                'target_field_id' => $mapping['target_field_id'],
                'transformation_type' => $mapping['transformation_type'] ?? 'none',
                'is_required' => $mapping['is_required'] ?? false,
            ]);
        }

        return redirect()->route('workspace.shared-geev-studio.show', [
            'global_key_api' => auth()->user()->global_key_api,
            'workspace_key_api' => $workspace->workspace_key_api
        ])->with('success', 'Conexão criada com sucesso!');
    }

    /**
     * Exibe detalhes da conexão
     */
    public function show(Workspace $workspace, InputConnection $connection)
    {       
        if (Auth::id() !== $workspace->user_id) {
            abort(404);
        }

        $connection->load(['structure.fields', 'triggerField', 'source', 'mappings.targetField', 'logs.topic']);

        return Inertia::render('Dashboard/InputConnections/Show', [
            'workspace' => $workspace,
            'connection' => $connection,
            'recentLogs' => $connection->logs()->with('topic')->latest()->limit(20)->get(),
        ]);
    }

    /**
     * Formulário de edição
     */
public function edit(Workspace $workspace, InputConnection $connection)
    {        
        if (Auth::id() !== $workspace->user_id) {
            abort(404);
        }

        // Obter tópicos do workspace que têm estrutura vinculada
        $topicsByStructure = Topic::where('workspace_id', $workspace->id)
            ->whereNotNull('structure_id')
            ->with(['structure', 'structure.fields'])
            ->orderBy('title')
            ->get()
            ->groupBy('structure_id')
            ->map(function ($topics) {
                return [
                    'structure' => $topics->first()->structure,
                    'topics' => $topics,
                ];
            })
            ->values();

        $connection->load(['topic', 'source', 'mappings.targetField']);

        return Inertia::render('Dashboard/ApiManagement/GeevStudio/StudioInputConnectionsForm', [
            'workspace' => $workspace,
            'topicsByStructure' => $topicsByStructure,
            'hasStructures' => $topicsByStructure->count() > 0,
            'connection' => $connection,
            'sourceTypes' => \App\Models\InputConnectionSource::getSourceTypes(),
            'transformations' => \App\Models\InputConnectionMapping::getTransformations(),
            'defaultConfig' => [
                'rest_api' => [
                    'url' => '',
                    'method' => 'GET',
                    'headers' => [],
                    'parameters' => [],
                    'authentication' => 'none',
                    'timeout' => 30,
                ],
            ],
        ]);
    }

    /**
     * Atualiza conexão
     */
    public function update(Request $request, Workspace $workspace, InputConnection $connection)
    {        
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'topic_id' => 'sometimes|required|exists:topics,id',
            'trigger_field_id' => 'nullable|exists:structure_fields,id',
            'is_active' => 'boolean',
            'timeout_seconds' => 'integer|min:1|max:300',
            'prevent_loops' => 'boolean',
            
            'source' => 'sometimes|required|array',
            'source.source_type' => 'sometimes|required|in:rest_api,webhook,csv,excel,form',
            'source.config' => 'sometimes|required|array',
            
            'mappings' => 'sometimes|required|array|min:1',
            'mappings.*.source_field' => 'required|string',
            'mappings.*.target_field_id' => 'required|exists:structure_fields,id',
            'mappings.*.transformation_type' => 'nullable|string',
            'mappings.*.is_required' => 'boolean',
        ]);

        // Se topic_id foi alterado, verificar permissões
        if (isset($validated['topic_id']) && $validated['topic_id'] != $connection->topic_id) {
            $topic = Topic::find($validated['topic_id']);
            if (!$topic || $topic->workspace_id !== $workspace->id) {
                return back()->withErrors(['topic_id' => 'Tópico não pertence a este workspace.']);
            }
            $structure_id = $topic->structure_id;
        } else {
            $structure_id = $connection->structure_id;
        }

        $connection->update([
            'name' => $validated['name'] ?? $connection->name,
            'description' => $validated['description'] ?? $connection->description,
            'topic_id' => $validated['topic_id'] ?? $connection->topic_id,
            'structure_id' => $structure_id,
            'trigger_field_id' => $validated['trigger_field_id'] ?? $connection->trigger_field_id,
            'is_active' => $validated['is_active'] ?? $connection->is_active,
            'timeout_seconds' => $validated['timeout_seconds'] ?? $connection->timeout_seconds,
            'prevent_loops' => $validated['prevent_loops'] ?? $connection->prevent_loops,
        ]);

        if (isset($validated['source'])) {
            $connection->source()->update([
                'source_type' => $validated['source']['source_type'],
                'config' => $validated['source']['config'],
            ]);
        }

        if (isset($validated['mappings'])) {
            $connection->mappings()->delete();
            foreach ($validated['mappings'] as $mapping) {
                $connection->mappings()->create([
                    'source_field' => $mapping['source_field'],
                    'target_field_id' => $mapping['target_field_id'],
                    'transformation_type' => $mapping['transformation_type'] ?? 'none',
                    'is_required' => $mapping['is_required'] ?? false,
                ]);
            }
        }

        return redirect()->route('workspaces.input-connections.index', $workspace->id)
            ->with('success', 'Conexão atualizada com sucesso!');
    }

    /**
     * Exclui conexão
     */
    public function destroy(Workspace $workspace, InputConnection $connection)
    {
        $this->authorize('edit', $workspace);
        
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        $connection->delete();

        return redirect()->route('workspaces.input-connections.index', $workspace->id)
            ->with('success', 'Conexão excluída com sucesso!');
    }

    /**
     * Executa conexão manualmente
     */
    public function execute(Workspace $workspace, InputConnection $connection)
    {
        $this->authorize('edit', $workspace);
        
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        // Obter tópico para exemplo (na prática, viria da requisição)
        $topic = $workspace->topics()->where('structure_id', $connection->structure_id)->first();
        
        if (!$topic) {
            return back()->with('error', 'Nenhum tópico encontrado para esta estrutura.');
        }

        $result = $this->connectionService->executeConnection($connection, $topic);

        if ($result['success']) {
            return back()->with('success', 'Conexão executada com sucesso!');
        } else {
            return back()->with('error', $result['message']);
        }
    }

    /**
     * Lista logs da conexão
     */
    public function logs(Workspace $workspace, InputConnection $connection)
    {
        $this->authorize('view', $workspace);
        
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        $logs = $connection->logs()
            ->with('topic')
            ->orderBy('executed_at', 'desc')
            ->paginate(20);

        return Inertia::render('Dashboard/InputConnections/Logs', [
            'workspace' => $workspace,
            'connection' => $connection,
            'logs' => $logs,
            'stats' => [
                'total' => $connection->logs()->count(),
                'success' => $connection->logs()->where('status', 'success')->count(),
                'error' => $connection->logs()->where('status', 'error')->count(),
                'pending' => $connection->logs()->where('status', 'pending')->count(),
            ],
        ]);
    }

    /**
     * Testa configuração da conexão
     */
    public function test(Workspace $workspace, InputConnection $connection)
    {
        $this->authorize('edit', $workspace);
        
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        try {
            $source = $connection->source;
            
            switch ($source->source_type) {
                case 'rest_api':
                    $config = $source->getRestApiConfig();
                    $response = Http::timeout($config['timeout'])
                        ->withHeaders($config['headers'])
                        ->{$config['method']}($config['url'], $config['parameters']);
                    
                    return response()->json([
                        'success' => $response->successful(),
                        'status' => $response->status(),
                        'data' => $response->json(),
                    ]);
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Tipo de fonte não suportado para teste',
                    ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtém campos disponíveis da fonte externa
     */
    public function getSourceFields(Workspace $workspace, InputConnection $connection)
    {
        $this->authorize('edit', $workspace);
        
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        try {
            $source = $connection->source;
            
            if ($source->source_type !== 'rest_api') {
                return response()->json([
                    'fields' => [],
                    'message' => 'Funcionalidade disponível apenas para APIs REST',
                ]);
            }

            $config = $source->getRestApiConfig();
            $response = Http::timeout(10)
                ->withHeaders($config['headers'])
                ->{$config['method']}($config['url'], $config['parameters']);

            if ($response->successful()) {
                $data = $response->json();
                $fields = $this->extractFieldsFromData($data);
                
                return response()->json([
                    'fields' => $fields,
                    'sample' => $data,
                ]);
            }
            
            return response()->json([
                'fields' => [],
                'message' => 'Falha na requisição: ' . $response->status(),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'fields' => [],
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extrai campos de dados JSON
     */
    private function extractFieldsFromData($data, $prefix = ''): array
    {
        $fields = [];
        
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $fieldKey = $prefix ? $prefix . '.' . $key : $key;
                
                if (is_array($value) && !empty($value)) {
                    if (isset($value[0]) && is_array($value[0])) {
                        // Array de objetos - pegar primeiro item
                        $fields = array_merge($fields, $this->extractFieldsFromData($value[0], $fieldKey));
                    } else {
                        $fields[] = [
                            'key' => $fieldKey,
                            'type' => 'array',
                            'value' => json_encode($value),
                        ];
                    }
                } else {
                    $fields[] = [
                        'key' => $fieldKey,
                        'type' => gettype($value),
                        'value' => $value,
                    ];
                }
            }
        }
        
        return $fields;
    }
}