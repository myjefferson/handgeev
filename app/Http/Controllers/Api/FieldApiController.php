<?php

namespace App\Http\Controllers\Api;

use App\Models\Field;
use App\Models\Topic;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FieldApiController extends Controller
{
    public function index($topicId)
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
            ->findOrFail($topicId);

            $response = response()->json([
                'metadata' => [
                    'topic_id' => (int)$topicId,
                    'topic_title' => $topic->title,
                    'workspace_id' => $topic->workspace_id,
                    'total_fields' => $topic->fields->count(),
                    'visible_fields' => $topic->fields->where('is_visible', true)->count(),
                    'generated_at' => now()->toISOString()
                ],
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

    public function show($fieldId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $field = Field::with(['topic.workspace'])
                ->whereHas('topic.workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($fieldId);

            $response = response()->json([
                'field' => [
                    'id' => $field->id,
                    'key' => $field->key_name,
                    'value' => $field->value,
                    'type' => $field->type,
                    'is_visible' => (bool)$field->is_visible,
                    'order' => $field->order,
                    'topic' => [
                        'id' => $field->topic->id,
                        'title' => $field->topic->title,
                        'workspace_id' => $field->topic->workspace_id
                    ],
                    'created_at' => $field->created_at->toISOString(),
                    'updated_at' => $field->updated_at->toISOString()
                ]
            ]);

            $this->logApiRequest($user, $field->topic->workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'FIELD_NOT_FOUND');
            return response()->json(['error' => 'Field not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function store(Request $request, $topicId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            $plan = $user->getPlan();

            $topic = Topic::with('workspace')
                ->whereHas('workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($topicId);

            // Verificar limite de campos
            $currentFields = $topic->fields()->count();
            $workspaceFields = $topic->workspace->getFieldsCountAttribute();
            
            if ($plan->max_fields > 0 && $workspaceFields >= $plan->max_fields) {
                $this->logApiRequest($user, $topic->workspace, $startTime, 403, 'FIELD_LIMIT_EXCEEDED');
                return response()->json([
                    'error' => 'Field limit exceeded',
                    'message' => "Your plan allows maximum {$plan->max_fields} fields",
                    'current_plan' => $plan->name,
                    'max_fields' => $plan->max_fields,
                    'current_count' => $workspaceFields
                ], 403);
            }

            $validator = Validator::make($request->all(), Field::getValidationRules());

            if ($validator->fails()) {
                $this->logApiRequest($user, $topic->workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['topic_id'] = $topicId;
            $data['is_visible'] = $data['is_visible'] ?? true;

            // Validar valor baseado no tipo
            $field = new Field($data);
            if (!$field->validateValue($data['value'])) {
                $this->logApiRequest($user, $topic->workspace, $startTime, 422, 'INVALID_FIELD_VALUE');
                return response()->json([
                    'error' => 'Invalid value for field type',
                    'message' => "The value provided is not valid for type '{$data['type']}'"
                ], 422);
            }

            // Formatar valor
            $data['value'] = $field->formatValue($data['value']);

            $field = Field::create($data);

            $response = response()->json([
                'message' => 'Field created successfully',
                'field' => [
                    'id' => $field->id,
                    'key' => $field->key_name,
                    'value' => $field->value,
                    'type' => $field->type,
                    'is_visible' => (bool)$field->is_visible,
                    'order' => $field->order,
                    'topic_id' => $field->topic_id,
                    'created_at' => $field->created_at->toISOString()
                ]
            ], 201);

            $this->logApiRequest($user, $topic->workspace, $startTime, 201, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'TOPIC_NOT_FOUND');
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function update(Request $request, $fieldId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $field = Field::with(['topic.workspace'])
                ->whereHas('topic.workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($fieldId);

            $validator = Validator::make($request->all(), [
                'key_name' => 'sometimes|string|max:255',
                'value' => 'sometimes|string',
                'type' => 'sometimes|in:text,number,boolean,email,url,date,json',
                'is_visible' => 'sometimes|boolean',
                'order' => 'sometimes|integer|min:0'
            ]);

            if ($validator->fails()) {
                $this->logApiRequest($user, $field->topic->workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Se tipo ou valor foram alterados, validar
            if (isset($data['type']) || isset($data['value'])) {
                $fieldType = $data['type'] ?? $field->type;
                $fieldValue = $data['value'] ?? $field->value;
                
                $tempField = new Field(['type' => $fieldType]);
                if (!$tempField->validateValue($fieldValue)) {
                    $this->logApiRequest($user, $field->topic->workspace, $startTime, 422, 'INVALID_FIELD_VALUE');
                    return response()->json([
                        'error' => 'Invalid value for field type',
                        'message' => "The value provided is not valid for type '{$fieldType}'"
                    ], 422);
                }

                // Formatar valor se necessário
                if (isset($data['value'])) {
                    $data['value'] = $tempField->formatValue($fieldValue);
                }
            }

            $field->update($data);

            $response = response()->json([
                'message' => 'Field updated successfully',
                'field' => [
                    'id' => $field->id,
                    'key' => $field->key_name,
                    'value' => $field->value,
                    'type' => $field->type,
                    'is_visible' => (bool)$field->is_visible,
                    'order' => $field->order,
                    'updated_at' => $field->updated_at->toISOString()
                ]
            ]);

            $this->logApiRequest($user, $field->topic->workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'FIELD_NOT_FOUND');
            return response()->json(['error' => 'Field not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function updateVisibility(Request $request, $fieldId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $field = Field::with(['topic.workspace'])
                ->whereHas('topic.workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($fieldId);

            $validator = Validator::make($request->all(), [
                'is_visible' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                $this->logApiRequest($user, $field->topic->workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }

            $field->update(['is_visible' => $request->is_visible]);

            $response = response()->json([
                'message' => 'Field visibility updated successfully',
                'field' => [
                    'id' => $field->id,
                    'key' => $field->key_name,
                    'is_visible' => (bool)$field->is_visible,
                    'updated_at' => $field->updated_at->toISOString()
                ]
            ]);

            $this->logApiRequest($user, $field->topic->workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'FIELD_NOT_FOUND');
            return response()->json(['error' => 'Field not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function destroy($fieldId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();

            $field = Field::with(['topic.workspace'])
                ->whereHas('topic.workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($fieldId);

            $field->delete();

            $response = response()->json([
                'message' => 'Field deleted successfully',
                'deleted_field' => [
                    'id' => $field->id,
                    'key' => $field->key_name
                ]
            ]);

            $this->logApiRequest($user, $field->topic->workspace, $startTime, 200, 'SUCCESS');
            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest($user ?? null, null, $startTime, 404, 'FIELD_NOT_FOUND');
            return response()->json(['error' => 'Field not found'], 404);
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