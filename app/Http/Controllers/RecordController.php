<?php

namespace App\Http\Controllers;

use App\Models\TopicRecord;
use App\Models\RecordFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'topic_id' => 'required|exists:topics,id',
                'record_order' => 'required|integer',
            ]);

            // Verificar permissões e limites aqui...

            $record = TopicRecord::create($validated);

            // Carregar o tópico atualizado com registros
            $topic = $record->topic->load('records.fieldValues');

            return response()->json([
                'success' => true,
                'message' => 'Registro criado com sucesso',
                'topic' => $topic
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao criar registro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateField(Request $request, TopicRecord $record, $fieldId)
    {
        try {
            $validated = $request->validate([
                'value' => 'nullable|string'
            ]);

            // Atualiza o valor do campo
            $record->setFieldValue($fieldId, $validated['value']);

            // Retorno usando flash message para o Inertia não interferir
            return back()->with([
                'alert' => [
                    'type' => 'success',
                    'message' => 'Campo atualizado com sucesso.',
                    'data' => [
                        'record_id' => $record->id,
                        'field_id' => $fieldId,
                        'value' => $validated['value']
                    ]
                ]
            ]);

        } catch (\Exception $e) {

            return back()->with([
                'alert' => [
                    'type' => 'error',
                    'message' => 'Erro ao atualizar campo.',
                    'details' => $e->getMessage()
                ]
            ]);
        }
    }

    public function destroy(TopicRecord $record)
    {
        try {
            $record->delete();

            return back()->with('success', 'Registro excluído com sucesso');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir registro');
        }
    }
}