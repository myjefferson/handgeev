<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TopicController extends Controller
{
    /**
     * Adiciona um novo tópico a um workspace.
     */
    public function store(Request $request)
    {
        try {
            // Valida a requisição.
            $validated = Validator::make($request->all(), Topic::$rules)->validate();
            $topic = Topic::create($validated);
            return response()->json($topic, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Atualiza um tópico existente.
     */
    public function update(Request $request, Topic $topic)
    {
        try {
            $validated = $request->validate([
                'title' => 'string|max:100',
                'order' => 'integer',
            ]);

            $topic->update($validated);

            return response()->json($topic);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Exclui um tópico.
     */
    public function destroy(string $id)
    {
        try{
            $topic = Topic::with(['workspace', 'fields'])->findOrFail($id);

            if($topic->workspace->user_id !== Auth::id()){
                return response()->json([
                    'error' => 'Você não tem permissão para excluir este tópico'
                ], 403);
            }

            $topic->fields()->delete();
            $topic->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tópico e todos os seus campos foram excluídos com sucesso',
                'deleted_fields' => $topic->fields->count()
            ], 200);
        }catch(\Exception $e){

        }
    }

    
}