<?php

namespace App\Http\Controllers\Api;

use App\Models\Structure;
use App\Models\Workspace;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class StructureApiController extends Controller
{
    public function index($workspaceId)
    {
        $startTime = microtime(true);
        $workspace = null;
        $user = Auth::user(); // pode ser null

        try {
            $workspace = Workspace::with([
                'topics.structure.fields'
            ])->findOrFail($workspaceId);

            // Coleta estruturas únicas usadas nos tópicos do workspace
            $structures = $workspace->topics
                ->pluck('structure')
                ->filter()
                ->unique('id')
                ->values();

            $responseData = [
                'metadata' => [
                    'workspace_id' => $workspace->id,
                    'workspace_title' => $workspace->title,
                    'total' => $structures->count(),
                    'generated_at' => now()->toISOString(),
                ],
                'structures' => $structures->map(fn ($structure) => [
                    'id' => $structure->id,
                    'name' => $structure->name,
                    'description' => $structure->description,
                    'fields_count' => $structure->fields->count(),
                    'fields' => $structure->fields->map(fn ($field) => [
                        'id' => $field->id,
                        'name' => $field->name,
                        'type' => $field->type,
                        'is_required' => $field->is_required,
                        'order' => $field->order,
                    ])->values(),
                    'created_at' => optional($structure->created_at)?->toISOString(),
                    'updated_at' => optional($structure->updated_at)?->toISOString(),
                ]),
            ];

            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');

            return response()->json($responseData);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            $this->logApiRequest($user, null, $startTime, 404, 'WORKSPACE_NOT_FOUND');

            return response()->json([
                'error' => 'Workspace not found'
            ], 404);

        } catch (\Throwable $e) {

            report($e); // 👈 ESSENCIAL

            $this->logApiRequest($user, $workspace, $startTime, 500, 'INTERNAL_ERROR');

            return response()->json([
                'error' => 'Internal server error'
            ], 500);
        }
    }
    
    public function show($structureId)
    {
        $startTime = microtime(true);
        $user = Auth::user();
        $workspace = null;

        try {
            $structure = Structure::with([
                    'fields',
                    'topics.workspace'
                ])
                ->whereHas('topics.workspace', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($structureId);

            // Pega o primeiro workspace relacionado (estrutura pode estar em vários)
            $workspace = $structure->topics
                ->pluck('workspace')
                ->filter()
                ->first();

            $responseData = [
                'structure' => [
                    'id' => $structure->id,
                    'name' => $structure->name,
                    'description' => $structure->description,
                    'workspace' => $workspace ? [
                        'id' => $workspace->id,
                        'title' => $workspace->title,
                    ] : null,
                    'fields' => $structure->fields->map(fn ($field) => [
                        'id' => $field->id,
                        'name' => $field->name,
                        'key' => $field->key_name,
                        'type' => $field->type,
                        'description' => $field->description,
                        'is_required' => $field->is_required,
                        'order' => $field->order,
                        'validation_rules' => $field->validation_rules,
                        'default_value' => $field->default_value,
                    ]),
                    'created_at' => optional($structure->created_at)?->toISOString(),
                    'updated_at' => optional($structure->updated_at)?->toISOString(),
                ],
            ];

            $this->logApiRequest($user, $workspace, $startTime, 200, 'SUCCESS');
            return response()->json($responseData);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            $this->logApiRequest($user ?? null, null, $startTime, 404, 'STRUCTURE_NOT_FOUND');
            return response()->json([
                'error' => 'Structure not found'
            ], 404);
        } catch (\Throwable $e) {
            report($e);
            $this->logApiRequest($user ?? null, null, $startTime, 500, 'INTERNAL_ERROR');
            return response()->json([
                'error' => 'Internal server error'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $startTime = microtime(true);
        $user = Auth::user();
        
        // Debug inicial
        \Log::info('=== STORE STRUCTURE START ===');
        \Log::info('User ID:', ['id' => $user->id]);
        \Log::info('Request data:', $request->all());

        try {
            // Validação com mais detalhes
            $validator = Validator::make($request->all(), [
                'structure.name' => 'required|string|max:150',
                'structure.description' => 'nullable|string',
                'structure.is_public' => 'nullable|boolean',

                'structure.fields' => 'required|array|min:1',
                'structure.fields.*.name' => 'required|string|max:100',
                'structure.fields.*.key' => 'nullable|string|max:100',
                'structure.fields.*.type' => 'required|string|in:text,number,date,select,checkbox,textarea', // especifique os tipos
                'structure.fields.*.description' => 'nullable|string|max:255',
                'structure.fields.*.is_required' => 'nullable|boolean',
                'structure.fields.*.order' => 'nullable|integer|min:0',
                'structure.fields.*.validation_rules' => 'nullable|array',
                'structure.fields.*.default_value' => 'nullable',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            \Log::info('Validated data:', $data);

            $structureData = $data['structure'];

            // Criar estrutura
            \Log::info('Creating structure...');
            $structure = Structure::create([
                'user_id' => $user->id,
                'name' => $structureData['name'],
                'description' => $structureData['description'] ?? null,
                'is_public' => filter_var($structureData['is_public'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]);

            \Log::info('Structure created:', ['id' => $structure->id]);

            // Criar campos
            \Log::info('Creating fields...', ['count' => count($structureData['fields'])]);
            foreach ($structureData['fields'] as $index => $fieldData) {
                try {
                    $field = $structure->fields()->create([
                        'name' => $fieldData['name'],
                        'key_name' => $fieldData['key'] ?? \Str::slug($fieldData['name'], '_'),
                        'type' => $fieldData['type'],
                        'description' => $fieldData['description'] ?? null,
                        'is_required' => filter_var($fieldData['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'order' => $fieldData['order'] ?? ($index + 1),
                        'validation_rules' => $fieldData['validation_rules'] ?? null,
                        'default_value' => $fieldData['default_value'] ?? null,
                    ]);
                    \Log::info("Field created:", ['id' => $field->id, 'name' => $field->name]);
                } catch (\Exception $fieldError) {
                    \Log::error('Error creating field:', [
                        'field_data' => $fieldData,
                        'error' => $fieldError->getMessage()
                    ]);
                    throw $fieldError;
                }
            }

            $structure->load('fields');

            $this->logApiRequest($user, null, $startTime, 201, 'STRUCTURE_CREATED');

            return response()->json([
                'message' => 'Structure created successfully',
                'structure' => [
                    'id' => $structure->id,
                    'name' => $structure->name,
                    'description' => $structure->description,
                    'is_public' => $structure->is_public,
                    'fields' => $structure->fields->map(fn ($field) => [
                        'id' => $field->id,
                        'name' => $field->name,
                        'key' => $field->key_name,
                        'type' => $field->type,
                        'is_required' => $field->is_required,
                        'order' => $field->order,
                    ]),
                    'created_at' => $structure->created_at->toISOString(),
                ],
            ], 201);

        } catch (\Throwable $e) {
            \Log::error('Store structure error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            report($e);

            $this->logApiRequest($user, null, $startTime, 500, 'INTERNAL_ERROR');

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Structure $structure)
    {
        try {
            // Verifica se a estrutura está sendo usada em algum tópico
            if ($structure->topics()->exists()) {
                return response()->json([
                    'error', 'Não é possível excluir uma estrutura que está sendo usada em tópicos.'
                ]);
            }

            $structure->delete();

            return response()->json([
                'success' => 'Estrutura excluída com sucesso!',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao excluir estrutura',
                'message' => $e->getMessage()
            ], 500);
            
        }
    }

    
    private function logApiRequest($user, $workspace, $startTime, $statusCode, $statusMessage = '')
    {
        // Implemente o logging conforme necessário
    }
}