<?php

namespace App\Http\Controllers\Api;

use App\Services\RateLimitService;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Topic;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class ApiController extends Controller
{
    public function getTokenByLogin(Request $request)
    {
        $startTime = microtime(true);
        
        // Rate limiting específico para endpoint de autenticação
        $authLimitKey = 'auth_attempts:' . $request->ip();
        if (!RateLimiter::attempt($authLimitKey, 5, function() {}, 300)) {
            $this->logApiRequest(null, null, $startTime, 429, 'AUTH_RATE_LIMIT_EXCEEDED');
            return response()->json([
                'error' => 'Too many authentication attempts',
                'message' => 'Please try again in 5 minutes'
            ], 429);
        }

        // Validação dos campos de entrada
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        
        // Tentativa de autenticação
        if (!auth()->attempt($credentials)) {
            $this->logApiRequest(null, null, $startTime, 401, 'INVALID_CREDENTIALS');
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }

        // Busca o usuário autenticado
        $user = auth()->user();

        // Verificar se o usuário tem acesso à API
        $plan = $user->getPlan();
        if (!$plan || !$plan->can_use_api) {
            $this->logApiRequest($user, null, $startTime, 403, 'API_ACCESS_DENIED');
            return response()->json([
                'error' => 'API access denied',
                'message' => 'Your plan does not include API access'
            ], 403);
        }

        try {
            $token = auth('api')->login($user);
            $response = response()->json([
                'status' => 'success',
                'authorization' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                    'rate_limits' => [
                        'per_minute' => $plan->api_requests_per_minute,
                    ]
                ]
            ]);

            $this->logApiRequest($user, null, $startTime, 200, 'AUTH_SUCCESS');
            return $response;

        } catch (\Exception $e) {
            $this->logApiRequest($user, null, $startTime, 500, 'TOKEN_GENERATION_FAILED');
            return response()->json([
                'error' => 'Token generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getVisibleWorkspaceData(string $workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            $plan = $user->getPlan();

            // Verificar rate limits antes de processar
            $rateLimitKey = 'workspace_request:' . $user->id;
            if (!RateLimiter::attempt($rateLimitKey . ':minute', $plan->api_requests_per_minute ?? 60, function() {}, 60)) {
                $this->logApiRequest($user, null, $startTime, 429, 'RATE_LIMIT_EXCEEDED');
                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Too many workspace requests. Please try again in a minute.',
                    'limits' => [
                        'per_minute' => $plan->api_requests_per_minute,
                        'per_hour' => $plan->api_requests_per_hour,
                        'per_day' => $plan->api_requests_per_day
                    ]
                ], 429);
            }

            $workspace = Workspace::with(['user', 'typeWorkspace', 'topics.fields'])
                ->where('id', $workspaceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Transformar para formato JSON estruturado
            $data = [
                'metadata' => [
                    'version' => '1.0',
                    'generated_at' => now()->toISOString(),
                    'workspace_id' => $workspace->id,
                    'rate_limits' => [
                        'remaining_minute' => RateLimiter::remaining($rateLimitKey . ':minute', $plan->api_requests_per_minute ?? 60),
                        'remaining_hour' => RateLimiter::remaining($rateLimitKey . ':hour', $plan->api_requests_per_hour ?? 1000),
                        'plan' => $plan->name
                    ]
                ],
                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'description' => $workspace->description ?? '',
                    'type' => $workspace->typeWorkspace->description,
                    'type_id' => $workspace->type_workspace_id,
                    'is_published' => $workspace->is_published,
                    'owner' => [
                        'id' => $workspace->user->id,
                        'name' => $workspace->user->name,
                        'email' => $workspace->user->email
                    ],
                    'dates' => [
                        'created' => $workspace->created_at->toISOString(),
                        'updated' => $workspace->updated_at->toISOString()
                    ]
                ],
                'topics' => $workspace->topics->map(function($topic) {
                    return [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'order' => $topic->order,
                        'fields_count' => $topic->fields->count(),
                        'fields' => $topic->fields->mapWithKeys(function($field) {
                            // Usando key_name como chave e value como valor
                            return [$field->key_name => $field->value];
                        })
                    ];
                }),
                'statistics' => [
                    'total_topics' => $workspace->topics->count(),
                    'total_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->count();
                    }),
                    'visible_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->where('is_visible', true)->count();
                    })
                ]
            ];

            $response = response()->json($data, 200);
            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json(['error' => 'Workspace não encontrado'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    public function getWorkspaceData(string $workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            $plan = $user->getPlan();

            // Rate limiting específico para este endpoint
            $rateLimitKey = 'workspace_data:' . $user->id;
            if (!RateLimiter::attempt($rateLimitKey . ':minute', $plan->api_requests_per_minute ?? 60, function() {}, 60)) {
                $this->logApiRequest($user, null, $startTime, 429, 'RATE_LIMIT_EXCEEDED');
                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Too many requests for workspace data.',
                    'retry_after' => RateLimiter::availableIn($rateLimitKey . ':minute')
                ], 429);
            }

            // Verificar se o workspace existe e pertence ao usuário
            $workspace = Workspace::with(['user', 'typeWorkspace'])
                ->where('id', $workspaceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Carregar tópicos apenas com campos visíveis
            $topics = Topic::with(['fields' => function($query) {
                    $query->where('is_visible', 1)
                        ->orderBy('order');
                }])
                ->where('workspace_id', $workspaceId)
                ->orderBy('order')
                ->get();

            // Estrutura da resposta
            $response = [
                'metadata' => [
                    'rate_limits' => [
                        'remaining' => RateLimiter::remaining($rateLimitKey . ':minute', $plan->api_requests_per_minute ?? 60),
                        'plan' => $plan->name
                    ]
                ],
                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'type' => $workspace->typeWorkspace->description,
                    'is_published' => $workspace->is_published,
                    'created_at' => $workspace->created_at,
                    'updated_at' => $workspace->updated_at
                ],
                'topics' => $topics->map(function($topic) {
                    return [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'order' => $topic->order,
                        'fields' => $topic->fields->mapWithKeys(function($field) {
                            return [$field->key_name => $field->value];
                        })
                    ];
                }),
                'statistics' => [
                    'total_topics' => $workspace->topics->count(),
                    'total_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->count();
                    }),
                    'visible_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->where('is_visible', true)->count();
                    })
                ]
            ];

            $apiResponse = response()->json($response, 200);
            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $apiResponse;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json([
                'error' => 'Workspace não encontrado ou você não tem permissão'
            ], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json([
                'error' => 'Erro ao carregar dados: ' . $e->getMessage()
            ], 500);
        }
    }

    // Rota para a Geev API compartilhada
    public function sharedApi($global_key_api, $workspace_key_api)
    {
        $startTime = microtime(true);
        
        try {
            // Encontrar o usuário pelo global_key_api
            $user = User::where('global_key_api', $global_key_api)->firstOrFail();
            
            // Buscar workspace com campos visíveis apenas
            $workspace = Workspace::with(['topics.fields' => function($query) {
                    $query->where('is_visible', true)
                        ->orderBy('order', 'asc');
                }])
                ->where('user_id', $user->id)
                ->where('workspace_key_api', $workspace_key_api)
                ->firstOrFail();

            // Estrutura da resposta
            $response = [
                'metadata' => [
                    'version' => '1.0',
                    'generated_at' => now()->toISOString(),
                    'workspace_id' => $workspace->id,
                    'access_type' => 'public_shared'
                ],
                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'description' => $workspace->description,
                    'is_published' => $workspace->is_published,
                    'owner' => [
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'dates' => [
                        'created' => $workspace->created_at->toISOString(),
                        'updated' => $workspace->updated_at->toISOString()
                    ]
                ],
                'topics' => $workspace->topics->map(function($topic) {
                    return [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'order' => $topic->order,
                        'fields_count' => $topic->fields->count(),
                        'fields' => $topic->fields->mapWithKeys(function($field) {
                        // Usando key_name como chave e value como valor
                        return [$field->key_name => $field->value];
                    })
                    ];
                }),
                'statistics' => [
                    'total_topics' => $workspace->topics->count(),
                    'total_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->count();
                    })
                ]
            ];

            $apiResponse = response()->json($response, 200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*'
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            $this->logApiRequest($user, $workspace, $startTime, 200, 'PUBLIC_API_SUCCESS');
            return $apiResponse;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest(null, null, $startTime, 404, 'PUBLIC_WORKSPACE_NOT_FOUND');
            return response()->json([
                'error' => 'Workspace não encontrado',
                'message' => 'O workspace solicitado não existe ou não está mais disponível'
            ], 404);
        } catch (\Exception $e) {
            $this->logApiRequest(null, null, $startTime, 500, 'PUBLIC_API_INTERNAL_ERROR');
            return response()->json([
                'error' => 'Erro interno',
                'message' => 'Ocorreu um erro ao processar sua requisição'
            ], 500);
        }
    }

    /**
     * Método para logging de requisições da API
     */
    private function logApiRequest($user, $workspace, $startTime, $statusCode, $statusMessage = '')
    {
        try {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000); // ms
            
            ApiRequestLog::create([
                'user_id' => $user?->id,
                'workspace_id' => $workspace?->id,
                'ip_address' => request()->ip(),
                'method' => request()->method(),
                'endpoint' => request()->path(),
                'response_code' => $statusCode,
                'response_time' => $responseTime,
                'user_agent' => request()->userAgent(),
            ]);

            \Log::debug('API Request Logged', [
                'user_id' => $user?->id,
                'workspace_id' => $workspace?->id,
                'endpoint' => request()->path(),
                'method' => request()->method(),
                'status_code' => $statusCode,
                'response_time' => $responseTime,
                'status_message' => $statusMessage
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to log API request: ' . $e->getMessage());
        }
    }
}