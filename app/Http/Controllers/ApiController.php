<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Topic;
use App\Models\User;
use App\Models\Workspace;
use App\Models\ApiRequestLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class ApiController extends Controller
{
    // Fornecer as hashes para garantir o tokenJWT
    public function getTokenByHashes(Request $request)
    {
        // Rate limiting específico para endpoint de autenticação
        $authLimitKey = 'auth_attempts:' . $request->ip();
        if (!RateLimiter::attempt($authLimitKey, 5, function() {}, 300)) { // 5 tentativas a cada 5 minutos
            return response()->json([
                'error' => 'Too many authentication attempts',
                'message' => 'Please try again in 5 minutes'
            ], 429);
        }

        $auth = auth('api');
        $globalHash = $request->input('global_hash_api');

        if (!$globalHash) {
            return response()->json(['error' => 'Global hash not provided.'], 400);
        }

        // Consulta do usuario no banco
        $user = User::where(['global_hash_api' => $globalHash])->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid hashes.'], 401);
        }

        // Verificar se o usuário tem acesso à API
        $plan = $user->getPlan();
        if (!$plan || !$plan->can_use_api) {
            return response()->json([
                'error' => 'API access denied',
                'message' => 'Your plan does not include API access'
            ], 403);
        }

        try {
            $token = $auth->fromUser($user);
            return response()->json([
                'status' => 'success',
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'expires_in' => $auth->factory()->getTTL() * 60,
                    'rate_limits' => [
                        'per_minute' => $plan->api_requests_per_minute,
                        'per_hour' => $plan->api_requests_per_hour,
                        'per_day' => $plan->api_requests_per_day
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getVisibleWorkspaceData(string $workspaceId)
    {
        try {
            $user = Auth::user();
            $plan = $user->getPlan();

            // Verificar rate limits antes de processar
            $rateLimitKey = 'workspace_request:' . $user->id;
            if (!RateLimiter::attempt($rateLimitKey . ':minute', $plan->api_requests_per_minute ?? 60, function() {}, 60)) {
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
                        'fields' => $topic->fields->map(function($field) {
                            return [
                                'id' => $field->id,
                                'key' => $field->key_name,
                                'value' => $field->value,
                                'visibility' => (bool) $field->is_visible,
                                'order' => $field->order,
                                'metadata' => [
                                    'created' => $field->created_at->toISOString(),
                                    'updated' => $field->updated_at->toISOString()
                                ]
                            ];
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

            return response()->json($data, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Workspace não encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }  

    public function getWorkspaceData(string $workspaceId)
    {
        try {
            $user = Auth::user();
            $plan = $user->getPlan();

            // Rate limiting específico para este endpoint
            $rateLimitKey = 'workspace_data:' . $user->id;
            if (!RateLimiter::attempt($rateLimitKey . ':minute', $plan->api_requests_per_minute ?? 60, function() {}, 60)) {
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

            return response()->json($response, 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Workspace não encontrado ou você não tem permissão'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar dados: ' . $e->getMessage()
            ], 500);
        }
    }

    // Método para verificar os limites atuais
    public function getRateLimitStatus(Request $request)
    {
        $user = Auth::user();
        $plan = $user->getPlan();

        return response()->json([
            'plan' => $plan->name,
            'limits' => [
                'per_minute' => $plan->api_requests_per_minute,
                'per_hour' => $plan->api_requests_per_hour,
                'per_day' => $plan->api_requests_per_day
            ],
            'current_usage' => [
                'minute' => [
                    'remaining' => RateLimiter::remaining('user_api:' . $user->id . ':minute', $plan->api_requests_per_minute),
                    'available_in' => RateLimiter::availableIn('user_api:' . $user->id . ':minute')
                ],
                'hour' => [
                    'remaining' => RateLimiter::remaining('user_api:' . $user->id . ':hour', $plan->api_requests_per_hour),
                    'available_in' => RateLimiter::availableIn('user_api:' . $user->id . ':hour')
                ]
            ]
        ]);
    }
}