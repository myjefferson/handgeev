<?php

namespace App\Http\Controllers;

use App\Services\RateLimitService;
use App\Services\ApiStatisticsService;
use Illuminate\Support\Facades\Crypt;
use App\Models\Workspace;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class WorkspaceSharedController extends Controller
{
    // Rota para a interface de visualização compartilhada
    public function showInterfaceApi($global_key_api, $workspace_key_api)
    {
        // Encontrar o usuário pelo global_key_api
        $user = User::where('global_key_api', $global_key_api)->firstOrFail();
        
        // Buscar workspace
        $workspace = Workspace::with(['topics.fields' => function($query) {
                $query->orderBy('order', 'asc')->where('is_visible', true);
            }])
            ->where('user_id', $user->id)
            ->where('workspace_key_api', $workspace_key_api)
            ->firstOrFail();

        if (!$workspace->is_published) {
            abort(403);
        }
        
        if ($workspace->type_view_workspace_id != 1) {
            abort(404);
        }

        $rateLimitInfo = RateLimitService::getRateLimitStatus($user);

        return view('pages.dashboard.workspace-shared.interface-api', compact(
            'user', 
            'workspace', 
            'rateLimitInfo',
            'global_key_api', 
            'workspace_key_api'
        ));
    }

    // Seu WorkspaceController - ADICIONAR ESTE MÉTODO
    public function showApiRest($global_key_api, $workspace_key_api)
    {
        if(Auth::check()){
            // Encontrar o usuário pelo global_key_api
            $user = User::where('global_key_api', $global_key_api)->firstOrFail();

            $workspace = Workspace::with(['topics.fields'])
                ->where('user_id', $user->id)
                ->where('workspace_key_api', $workspace_key_api)
                ->firstOrFail();
                
            if(!$workspace){
                abort(404);
            }

            $apiKey = $workspace->workspace_key_api;

            return view('pages.dashboard.workspace-shared.rest-api', compact(
                'workspace',
                'apiKey',
            ));
        }
        return abort(403);
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
            return redirect()->route('workspace.shared-interface-api.show', $dataKey);
        }
        
        if (!$workspace->password && $workspace->type_view_workspace_id === 2) {
            return redirect()->route('workspace.api-rest.show', $dataKey);
        }

        return view('pages.dashboard.workspace-shared.workspace-password', compact('workspace', 'user', 'globalHash', 'workspaceKey'));
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
                    'redirect' => route('workspace.shared-interface-api.show', [
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
     * Estatísticas do workspace (mantém igual)
     */
    private function getWorkspaceStatistics(Workspace $workspace)
    {
        return [
            'total_topics' => $workspace->topics()->count(),
            'total_fields' => $workspace->topics()->withCount('fields')->get()->sum('fields_count'),
            'visible_fields' => $workspace->topics()->whereHas('fields', function($q) {
                $q->where('is_visible', true);
            })->withCount(['fields' => function($q) {
                $q->where('is_visible', true);
            }])->get()->sum('fields_count'),
            'last_updated' => $workspace->updated_at->toISOString(),
            'created_at' => $workspace->created_at->toISOString(),
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
