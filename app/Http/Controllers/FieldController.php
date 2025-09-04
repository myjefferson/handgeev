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
            
            // Valida a requisição.
            $validated = Validator::make($request->all(), Field::$rules)->validate();
            
            // Adiciona a ordem baseada no próximo número disponível
            $validated['order'] = $topic->fields()->count() + 1;
            
            $createField = Field::create($validated);
    
            return response()->json([
                'success' => true,
                'status' => 'Campo salvo com sucesso', 
                'data' => $createField
            ], 201);
        }catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Ocorreu um erro: '. $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Verificar se o campo pertence ao tópico
            // dd($topic->id);
            // if ($field->topic_id !== $topic->id) {
            //     return response()->json([
            //         'error' => 'Este campo não pertence ao tópico especificado'
            //     ], 404);
            // }

            $validated = $request->validate(Field::$rules);

            $field = Field::findOrFail($id);            
            $field->update($validated);


            return response()->json([
                'success' => true,
                'message' => 'Campo atualizado com sucesso', 
                'data' => $field
            ], 200); // 200 OK é mais apropriado para atualização

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
            $field->delete($field);
    
            return response()->json([
                'success' => true,
                'message' => 'Campo removido com sucesso', 
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
}