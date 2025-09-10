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
        try{
            // Primeiro, encontre o workspace através da relação correta
            $workspace = Workspace::find($request->workspace_id);
            
            if (!$workspace || Auth::id() !== $workspace->user_id) {
                abort(403, 'Você não tem permissão para adicionar campos neste tópico');
            }
            
            // ========== VERIFICAÇÃO DE LIMITE ==========
            $user = Auth::user();
            if (!$user->canAddMoreFields($workspace->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'limit_exceeded',
                    'message' => 'Limite de campos atingido. Faça upgrade do seu plano para adicionar mais campos.'
                ], 403);
            }
            // ========== FIM DA VERIFICAÇÃO ==========
            
            // Valida a requisição.
            $validated = Validator::make($request->all(), Field::$rules)->validate();
            
            // Adiciona a ordem baseada no próximo número disponível
            $validated['order'] = $topic->fields()->count() + 1;
            
            $createField = Field::create($validated);
    
            return response()->json([
                'success' => true,
                'status' => 'Campo salvo com sucesso', 
                'data' => $createField,
                'limits' => [
                    'current' => $user->getCurrentFieldsCount($workspace->id),
                    'max' => $user->getFieldsLimit(),
                    'remaining' => $user->getRemainingFieldsCount($workspace->id)
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch(\Exception $e){
            return response()->json([
                'error' => 'Ocorreu um erro: '. $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate(Field::$rules);

            $field = Field::findOrFail($id);
            
            // Verificar permissão - usuário só pode editar seus próprios campos
            $workspace = $field->topic->workspace;
            if (Auth::id() !== $workspace->user_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para editar este campo'
                ], 403);
            }
            
            $field->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Campo atualizado com sucesso', 
                'data' => $field
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro: ' . $e->getMessage()
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
            
            // Verificar permissão - usuário só pode excluir seus próprios campos
            $workspace = $field->topic->workspace;
            if (Auth::id() !== $workspace->user_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para excluir este campo'
                ], 403);
            }
            
            $field->delete();
    
            // Retornar informações atualizadas de limite
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'message' => 'Campo removido com sucesso', 
                'data' => $field,
                'limits' => [
                    'current' => $user->getCurrentFieldsCount($workspace->id),
                    'max' => $user->getFieldsLimit(),
                    'remaining' => $user->getRemainingFieldsCount($workspace->id)
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Método para verificar limite sem criar campo
     */
    public function checkLimit(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspace_id);
            
            if (!$workspace || Auth::id() !== $workspace->user_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Workspace não encontrado ou sem permissão'
                ], 403);
            }
            
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'can_add_more' => $user->canAddMoreFields($workspace->id),
                'limits' => [
                    'current' => $user->getCurrentFieldsCount($workspace->id),
                    'max' => $user->getFieldsLimit(),
                    'remaining' => $user->getRemainingFieldsCount($workspace->id)
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao verificar limite: ' . $e->getMessage()
            ], 500);
        }
    }
}