<?php

namespace App\Http\Controllers;

use App\Services\HashService;
use Illuminate\Http\Request;
use App\Models\Workspace;

class WorkspaceSettingController extends Controller
{
    /**
     * Exibe a lista de todos os workspaces do usuário autenticado.
     */
    public function index($id)
    {
        // Carrega o workspace com os tópicos e fields aninhados
        // $workspace = Workspace::with(['topics' => function($query) {
        //         $query->orderBy('order')->with(['fields' => function($query) {
        //             $query->orderBy('order');
        //         }]);
        //     }])
        //     ->where('id', $id)
        //     ->where('user_id', Auth::id())
        //     ->first();

        // // Se não encontrou o workspace, retorna 404
        // if (!$workspace) {
        //     abort(404, 'Workspace não encontrado ou você não tem permissão para acessá-lo');
        // }

        // // Obter informações de limite de campos
        // $user = Auth::user();
        // $canAddMoreFields = $user->canAddMoreFields($workspace->id);
        // $fieldsLimit = $user->getFieldsLimit();
        // $currentFieldsCount = $user->getCurrentFieldsCount($workspace->id);
        // $remainingFields = $user->getRemainingFieldsCount($workspace->id);
        $workspace = Workspace::find($id);
        return view('pages.dashboard.workspaces.workspace-settings', compact('workspace'));
    }

    public function generateNewHashApi($id)
    {
        try {
            // Gera os novos hashes
            $workspaceHash = HashService::generateUniqueHash();

            // Atualiza o usuário autenticado
            $user = Workspace::findOrFail($id);
            $user->update([
                'workspace_hash_api' => $workspaceHash
            ]);

            // Retorna os hashes atualizados no JSON
            return response()->json([
                'success' => true,
                'message' => 'Código API Workspace gerado com sucesso!',
                'data' => [
                    'workspace_hash_api' => $workspaceHash
                ],
            ]);
        } catch (\Exception $e) {
            // Trata erros e retorna a mensagem de erro
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao gerar os códigos API.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
