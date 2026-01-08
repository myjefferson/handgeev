<?php

namespace App\Http\Controllers\Api;

use App\Models\TopicRecord;
use App\Models\RecordFieldValue;
use App\Models\Topic;
use App\Models\Structure;
use App\Models\Workspace;
use App\Models\User;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RecordApiController extends Controller
{
    /**
     * 📄 GET /topics/{topic}/records - Listar registros de um tópico
     */
    public function index(int $topicId)
    {
        $startTime = microtime(true);
        $user = Auth::user();

        try {
            $topic = Topic::with([
                    'structure.fields'
                ])
                ->findOrFail($topicId);

            // 🔐 Se NÃO tiver estrutura, não tem records estruturados
            if (!$topic->structure) {
                return response()->json([
                    'metadata' => [
                        'topic_id' => $topic->id,
                        'topic_title' => $topic->title,
                        'total_records' => 0,
                        'generated_at' => now()->toISOString()
                    ],
                    'records' => []
                ]);
            }

            // 🔐 Controle de acesso
            if ($user && !$topic->structure->canBeUsedBy($user)) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 403);
            }

            $perPage = request()->integer('per_page', 20);

            $records = TopicRecord::where('topic_id', $topic->id)
                ->with(['fieldValues.structureField'])
                ->orderBy('order')
                ->paginate($perPage);

            $structureFields = $topic->structure->fields ?? collect();

            $responseData = [
                'metadata' => [
                    'topic_id'        => $topic->id,
                    'topic_title'     => $topic->title,
                    'structure_id'    => $topic->structure->id,
                    'structure_name'  => $topic->structure->name,
                    'total_records'   => $records->total(),
                    'current_page'    => $records->currentPage(),
                    'per_page'        => $records->perPage(),
                    'last_page'       => $records->lastPage(),
                    'generated_at'    => now()->toISOString()
                ],
                'records' => $records->map(fn ($record) =>
                    $this->formatRecordResponse($record, $structureFields)
                )->values()
            ];

            return response()->json($responseData);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {

            return response()->json([
                'error' => 'Topic not found'
            ], 404);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'error' => 'Internal server error',
                'message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    
    /**
     * 📄 GET /records/{record} - Mostrar um registro específico
     */
    public function show($recordId)
    {
        $startTime = microtime(true);

        try {
            $user = Auth::user();

            $record = TopicRecord::with([
                    'topic.workspace',          // ✅ workspace vem do topic
                    'topic.structure.fields',
                    'fieldValues.structureField'
                ])
                ->whereHas('topic.workspace', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($recordId);

            $structureFields = $record->topic->structure?->fields ?? collect();

            $responseData = $this->formatRecordResponse(
                $record,
                $structureFields
            );

            $this->logApiRequest(
                $user,
                $record->topic->workspace, // ✅ correto
                $startTime,
                200,
                'SUCCESS'
            );

            return response()->json($responseData);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {

            $this->logApiRequest(Auth::user(), null, $startTime, 404, 'RECORD_NOT_FOUND');

            return response()->json([
                'error' => 'Record not found'
            ], 404);

        } catch (\Throwable $e) {

            report($e);

            $this->logApiRequest(Auth::user(), null, $startTime, 500, 'INTERNAL_ERROR');

            return response()->json([
                'error' => 'Internal server error',
                'message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * ➕ POST /topics/{topic}/records - Criar um novo registro
     */
    public function store(Request $request, $topicId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            
            $topic = Topic::with(['structure.fields', 'structure.workspace'])
                ->whereHas('structure.workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($topicId);
            
            // Validar dados baseados nos campos da estrutura
            $validator = $this->validateRecordData($request->all(), $topic->structure->fields);
            
            if ($validator->fails()) {
                $this->logApiRequest($user, $topic->structure->workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }
            
            // Criar o registro
            $record = TopicRecord::create([
                'topic_id' => $topicId,
                'order' => $request->get('order', 0)
            ]);
            
            // Criar valores dos campos
            $this->createFieldValues($record, $request->all(), $topic->structure->fields);
            
            // Recarregar com relacionamentos
            $record->load(['fieldValues.structureField']);
            
            $responseData = $this->formatRecordResponse($record, $topic->structure->fields);
            
            $response = response()->json([
                'message' => 'Record created successfully',
                'record' => $responseData
            ], 201);
            
            $this->logApiRequest($user, $topic->structure->workspace, $startTime, 201, 'SUCCESS');
            return $response;
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest(Auth::user() ?? null, null, $startTime, 404, 'TOPIC_NOT_FOUND');
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest(Auth::user() ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * ✏️ PUT /records/{record} - Atualizar um registro
     */
    public function update(Request $request, $recordId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            
            $record = TopicRecord::with(['topic.structure.fields', 'topic.structure.workspace', 'fieldValues'])
                ->whereHas('topic.structure.workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($recordId);
            
            // Validar dados
            $validator = $this->validateRecordData($request->all(), $record->topic->structure->fields, true);
            
            if ($validator->fails()) {
                $this->logApiRequest($user, $record->topic->structure->workspace, $startTime, 422, 'VALIDATION_FAILED');
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()
                ], 422);
            }
            
            // Atualizar ordem se fornecida
            if ($request->has('order')) {
                $record->update(['order' => $request->get('order')]);
            }
            
            // Atualizar valores dos campos
            $this->updateFieldValues($record, $request->all(), $record->topic->structure->fields);
            
            // Recarregar
            $record->refresh()->load(['fieldValues.structureField']);
            
            $responseData = $this->formatRecordResponse($record, $record->topic->structure->fields);
            
            $response = response()->json([
                'message' => 'Record updated successfully',
                'record' => $responseData
            ]);
            
            $this->logApiRequest($user, $record->topic->structure->workspace, $startTime, 200, 'SUCCESS');
            return $response;
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest(Auth::user() ?? null, null, $startTime, 404, 'RECORD_NOT_FOUND');
            return response()->json(['error' => 'Record not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest(Auth::user() ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * 🗑️ DELETE /records/{record} - Deletar um registro
     */
    public function destroy($recordId)
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            
            $record = TopicRecord::with(['topic.structure.workspace'])
                ->whereHas('topic.structure.workspace', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($recordId);
            
            $record->delete();
            
            $response = response()->json([
                'message' => 'Record deleted successfully',
                'deleted_record' => [
                    'id' => $record->id,
                    'topic_id' => $record->topic_id
                ]
            ]);
            
            $this->logApiRequest($user, $record->topic->structure->workspace, $startTime, 200, 'SUCCESS');
            return $response;
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest(Auth::user() ?? null, null, $startTime, 404, 'RECORD_NOT_FOUND');
            return response()->json(['error' => 'Record not found'], 404);
        } catch (\Exception $e) {
            $this->logApiRequest(Auth::user() ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * 🌐 GET /shared/{workspace}/{structure}/{topic} - API pública compartilhada
     */
    public function sharedRecords($workspaceKey, $structureSlug, $topicSlug)
    {
        $startTime = microtime(true);
        
        try {
            // Buscar workspace pela chave
            $workspace = Workspace::where('workspace_key_api', $workspaceKey)
                ->where('is_published', true)
                ->firstOrFail();
            
            // Buscar estrutura pelo slug
            $structure = Structure::where('workspace_id', $workspace->id)
                ->where('slug', $structureSlug)
                ->firstOrFail();
            
            // Buscar tópico pelo slug
            $topic = Topic::where('structure_id', $structure->id)
                ->where('slug', $topicSlug)
                ->firstOrFail();
            
            // Buscar registros com valores
            $records = TopicRecord::where('topic_id', $topic->id)
                ->with(['fieldValues.structureField'])
                ->orderBy('order')
                ->get();
            
            $responseData = [
                'metadata' => [
                    'workspace' => $workspace->title,
                    'structure' => $structure->name,
                    'topic' => $topic->title,
                    'total_records' => $records->count(),
                    'access_type' => 'public_shared',
                    'generated_at' => now()->toISOString()
                ],
                'records' => $records->map(function($record) use ($structure) {
                    return $this->formatSharedRecordResponse($record, $structure->fields);
                })
            ];
            
            $this->logApiRequest(null, $workspace, $startTime, 200, 'PUBLIC_API_SUCCESS');
            return response()->json($responseData);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->logApiRequest(null, null, $startTime, 404, 'PUBLIC_RESOURCE_NOT_FOUND');
            return response()->json([
                'error' => 'Resource not found',
                'message' => 'The requested data is not available'
            ], 404);
        } catch (\Exception $e) {
            $this->logApiRequest(null, null, $startTime, 500, 'PUBLIC_API_INTERNAL_ERROR');
            return response()->json([
                'error' => 'Internal server error',
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
    }
    
    /**
     * 🛠️ Métodos auxiliares
     */
    
    private function formatRecordResponse($record, $structureFields)
    {
        // 🔐 GARANTIA DE ITERÁVEL
        if (!$structureFields || !is_iterable($structureFields)) {
            $structureFields = collect();
        }

        $fieldValuesMap = [];

        foreach ($record->fieldValues as $fieldValue) {
            if (!$fieldValue->structure_field_id) continue;

            $fieldValuesMap[$fieldValue->structure_field_id] = [
                'value' => $fieldValue->formatted_value,
                'raw_value' => $fieldValue->field_value
            ];
        }

        $values = [];

        foreach ($structureFields as $field) {
            if (!$field) continue;

            $fieldData = $fieldValuesMap[$field->id] ?? null;

            $values[$field->name] = [
                'field_id'    => $field->id,
                'type'        => $field->type ?? 'text',
                'label'       => $field->label ?? $field->name,
                'is_required' => (bool) ($field->is_required ?? false),
                'value'       => $fieldData['value'] ?? null,
                'raw_value'   => $fieldData['raw_value'] ?? null
            ];
        }

        return [
            'id' => $record->id,
            'order' => $record->order,
            'topic_id' => $record->topic_id,
            'values' => $values,
            'created_at' => optional($record->created_at)->toISOString(),
            'updated_at' => optional($record->updated_at)->toISOString()
        ];
    }

    private function formatSharedRecordResponse($record, $structureFields)
    {
        // Versão simplificada para API pública
        $fieldValuesMap = [];
        foreach ($record->fieldValues as $fieldValue) {
            $fieldValuesMap[$fieldValue->structure_field_id] = $fieldValue->formatted_value;
        }
        
        $values = [];
        foreach ($structureFields as $field) {
            if ($field->is_public ?? true) { // Mostrar apenas campos públicos
                $values[$field->name] = $fieldValuesMap[$field->id] ?? null;
            }
        }
        
        return [
            'id' => $record->id,
            'order' => $record->order,
            'values' => $values,
            'created_at' => $record->created_at->toISOString()
        ];
    }
    
    private function validateRecordData($data, $structureFields, $isUpdate = false)
    {
        $rules = [];
        
        // Adicionar regras para cada campo da estrutura
        foreach ($structureFields as $field) {
            $fieldRules = [];
            
            // Regras básicas baseadas no tipo
            switch ($field->type) {
                case 'number':
                    $fieldRules[] = 'integer';
                    break;
                case 'decimal':
                    $fieldRules[] = 'numeric';
                    break;
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'json':
                    $fieldRules[] = 'json';
                    break;
                default:
                    $fieldRules[] = 'string';
            }
            
            // Campo obrigatório apenas na criação
            if ($field->is_required && !$isUpdate) {
                array_unshift($fieldRules, 'required');
            } else {
                $fieldRules[] = 'nullable';
            }
            
            $rules[$field->name] = implode('|', $fieldRules);
        }
        
        // Regra para ordem
        if ($isUpdate) {
            $rules['order'] = 'sometimes|integer|min:0';
        } else {
            $rules['order'] = 'sometimes|integer|min:0';
        }
        
        return Validator::make($data, $rules);
    }
    
    private function createFieldValues($record, $data, $structureFields)
    {
        foreach ($structureFields as $field) {
            $value = $data[$field->name] ?? $field->default_value;
            
            if ($value !== null) {
                RecordFieldValue::create([
                    'record_id' => $record->id,
                    'structure_field_id' => $field->id,
                    'field_value' => $value
                ]);
            }
        }
    }
    
    private function updateFieldValues($record, $data, $structureFields)
    {
        foreach ($structureFields as $field) {
            if (array_key_exists($field->name, $data)) {
                RecordFieldValue::updateOrCreate(
                    [
                        'record_id' => $record->id,
                        'structure_field_id' => $field->id
                    ],
                    [
                        'field_value' => $data[$field->name]
                    ]
                );
            }
        }
    }
    
    private function logApiRequest($user, $workspace, $startTime, $statusCode, $statusMessage = '')
    {
        try {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            
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
            
        } catch (\Exception $e) {
            \Log::error('Failed to log API request: ' . $e->getMessage());
        }
    }
}