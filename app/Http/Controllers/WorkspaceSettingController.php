<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Services\HashService;
use Illuminate\Http\Request;
use App\Models\Collaborator;
use App\Models\Workspace;
use App\Models\Topic;
use App\Models\Field;
use DB;

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
        $hasPasswordWorkspace = !is_null($workspace->password);
        if ($workspace->password) {
            try {
                $workspace->plain_password = Crypt::decryptString($workspace->password);
            } catch (\Exception $e) {
                $workspace->plain_password = null;
            }
        }
        
        return view('pages.dashboard.workspaces.workspace-settings', compact('workspace', 'hasPasswordWorkspace'));
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

    public function passwordWorkspace(Request $request, $id)
    {
        try {
            $workspace = Workspace::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $checkbox = filter_var($request->checkbox, FILTER_VALIDATE_BOOLEAN);
            if (!$checkbox) {
                $workspace->update([
                    'password' => null,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Senha removida do workspace!',
                    'password' => ''
                ], 200);
            }

            $workspace->update([
                'password' => Crypt::encryptString($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Senha para o workspace aplicada!',
                'password' => $request->password
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao criar a senha para o Workspace.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function viewWorkspace(Request $request, $id){
        try {
            $workspace = Workspace::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $workspace->update([
                'type_view_workspace_id' => $request->type_view_workspace,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de visualização alterado com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao alterar o tipo de visualização do Workspace.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplicar um workspace completo com nome personalizado
     */
    public function duplicate(Request $request, $workspaceId)
    {
        try {
            DB::beginTransaction();

            // Validar request
            $request->validate([
                'new_title' => 'required|string|max:100'
            ]);

            // Encontrar o workspace original
            $originalWorkspace = Workspace::with(['topics.fields'])
                ->where('id', $workspaceId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Verificar limite de campos do usuário
            $user = Auth::user();
            $totalFields = $originalWorkspace->topics->sum(function($topic) {
                return $topic->fields->count();
            });
            
            if (!$user->canAddMoreFields(null, $totalFields)) {
                return response()->json([
                    'success' => false,
                    'error' => 'limit_exceeded',
                    'message' => 'Limite de campos excedido. Faça upgrade do seu plano para duplicar este workspace.'
                ], 403);
            }

            // Verificar se título já existe
            $existingWorkspace = Workspace::where('user_id', Auth::id())
                ->where('title', $request->new_title)
                ->first();

            if ($existingWorkspace) {
                return response()->json([
                    'success' => false,
                    'error' => 'title_exists',
                    'message' => 'Já existe um workspace com este nome. Escolha outro nome.'
                ], 422);
            }

            // Criar novo workspace
            $newWorkspace = Workspace::create([
                'user_id' => Auth::id(),
                'type_workspace_id' => $originalWorkspace->type_workspace_id,
                'type_view_workspace_id' => $originalWorkspace->type_view_workspace_id,
                'title' => $request->new_title,
                'is_published' => false,
                'password' => $originalWorkspace->password,
                'workspace_hash_api' => HashService::generateUniqueHash(),
            ]);

            // Duplicar tópicos e campos
            foreach ($originalWorkspace->topics as $originalTopic) {
                $newTopic = Topic::create([
                    'workspace_id' => $newWorkspace->id,
                    'title' => $originalTopic->title,
                    'order' => $originalTopic->order,
                ]);

                // Duplicar campos do tópico
                foreach ($originalTopic->fields as $originalField) {
                    Field::create([
                        'topic_id' => $newTopic->id,
                        'key_name' => $originalField->key_name,
                        'value' => $originalField->value,
                        'is_visible' => $originalField->is_visible,
                        'order' => $originalField->order,
                    ]);
                }
            }

            // Criar registro de colaborador
            Collaborator::create([
                'workspace_id' => $newWorkspace->id,
                'user_id' => Auth::id(),
                'role' => 'owner',
                'invited_by' => Auth::id(),
                'invited_at' => now(),
                'joined_at' => now(),
                'status' => 'accepted'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Workspace duplicado com sucesso!',
                'data' => [
                    'new_workspace_id' => $newWorkspace->id,
                    'new_workspace_title' => $newWorkspace->title,
                    'redirect_url' => route('workspace.show', $newWorkspace->id)
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'validation_error',
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'not_found',
                'message' => 'Workspace não encontrado'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Erro ao duplicar workspace: ' . $e->getMessage()
            ], 500);
        }
    }
}
