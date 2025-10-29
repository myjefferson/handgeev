<?php

namespace App\Http\Controllers\Api;

use App\Models\Workspace;
use App\Models\WorkspaceAllowedDomain;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class WorkspaceApiController extends Controller
{
    public function show($workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            $workspace = Workspace::with(['user', 'typeWorkspace', 'topics.fields' => function($query) {
                $query->where('is_visible', true)->orderBy('order');
            }])
            ->where('id', $workspaceId)
            ->where('user_id', $user->id)
            ->firstOrFail();

            $plan = $user->getPlan();

            // Rate limiting
            $rateLimitKey = 'workspace_show:' . $user->id;
            if (!RateLimiter::attempt($rateLimitKey . ':minute', $plan->api_requests_per_minute ?? 60, function() {}, 60)) {
                $this->logApiRequest($user, $workspace, $startTime, 429, 'RATE_LIMIT_EXCEEDED');
                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Too many workspace requests'
                ], 429);
            }

            // Verificar se API está habilitada
            if (!$workspace->api_enabled) {
                $this->logApiRequest($user, $workspace, $startTime, 403, 'API_DISABLED');
                return response()->json([
                    'error' => 'API disabled',
                    'message' => 'Workspace API is currently disabled'
                ], 403);
            }

            $data = [
                'metadata' => [
                    'version' => '1.0',
                    'generated_at' => now()->toISOString(),
                    'workspace_id' => $workspace->id,
                    'rate_limits' => [
                        'remaining_minute' => RateLimiter::remaining($rateLimitKey . ':minute', $plan->api_requests_per_minute ?? 60),
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
                    'api_enabled' => $workspace->api_enabled,
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
                        'fields' => $topic->fields
                        ->filter(function($field) {
                            return !empty($field->key_name) && is_string($field->key_name);
                        })
                        ->mapWithKeys(function($field) {
                            $key = trim($field->key_name);
                            return [$key => [
                                'id' => $field->id,
                                'value' => $field->value,
                                'type' => $field->type,
                                'order' => $field->order,
                                'created_at' => $field->created_at->toISOString(),
                                'updated_at' => $field->updated_at->toISOString()
                            ]];
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

            $response = response()->json($data);
            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json([
                'error' => 'Workspace not found',
                'message' => 'The requested workspace does not exist or you do not have permission'
            ], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, $workspace ?? null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json([
                'error' => 'Internal server error',
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
    }

    public function stats($workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $workspace = Workspace::with(['topics.fields'])
                ->where('id', $workspaceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $stats = [
                'workspace_id' => $workspace->id,
                'workspace_title' => $workspace->title,
                'totals' => [
                    'topics' => $workspace->topics->count(),
                    'fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->count();
                    }),
                    'visible_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->where('is_visible', true)->count();
                    })
                ],
                'topics_breakdown' => $workspace->topics->map(function($topic) {
                    return [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'fields_count' => $topic->fields->count(),
                        'visible_fields_count' => $topic->fields->where('is_visible', true)->count(),
                        'order' => $topic->order
                    ];
                }),
                'fields_by_type' => $workspace->topics->flatMap(function($topic) {
                    return $topic->fields;
                })->groupBy('type')->map(function($fields) {
                    return $fields->count();
                })
            ];

            $response = response()->json([
                'metadata' => [
                    'generated_at' => now()->toISOString()
                ],
                'statistics' => $stats
            ]);

            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json(['error' => 'Workspace not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, $workspace ?? null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function update(Request $request, $workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $workspace = Workspace::where('id', $workspaceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:100',
                'description' => 'nullable|string|max:500',
                'is_published' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                $this->logApiRequest($user, $workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }

            $workspace->update($validator->validated());

            $response = response()->json([
                'message' => 'Workspace updated successfully',
                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'description' => $workspace->description,
                    'is_published' => $workspace->is_published,
                    'updated_at' => $workspace->updated_at->toISOString()
                ]
            ]);

            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json(['error' => 'Workspace not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, $workspace ?? null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function updateSettings(Request $request, $workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            $plan = $user->getPlan();

            $workspace = Workspace::where('id', $workspaceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'api_enabled' => 'sometimes|boolean',
                'allowed_domains' => 'sometimes|array',
                'allowed_domains.*' => 'string|max:255'
            ]);

            if ($validator->fails()) {
                $this->logApiRequest($user, $workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            if (isset($data['api_enabled'])) {
                $workspace->api_enabled = $data['api_enabled'];
            }

            // Atualizar domínios permitidos se fornecido
            if (isset($data['allowed_domains'])) {
                $maxDomains = $plan->max_domains ?? 1;
                
                if (count($data['allowed_domains']) > $maxDomains) {
                    $this->logApiRequest($user, $workspace, $startTime, 403, 'DOMAIN_LIMIT_EXCEEDED');
                    return response()->json([
                        'error' => 'Domain limit exceeded',
                        'message' => "Your plan allows maximum {$maxDomains} domains",
                        'current_plan' => $plan->name,
                        'max_domains' => $maxDomains
                    ], 403);
                }

                // Remover domínios existentes
                $workspace->allowedDomains()->delete();

                // Adicionar novos domínios
                foreach ($data['allowed_domains'] as $domain) {
                    WorkspaceAllowedDomain::create([
                        'workspace_id' => $workspace->id,
                        'domain' => $domain,
                        'is_active' => true
                    ]);
                }
            }

            $workspace->save();

            $response = response()->json([
                'message' => 'Workspace settings updated successfully',
                'settings' => [
                    'api_enabled' => $workspace->api_enabled,
                    'allowed_domains' => $workspace->allowedDomains->pluck('domain'),
                    'workspace_key_api' => $workspace->workspace_key_api
                ]
            ]);

            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json(['error' => 'Workspace not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, $workspace ?? null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function manageDomains(Request $request, $workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            $plan = $user->getPlan();

            $workspace = Workspace::where('id', $workspaceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'domains' => 'required|array',
                'domains.*' => 'string|max:255|regex:/^(\*\.)?[a-zA-Z0-9*][a-zA-Z0-9-.*]*\.[a-zA-Z]{2,}$/'
            ]);

            if ($validator->fails()) {
                $this->logApiRequest($user, $workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }

            $maxDomains = $plan->max_domains ?? 1;
            
            if (count($request->domains) > $maxDomains) {
                $this->logApiRequest($user, $workspace, $startTime, 403, 'DOMAIN_LIMIT_EXCEEDED');
                return response()->json([
                    'error' => 'Domain limit exceeded',
                    'message' => "Your plan allows maximum {$maxDomains} domains",
                    'current_plan' => $plan->name,
                    'max_domains' => $maxDomains
                ], 403);
            }

            // Remover domínios existentes
            $workspace->allowedDomains()->delete();

            // Adicionar novos domínios
            foreach ($request->domains as $domain) {
                WorkspaceAllowedDomain::create([
                    'workspace_id' => $workspace->id,
                    'domain' => $domain,
                    'is_active' => true
                ]);
            }

            $response = response()->json([
                'message' => 'Domains updated successfully',
                'domains' => $workspace->allowedDomains->pluck('domain'),
                'count' => $workspace->allowedDomains->count(),
                'max_allowed' => $maxDomains
            ]);

            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json(['error' => 'Workspace not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, $workspace ?? null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function getPermissions(Workspace $workspace)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            
            if ($workspace->user_id !== $user->id) {
                $this->logApiRequest($user, $workspace, $startTime, 403, 'UNAUTHORIZED');
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $permissions = $workspace->apiPermissions->mapWithKeys(function ($permission) {
                return [$permission->endpoint => $permission->allowed_methods];
            });

            $response = response()->json([
                'permissions' => $permissions,
                'user_plan' => $user->getPlan()->name
            ]);

            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, $workspace ?? null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
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

            // Log adicional para debug
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