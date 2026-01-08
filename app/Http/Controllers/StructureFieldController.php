<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\StructureField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StructureFieldController extends Controller
{
    /**
     * Store a newly created structure field.
     */
    public function store(Request $request, Structure $structure)
    {
        try {
            // Verificar se o usuário é dono da estrutura
            if ($structure->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para adicionar campos a esta estrutura'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'type' => 'required|string|in:text,number,decimal,boolean,date,datetime,email,url,json',
                'default_value' => 'nullable|string',
                'is_required' => 'boolean',
                'order' => 'required|integer|min:1',
            ]);

            // Criar o campo
            $field = $structure->fields()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Campo adicionado com sucesso',
                'field' => $field
            ], 201);

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
                'message' => 'Erro ao adicionar campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified structure field.
     */
    public function update(Request $request, Structure $structure, StructureField $field)
    {
        try {
            // Verificar se o campo pertence à estrutura e se o usuário é dono
            if ($field->structure_id !== $structure->id || $structure->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para editar este campo'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'type' => 'required|string|in:text,number,decimal,boolean,date,datetime,email,url,json',
                'default_value' => 'nullable|string',
                'is_required' => 'boolean',
                'order' => 'required|integer|min:1',
            ]);

            // Atualizar o campo
            $field->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Campo atualizado com sucesso',
                'field' => $field
            ]);

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
                'message' => 'Erro ao atualizar campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified structure field.
     */
    public function destroy(Structure $structure, StructureField $field)
    {
        try {
            // Verificar se o campo pertence à estrutura e se o usuário é dono
            if ($field->structure_id !== $structure->id || $structure->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'permission_denied',
                    'message' => 'Você não tem permissão para excluir este campo'
                ], 403);
            }

            $field->delete();

            return response()->json([
                'success' => true,
                'message' => 'Campo excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao excluir campo: ' . $e->getMessage()
            ], 500);
        }
    }
}