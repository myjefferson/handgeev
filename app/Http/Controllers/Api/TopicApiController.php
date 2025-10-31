<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\Workspace;
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
    public function index($workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $workspace = Workspace::where('id', $workspaceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $topics = Topic::where('workspace_id', $workspaceId)
                ->orderBy('order')
                ->get();

            // Verificar se é uma visualização completa
            $viewType = request()->get('view');

            if ($viewType === 'full') {
                $responseData = $this->getFullTopicsData($topics, $workspace);
            } else {
                $responseData = $this->getSimpleTopicsData($topics, $workspace);
            }

            $response = response()->json($responseData);
            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json(['error' => 'Workspace not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }


    /**
     * Show one topic
     * **/
    public function show($topicId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $topic = Topic::with(['workspace', 'fields' => function($query) {
                $query->orderBy('order');
            }])
            ->whereHas('workspace', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($topicId)->get();

            // Verificar se é uma visualização completa
            $viewType = request()->get('view');

            if ($viewType === 'full') {
                $responseData = $this->getFullTopicsData($topic);
            } else {
                $responseData = $this->getSimpleTopicsData($topic);
            }

            $response = response()->json($responseData);
            $this->logApiRequest($user, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'TOPIC_NOT_FOUND');
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'.$e], 500);
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

    public function store(Request $request, $workspaceId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            $plan = $user->getPlan();

            $workspace = Workspace::where('id', $workspaceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Verificar limite de tópicos
            $currentTopics = $workspace->topics()->count();
            if ($plan->max_topics > 0 && $currentTopics >= $plan->max_topics) {
                $this->logApiRequest($user, $workspace, $startTime, 403, 'TOPIC_LIMIT_EXCEEDED');
                return response()->json([
                    'error' => 'Topic limit exceeded',
                    'message' => "Your plan allows maximum {$plan->max_topics} topics",
                    'current_plan' => $plan->name,
                    'max_topics' => $plan->max_topics,
                    'current_count' => $currentTopics
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:200',
                'order' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                $this->logApiRequest($user, $workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }

            $topic = Topic::create([
                'workspace_id' => $workspaceId,
                'title' => $request->title,
                'order' => $request->order
            ]);

            $response = response()->json([
                'message' => 'Topic created successfully',
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'order' => $topic->order,
                    'workspace_id' => $topic->workspace_id,
                    'created_at' => $topic->created_at->toISOString()
                ]
            ], 201);

            $this->logApiRequest($user, $workspace, $startTime, 201, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');
            return response()->json(['error' => 'Workspace not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
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