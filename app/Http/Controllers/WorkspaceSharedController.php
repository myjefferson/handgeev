<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Services\RateLimitService;
use App\Services\ApiStatisticsService;
use Illuminate\Support\Facades\Crypt;
use App\Models\Workspace;
use App\Models\Topic;
use App\Models\User;
use App\Models\InputConnection;
use Illuminate\Http\Request;
use Auth;

class WorkspaceSharedController extends Controller
{
    // Rota para a interface de visualização compartilhada
    public function geevStudio($global_key_api, $workspace_key_api)
    {
        // Encontrar o usuário pelo global_key_api
        $user = User::where('global_key_api', $global_key_api)->firstOrFail();
        
        // Buscar workspace com relações corretas
        $workspace = Workspace::with([
            'topics.records.fieldValues.structureField' => function ($query) {
                $query->orderBy('order', 'asc');
            }
        ])
        ->where('user_id', $user->id)
        ->where('workspace_key_api', $workspace_key_api)
        ->firstOrFail();

        // Verificar permissões
        if (!$workspace->is_published && $workspace->user_id != Auth::id()) {
            abort(403);
        }

        if ($workspace->type_view_workspace_id != 1) {
            abort(404);
        }

        $rateLimitInfo = RateLimitService::getRateLimitStatus($user);

        // Primeiro tópico
        $firstTopic = $workspace->topics->first();

        // Primeiro campo REAL (RecordFieldValue + StructureField)
        $firstField = null;
        if ($firstTopic && $firstTopic->records->isNotEmpty()) {
            $firstField = $firstTopic->records->first()->fieldValues->first();
        }

        // 🟢 CARREGAR TODAS AS CONEXÕES DE ENTRADA com relacionamentos necessários
        $inputConnections = InputConnection::where('workspace_id', $workspace->id)
            ->with([
                'structure', 
                'triggerField', 
                'source', 
                'mappings.targetField',
                'logs' => function($query) {
                    $query->latest()->limit(5);
                },
                'topic' // Se você adicionou topic_id
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Preparar dados para envio
        $sharedData = [
            'workspace' => [
                'id' => $workspace->id,
                'title' => $workspace->title,
                'workspace_key_api' => $workspace->workspace_key_api,
                'is_published' => $workspace->is_published,
                'topics' => $workspace->topics->map(function($topic) {
                    return [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'records' => $topic->records->map(function($record) {
                            return [
                                'record_id' => $record->id,
                                'values' => $record->fieldValues->map(function($value) {
                                    return [
                                        'id' => $value->id,
                                        'key_name' => $value->structureField->name,
                                        'type' => $value->structureField->type,
                                        'is_visible' => $value->structureField->is_visible,
                                        'order' => $value->structureField->order,
                                        'value' => $value->formatted_value,
                                    ];
                                }),
                            ];
                        }),
                    ];
                })
            ],
            'rateLimitInfo' => $rateLimitInfo,
            'global_key_api' => $global_key_api,
            'workspace_key_api' => $workspace_key_api,
            'share_url' => url()->current(),
            'api_endpoints' => [
                'first_topic_id' => $firstTopic?->id,
                'first_field_id' => $firstField?->structure_field_id,
                'base_url' => url('/api')
            ],
            // 🟢 Enviar as conexões para a aba do GeevStudio
            'connections' => $inputConnections, // Mudei de inputConnections para connections
            // 🟢 Adicionar dados de configuração também
            'sourceTypes' => \App\Models\InputConnectionSource::getSourceTypes(),
            'transformations' => \App\Models\InputConnectionMapping::getTransformations(),
        ];

        return Inertia::render('Dashboard/ApiManagement/GeevStudio/GeevStudio', $sharedData);
    }

    // Seu WorkspaceController - ADICIONAR ESTE MÉTODO
    public function showApiRest($global_key_api, $workspace_key_api)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $user = User::where('global_key_api', $global_key_api)->firstOrFail();

        $rateLimitData = RateLimitService::getRateLimitStatus($user);

        $workspace = Workspace::with(['topics', 'user', 'allowedDomains'])
            ->where('user_id', $user->id)
            ->where('workspace_key_api', $workspace_key_api)
            ->firstOrFail();

        if (!$workspace) {
            abort(404);
        }

        return Inertia::render('Dashboard/ApiManagement/GeevApi/GeevApi', [
            'workspace' => $workspace->load(['topics', 'allowedDomains']),
            'rateLimitData' => $rateLimitData
        ]);
    }

        /**
     * Exibe o formulário de senha para workspace protegido
     */
    public function showPasswordForm($globalKey, $workspaceKey)
    {
        $user = User::where('global_key_api', $globalKey)->firstOrFail();
        $workspace = Workspace::where('workspace_key_api', $workspaceKey)
                            ->where('user_id', $user->id)
                            ->firstOrFail();
    
        // Se não tem senha, redireciona direto
        $dataKey = [
                'global_key_api' => $globalKey,
                'workspace_key_api' => $workspaceKey
        ];

        if (!$workspace->password && $workspace->type_view_workspace_id === 1) {
            return redirect()->route('workspace.shared-geev-studio.show', $dataKey);
        }
        
        if (!$workspace->password && $workspace->type_view_workspace_id === 2) {
            return redirect()->route('workspace.api-rest.show', $dataKey);
        }

        return Inertia::render('Dashboard/ApiManagement/Protected', [
            'workspace' => [
                'title' => $workspace->title,
            ],
            'user' => [
                'name' => $user->name,
            ],
            'verifyUrl' => route(
                'workspace.shared.verify-password',
                $dataKey
            ),
        ]);
    }

    /**
     * Verifica a senha do workspace
     */
    public function verifyPassword(Request $request, $globalHash, $workspaceKey)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $user = User::where('global_key_api', $globalHash)->firstOrFail();
        $workspace = Workspace::where('workspace_key_api', $workspaceKey)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        try {
            $decryptedPassword = Crypt::decryptString($workspace->password);
            
            if ($request->password === $decryptedPassword) {
                // Armazena na sessão que o usuário tem acesso
                session(["workspace_access_{$workspace->id}" => true]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Acesso permitido!',
                    'redirect' => route('workspace.shared-geev-studio.show', [
                        'global_key_api' => $globalHash,
                        'workspace_key_api' => $workspaceHash
                    ])
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta. Tente novamente.'
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar senha.'
            ], 500);
        }
    }

    /**
     * Middleware para verificar acesso ao workspace
     */
    public static function checkAccess($workspace)
    {
        // Se não tem senha, acesso liberado
        if (!$workspace->password) {
            return true;
        }

        // Verificar se a sessão existe e é true
        $sessionKey = "workspace_access_{$workspace->id}";
        
        \Log::debug('CheckAccess Session Check:', [
            'session_key' => $sessionKey,
            'session_exists' => session()->has($sessionKey),
            'session_value' => session($sessionKey),
            'session_all' => array_keys(session()->all())
        ]);
        
        return session()->has($sessionKey) && session($sessionKey) === true;
    }

    public function getPermissions(Workspace $workspace)
    {
        $user = Auth::user();
        
        if ($workspace->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $permissions = $workspace->apiPermissions->mapWithKeys(function ($permission) {
            return [$permission->endpoint => $permission->allowed_methods];
        });

        return response()->json([
            'permissions' => $permissions,
            'user_plan' => $user->getPlan()->name
        ]);
    }

    public function updatePermissions(Request $request, Workspace $workspace)
    {
        $user = Auth::user();
        
        // Verificar se usuário tem permissão
        if ($workspace->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $plan = $user->getPlan();
        
        // Verificar se plano permite configuração granular
        if (in_array($plan->name, ['free', 'start'])) {
            return response()->json([
                'error' => 'Plan limitation',
                'message' => 'Method permission configuration is available only for Pro and Premium plans'
            ], 403);
        }

        $request->validate([
            'endpoint' => 'required|in:workspace,topics,fields',
            'methods' => 'required|array',
            'methods.*' => 'in:GET,POST,PUT,PATCH,DELETE'
        ]);

        $workspace->updatePermissions($request->endpoint, $request->methods);

        return response()->json([
            'message' => 'Permissions updated successfully',
            'permissions' => [
                'endpoint' => $request->endpoint,
                'allowed_methods' => $request->methods
            ]
        ]);
    }

    /**
     * Obter estatísticas reais da API
     */
    public function getApiStatistics($global_key_api, $workspace_key_api)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('global_key_api', $global_key_api)->firstOrFail();
        $workspace = Workspace::where('workspace_key_api', $workspace_key_api)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
            // Usar dados reais
        $rateLimitInfo = RateLimitService::getRateLimitStatus($user);
        $workspaceStats = $this->getWorkspaceStatistics($workspace);
        $apiUsage = ApiStatisticsService::getRealApiUsageStatistics($workspace);
        $performanceMetrics = ApiStatisticsService::getPerformanceMetricsSimple($workspace);
        $usageByPeriod = ApiStatisticsService::getUsageByPeriod($workspace, 7);
        $methodsDistribution = ApiStatisticsService::getMethodsDistribution($workspace);
        $statusDistribution = ApiStatisticsService::getStatusDistribution($workspace);

        return response()->json([
            'success' => true,
            'workspace_stats' => $workspaceStats,
            'rate_limit_stats' => $this->formatRateLimitStats($rateLimitInfo),
            'api_usage' => $apiUsage,
            'performance_metrics' => $performanceMetrics,
            'usage_by_period' => $usageByPeriod,
            'methods_distribution' => $methodsDistribution,
            'status_distribution' => $statusDistribution,
            'plan_info' => [
                'name' => $rateLimitInfo['plan'],
                'limits' => $rateLimitInfo['limits']
            ]
        ]);
    }

    /**
     * Estatísticas do workspace
     */
    private function getWorkspaceStatistics(Workspace $workspace)
    {
        // Total de tópicos
        $totalTopics = $workspace->topics()->count();

        // Busca todos os topics com suas estruturas e fields
        $topics = $workspace->topics()
            ->with('structure.fields')
            ->get();

        // Total de campos existentes nas estruturas dos tópicos
        $totalFields = $topics->sum(function ($topic) {
            return $topic->structure?->fields->count() ?? 0;
        });

        // Campos visíveis (is_visible = true)
        // Se sua tabela structure_fields tiver essa coluna
        $visibleFields = $topics->sum(function ($topic) {
            return $topic->structure?->fields
                ->where('is_visible', true)
                ->count() ?? 0;
        });

        return [
            'total_topics'     => $totalTopics,
            'total_fields'     => $totalFields,
            'visible_fields'   => $visibleFields,
            'last_updated'     => $workspace->updated_at->toISOString(),
            'created_at'       => $workspace->created_at->toISOString(),
        ];
    }


    /**
     * Formatar estatísticas de rate limit
     */
    private function formatRateLimitStats(array $rateLimitInfo)
    {
        return [
            'minute' => [
                'limit' => $rateLimitInfo['limits']['per_minute'],
                'remaining' => $rateLimitInfo['current_usage']['minute']['remaining'],
                'reset_in' => $rateLimitInfo['current_usage']['minute']['available_in']
            ],
            'hour' => [
                'limit' => $rateLimitInfo['limits']['per_hour'],
                'remaining' => $rateLimitInfo['current_usage']['hour']['remaining'],
                'reset_in' => $rateLimitInfo['current_usage']['hour']['available_in']
            ],
            'day' => [
                'limit' => $rateLimitInfo['limits']['per_day'],
                'remaining' => $rateLimitInfo['current_usage']['hour']['remaining'],
                'reset_in' => $rateLimitInfo['current_usage']['hour']['available_in']
            ]
        ];
    }

    /**
     * Endpoint para estatísticas detalhadas por endpoint
     */
    public function getEndpointStatistics($global_key_api, $workspace_key_api)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('global_key_api', $global_key_api)->firstOrFail();
        $workspace = Workspace::where('workspace_key_api', $workspace_key_api)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        $endpointStats = ApiStatisticsService::getEndpointStatistics($workspace);

        return response()->json([
            'success' => true,
            'endpoints' => $endpointStats
        ]);
    }
}
