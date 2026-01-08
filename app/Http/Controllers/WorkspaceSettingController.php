<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\HashService;
use Illuminate\Http\Request;
use App\Models\Collaborator;
use App\Models\Workspace;
use App\Models\Topic;
use App\Models\Field;

class WorkspaceSettingController extends Controller
{
    /**
     * Exibe a lista de todos os workspaces do usuário autenticado.
     */
    public function index($id)
    {
        $workspace = Workspace::with(['topics'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $hasPasswordWorkspace = !is_null($workspace->password);
        
        if ($workspace->password) {
            try {
                $workspace->plain_password = Crypt::decryptString($workspace->password);
            } catch (\Exception $e) {
                $workspace->plain_password = null;
            }
        }

        return Inertia::render('Dashboard/Workspace/Settings', [
            'workspace' => $workspace,
            'hasPasswordWorkspace' => $hasPasswordWorkspace,
        ]);
    }

    public function generateNewHashApi($id)
    {
        try {
            // Gera os novos hashes
            $workspaceHash = HashService::generateUniqueHash();

            // Atualiza o usuário autenticado
            $user = Workspace::findOrFail($id);
            $user->update([
                'workspace_key_api' => $workspaceHash
            ]);

            // Retorna os hashes atualizados no JSON
            return response()->json([
                'success' => true,
                'message' => 'Código API Workspace gerado com sucesso!',
                'data' => [
                    'workspace_key_api' => $workspaceHash
                ],
            ]);
        } catch (\Exception $e) {
            // Trata erros e retorna a mensagem de erro
            return back()->with([
                'type' => 'error',
                'message' => 'Ocorreu um erro ao gerar os códigos API.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Atualiza configurações de acesso do workspace
     */
    public function updateAccessSettings(Request $request, string $id)
    {
        DB::beginTransaction();
        
        try {
            $workspace = Workspace::findOrFail($id);
            
            // Verificar permissão
            if($workspace->user_id !== Auth::id()) {
                return response()->json(['error' => 'Você não tem permissão para alterar este workspace.'], 403);
            }

            // Validação - apenas campos que existem no model
            $validator = Validator::make($request->all(), [
                'is_published' => 'required|boolean',
                'password' => 'nullable|string|min:8|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $validatedData = $validator->validated();

            // Preparar dados para atualização
            $updateData = [
                'is_published' => $validatedData['is_published']
            ];

            // Gerenciar senha baseado no switch password_enabled
            $passwordEnabled = filter_var($request->input('password_enabled'), FILTER_VALIDATE_BOOLEAN);
            
            if ($passwordEnabled && !empty($validatedData['password'])) {
                // Ativar/atualizar senha
                $updateData['password'] = Crypt::encryptString($validatedData['password']);
            } elseif (!$passwordEnabled) {
                // Desativar senha
                $updateData['password'] = null;
            }
            // Se password_enabled=true mas senha vazia, mantém a senha atual

            // Atualizar workspace
            $workspace->update($updateData);

            DB::commit();

            return back()->with([
                'type' => 'success',
                'message' => 'Configurações de acesso atualizadas com sucesso!',
                'data' => [
                    'is_published' => $workspace->is_published,
                    'has_password' => !is_null($workspace->password),
                    'access_type' => $workspace->is_published ? 'public' : 'private'
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with(['error' => 'Workspace não encontrado.'], 404);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar configurações de acesso: ' . $e->getMessage());
            
            return back()->with([
                'type' => 'error',
                'error' => 'Erro interno ao atualizar configurações.'
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

            return back()->with([
                'type' => 'success',
                'message' => 'Tipo de visualização alterado com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return back()->with([
                'type' => 'error',
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
        // Verificar se usuário é Pro ou Admin
        if (!auth()->user()->isPro() && !auth()->user()->isAdmin()) {
            return response()->json([
                'type' => 'error',
                'error' => 'server_error',
                'message' => 'A duplicação de workspaces está disponível apenas para usuários Pro.'
            ], 500);
        }

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
            
            if (!$user->canCreateField(null, $totalFields)) {
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
                'workspace_key_api' => HashService::generateUniqueHash(),
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
