<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Topic;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FieldController extends Controller
{
    public function store(Request $request, Topic $topic)
    {
        try {
            $workspace = Workspace::find($request->workspace_id);
        
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'error' => 'not_found',
                    'message' => 'Workspace não encontrado'
                ], 404);
            }

            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para adicionar campos neste workspace'
                ], 403);
            }
            
            $currentCount = $user->getTopicFieldsCount($topic->id);
            $fieldsLimit = $user->getFieldsLimitPerTopic();
            $isUnlimited = $user->hasUnlimitedFields();

            if (!$isUnlimited && $currentCount >= $fieldsLimit) {
                return response()->json([
                    'success' => false,
                    'error' => 'limit_exceeded',
                    'message' => "Limite de {$fieldsLimit} campos por tópico atingido. Este tópico já tem {$currentCount} campos.",
                    'limits' => [
                        'current' => $currentCount,
                        'max' => $fieldsLimit,
                        'remaining' => 0,
                        'is_unlimited' => false
                    ]
                ], 403);
            }
            
            $validated = Validator::make($request->all(), Field::getValidationRules())->validate();
            $validated['key_name'] = Field::formatKeyName($validated['key_name']);
            $existingField = $topic->fields()
                ->where('key_name', $validated['key_name'])
                ->first();
                
            if ($existingField) {
                \Log::warning("Chave duplicada", [
                    'key_name' => $validated['key_name'],
                    'topic_id' => $topic->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'duplicate_key',
                    'message' => 'Já existe um campo com esta chave neste tópico'
                ], 422);
            }
            
            $validated['order'] = $currentCount + 1;
            
            \Log::info("Criando campo", [
                'topic_id' => $topic->id,
                'key_name' => $validated['key_name'],
                'order' => $validated['order'],
                'current_count_before' => $currentCount
            ]);
            
            $createField = Field::create($validated);
            $newCount = $user->getTopicFieldsCount($topic->id);
            
            \Log::info("Campo criado com sucesso", [
                'field_id' => $createField->id,
                'topic_id' => $topic->id,
                'new_count' => $newCount,
                'limit' => $fieldsLimit
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Campo criado com sucesso', 
                'data' => $createField,
                'limits' => [
                    'current' => $newCount,
                    'max' => $fieldsLimit,
                    'remaining' => $isUnlimited ? PHP_INT_MAX : max(0, $fieldsLimit - $newCount),
                    'is_unlimited' => $isUnlimited
                ]
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'validation_error',
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch(\Exception $e){
            \Log::error('Erro ao criar campo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Ocorreu um erro interno'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $field = Field::findOrFail($id);
            $workspace = $field->topic->workspace;
            $topic = $field->topic;
            
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para editar este campo'
                ], 403);
            }
            
            if ($isOwner && $user->isFree() && $request->type !== 'text') {
                return response()->json([
                    'success' => false,
                    'error' => 'plan_restriction',
                    'message' => 'A funcionalidade de tipagem avançada está disponível apenas para planos Start, Pro e Premium. Faça upgrade para acessar tipos como boolean e number.'
                ], 403);
            }
            
            // Valida com regras dinâmicas baseadas no plano
            $validated = Validator::make($request->all(), Field::getValidationRules())->validate();
            $validated['key_name'] = Field::formatKeyName($validated['key_name']);

            $field->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Campo atualizado com sucesso', 
                'data' => $field
            ], 200);            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'validation_error',
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Ocorreu um erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $field = Field::findOrFail($id);
            $workspace = $field->topic->workspace;
            $topic = $field->topic;
            
            $user = Auth::user();
            $isOwner = $workspace->user_id === $user->id;
            $collaborator = $workspace->collaborators()->where('user_id', $user->id)->where('status', 'accepted')->first();
            
            if (!$isOwner && (!$collaborator || !$collaborator->canEdit())) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para excluir este campo'
                ], 403);
            }
            
            $field->delete();

            // ✅ CORREÇÃO: Usar método alternativo para retornar limites atualizados
            return response()->json([
                'success' => true,
                'message' => 'Campo removido com sucesso', 
                'data' => ['id' => $id],
                'limits' => $isOwner ? [
                    'current' => $user->getTopicFieldsCount($topic->id),
                    'max' => $user->getFieldsLimit(),
                    'remaining' => $user->getRemainingFieldsCount($workspace->id, $topic->id),
                    'is_unlimited' => $user->hasUnlimitedFields()
                ] : null
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'not_found',
                'message' => 'Campo não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Ocorreu um erro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Método para verificar limite sem criar campo (apenas para dono)
     */
    public function checkLimit(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspace_id);
            $topic = Topic::find($request->topic_id);
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'error' => 'not_found',
                    'message' => 'Workspace não encontrado'
                ], 404);
            }
            
            $user = Auth::user();
            
            // A verificação de limite só se aplica ao dono
            if ($workspace->user_id !== $user->id) {
                return response()->json([
                    'success' => true,
                    'can_add_more' => true,
                    'limits' => null,
                    'allowed_types' => Field::getAllowedTypesWithLabels($user)
                ], 200);
            }
            
            $topicId = $topic ? $topic->id : null;
            $currentCount = $topicId ? $user->getTopicFieldsCount($topicId) : 0;
            $maxLimit = $user->getFieldsLimit();
            
            $isUnlimited = $maxLimit === 0;
            $canAddMore = $isUnlimited || $currentCount < $maxLimit;
            $remaining = $isUnlimited ? PHP_INT_MAX : max(0, $maxLimit - $currentCount);
            
            return response()->json([
                'success' => true,
                'can_add_more' => $canAddMore,
                'limits' => [
                    'current' => $currentCount,
                    'max' => $maxLimit,
                    'remaining' => $remaining,
                    'is_unlimited' => $isUnlimited
                ],
                'allowed_types' => Field::getAllowedTypesWithLabels($user)
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao verificar limite: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao verificar limite: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Método para obter tipos permitidos (útil para o frontend)
     */
    public function getAllowedTypes(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspace_id);
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'error' => 'not_found',
                    'message' => 'Workspace não encontrado'
                ], 404);
            }

            $user = Auth::user();
            $allowedTypes = Field::getAllowedTypesWithLabels($user);
            
            return response()->json([
                'success' => true,
                'allowed_types' => $allowedTypes,
                'user_plan' => $user->getPlan()->name,
                'is_admin' => $user->isAdmin(),
                'is_free' => $user->isFree(),
                'is_pro' => $user->isPro(),
                'is_premium' => $user->isPremium()
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao obter tipos permitidos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao obter tipos permitidos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método para verificar se um tipo específico é permitido
     */
    public function checkTypeAllowed(Request $request)
    {
        try {
            $type = $request->type;
            
            if (!$type) {
                return response()->json([
                    'success' => false,
                    'error' => 'missing_type',
                    'message' => 'Tipo não especificado'
                ], 400);
            }

            $user = Auth::user();
            $isAllowed = Field::isTypeAllowed($type, $user);
            
            return response()->json([
                'success' => true,
                'type' => $type,
                'is_allowed' => $isAllowed,
                'user_plan' => $user->getPlan()->name
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao verificar tipo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao verificar tipo: ' . $e->getMessage()
            ], 500);
        }
    }
}