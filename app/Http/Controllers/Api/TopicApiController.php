<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\Workspace;
use App\Models\Structure;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TopicApiController extends Controller
{
    /**
     * Show topics by workspace
     * **/
    public function index($structureId)
    {
        dd('test');
        $startTime = microtime(true);
        $user = Auth::user(); // pode ser null
        $structure = null;

        try {
            $structure = Structure::with(['topics.records'])
                ->findOrFail($structureId);

            // Se quiser restringir acesso (opcional)
            if ($user && !$structure->canBeUsedBy($user)) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 403);
            }

            $topics = $structure->topics()
                ->orderBy('order')
                ->get();

            $responseData = [
                'metadata' => [
                    'structure_id' => $structure->id,
                    'structure_name' => $structure->name,
                    'total' => $topics->count(),
                    'generated_at' => now()->toISOString()
                ],
                'topics' => $topics->map(fn ($topic) => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'order' => $topic->order,
                    'records_count' => $topic->records?->count() ?? 0,
                    'created_at' => $topic->created_at?->toISOString(),
                    'updated_at' => $topic->updated_at?->toISOString(),
                ])
            ];

            $this->logApiRequest($user, null, $startTime, 200, 'SUCCESS');

            return response()->json($responseData);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            $this->logApiRequest($user, null, $startTime, 404, 'STRUCTURE_NOT_FOUND');

            return response()->json([
                'error' => 'Structure not found: '.$e->getMessage()
            ], 404);

        } catch (\Throwable $e) {

            report($e); // 🔥 essencial para debug real

            $this->logApiRequest($user, null, $startTime, 500, 'INTERNAL_ERROR');

            return response()->json([
                'error' => 'Internal server error: '.$e->getMessage()
            ], 500);
        }
    }


    /**
     * Show one topic
     * **/
    public function show($topicId)
    {
        $startTime = microtime(true);
        $user = Auth::user();
        $topic = null;

        try {
            $topic = Topic::with([
                'structure.fields',
                'records'
            ])->findOrFail($topicId);

            // 🔐 Controle de acesso via Structure
            if ($user && !$topic->structure->canBeUsedBy($user)) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 403);
            }

            $responseData = [
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'order' => $topic->order,
                    'structure' => [
                        'id' => $topic->structure->id,
                        'name' => $topic->structure->name,
                        'fields_count' => $topic->structure->fields->count(),
                    ],
                    'records_count' => $topic->records->count(),
                    'created_at' => $topic->created_at?->toISOString(),
                    'updated_at' => $topic->updated_at?->toISOString(),
                ]
            ];

            $this->logApiRequest($user, null, $startTime, 200, 'SUCCESS');

            return response()->json($responseData);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            $this->logApiRequest($user, null, $startTime, 404, 'TOPIC_NOT_FOUND');

            return response()->json([
                'error' => 'Topic not found'
            ], 404);

        } catch (\Throwable $e) {

            report($e); // 🔥 essencial

            $this->logApiRequest($user, null, $startTime, 500, 'INTERNAL_ERROR');

            return response()->json([
                'error' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Retorna dados completos dos tópicos com campos detalhados
     */
    private function getFullTopicsData($topics, $workspace = null)
    {
        // Carregar campos para todos os tópicos de uma vez (mais eficiente)
        $topics->load(['fields' => function($query) {
            $query->where('is_visible', true)->orderBy('order');
        }]);

        if($workspace){
            $fullData['metadata'] = [
                'workspace_id' => (int)$workspace->id,
                'workspace_title' => $workspace->title,
                'total_topics' => $topics->count(),
                'view_type' => 'simple',
                'generated_at' => now()->toISOString()
            ];
        }

        $fullData['topics'] = $topics->map(function($topic) {
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
                            'order' => $field->order
                        ]];
                    }),
                'created_at' => $topic->created_at->toISOString(),
                'updated_at' => $topic->updated_at->toISOString()
            ];
        });

        return $fullData;
    }

    /**
     * Retorna dados simplificados dos tópicos (apenas metadados)
     */
    private function getSimpleTopicsData($topics, $workspace = null)
    {
        if($workspace){
            $simpleData['metadata'] = [
                'workspace_id' => (int)$workspace->id,
                'workspace_title' => $workspace->title,
                'total_topics' => $topics->count(),
                'view_type' => 'simple',
                'generated_at' => now()->toISOString()
            ];
        }

        $simpleData['topics'] = $topics->map(function($topic) {
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
                        'order' => $field->order
                    ]];
                }),
            ];
        });

        return $simpleData;
    }

    public function store(Request $request, $structureId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            $plan = $user->getPlan();
            
            $structure = Structure::with(['workspace'])
                ->whereHas('workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($structureId);
            
            // Verificar limite de tópicos
            $currentTopics = $structure->topics()->count();
            if ($plan->topics > 0 && $currentTopics >= $plan->topics) {
                $this->logApiRequest($user, $structure->workspace, $startTime, 403, 'TOPIC_LIMIT_EXCEEDED');
                return response()->json([
                    'error' => 'Topic limit exceeded',
                    'message' => "Your plan allows maximum {$plan->topics} topics",
                    'current_plan' => $plan->name
                ], 403);
            }
            
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:200',
                'order' => 'sometimes|integer|min:0'
            ]);
            
            if ($validator->fails()) {
                $this->logApiRequest($user, $structure->workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }
            
            $topic = Topic::create([
                'structure_id' => $structureId,
                'title' => $request->title,
                'order' => $request->order ?? 0,
            ]);
            
            $response = response()->json([
                'message' => 'Topic created successfully',
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'order' => $topic->order,
                    'structure_id' => $topic->structure_id,
                    'created_at' => $topic->created_at->toISOString()
                ]
            ], 201);
            
            $this->logApiRequest($user, $structure->workspace, $startTime, 201, 'SUCCESS');
            return $response;
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest(Auth::user() ?? null, null, $startTime, 404, 'STRUCTURE_NOT_FOUND');
            return response()->json(['error' => 'Structure not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest(Auth::user() ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function update(Request $request, $topicId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $topic = Topic::whereHas('workspace', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($topicId);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:200',
                'order' => 'sometimes|integer|min:0'
            ]);

            if ($validator->fails()) {
                $this->logApiRequest($user, $topic->workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }

            $topic->update($validator->validated());

            $response = response()->json([
                'message' => 'Topic updated successfully',
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'order' => $topic->order,
                    'updated_at' => $topic->updated_at->toISOString()
                ]
            ]);

            $this->logApiRequest($user, $topic->workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'TOPIC_NOT_FOUND');
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function destroy($topicId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $topic = Topic::whereHas('workspace', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($topicId);

            $topic->delete();

            $response = response()->json([
                'message' => 'Topic deleted successfully',
                'deleted_topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title
                ]
            ]);

            $this->logApiRequest($user, $topic->workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'TOPIC_NOT_FOUND');
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
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