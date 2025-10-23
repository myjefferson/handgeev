<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Collaborator;
use App\Notifications\EditRequestNotification;
use App\Notifications\EditRequestStatusNotification;

class EditRequestController extends Controller
{
    /**
     * Solicitar permissão de edição (apenas para colaboradores viewer)
     */
    public function requestEditAccess(Request $request, $globalHash, $workspaceHash)
    {
        // Verificar se o usuário está logado
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Você precisa estar logado para solicitar permissão de edição.',
                'redirect' => route('login.show')
            ], 401);
        }

        $request->validate([
            'message' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();

        // Encontrar workspace pelos hashes
        $workspaceOwner = User::where('global_key_api', $globalHash)->firstOrFail();
        $workspace = Workspace::where('workspace_key_api', $workspaceHash)
                            ->where('user_id', $workspaceOwner->id)
                            ->firstOrFail();

        // 🔥 VERIFICAR SE É COLABORADOR VIEWER 🔥
        $collaborator = Collaborator::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('role', 'viewer')
            ->where('status', 'accepted')
            ->first();

        if (!$collaborator) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas colaboradores com permissão de visualização podem solicitar edição.'
            ], 403);
        }

        // Verificar se já existe uma solicitação pendente
        $existingRequest = Collaborator::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('request_type', 'edit_request')
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Você já tem uma solicitação de edição pendente para este workspace.'
            ], 422);
        }

        // Criar solicitação de upgrade
        $collaboratorRequest = Collaborator::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'invitation_email' => $user->email,
            'role' => 'editor', // Nova role solicitada
            'invited_by' => $workspace->user_id,
            'invited_at' => now(),
            'request_message' => $request->message,
            'requested_at' => now(),
            'request_type' => 'edit_request',
            'status' => 'pending'
        ]);

        // Notificar o proprietário
        try {
            $workspace->user->notify(new EditRequestNotification($collaboratorRequest, $workspace, $user));
            
            return response()->json([
                'success' => true,
                'message' => 'Solicitação de edição enviada com sucesso! O proprietário será notificado.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar notificação: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'message' => 'Solicitação enviada! Pode haver um atraso na notificação do proprietário.'
            ]);
        }
    }

    /**
     * Aprovar solicitação de edição
     */
    public function approveRequest($requestId)
    {
        $collaboratorRequest = Collaborator::findOrFail($requestId);
        $workspace = $collaboratorRequest->workspace;

        // Verificar se o usuário atual é o proprietário
        if ($workspace->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o proprietário pode aprovar solicitações.'
            ], 403);
        }

        // Buscar o colaborador original (viewer)
        $originalCollaborator = Collaborator::where('workspace_id', $workspace->id)
            ->where('user_id', $collaboratorRequest->user_id)
            ->where('role', 'viewer')
            ->where('status', 'accepted')
            ->first();

        if (!$originalCollaborator) {
            return response()->json([
                'success' => false,
                'message' => 'Colaborador não encontrado ou já possui outra role.'
            ], 404);
        }

        // Atualizar a role do colaborador para editor
        $originalCollaborator->update([
            'role' => 'editor',
            'updated_at' => now()
        ]);

        // Atualizar a solicitação
        $collaboratorRequest->update([
            'status' => 'accepted',
            'responded_at' => now()
        ]);

        // Notificar o usuário
        $user = User::find($collaboratorRequest->user_id);
        if ($user) {
            $user->notify(new EditRequestStatusNotification($collaboratorRequest, $workspace, 'approved'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissão de edição concedida com sucesso!'
        ]);
    }

    /**
     * Rejeitar solicitação de edição
     */
    public function rejectRequest(Request $request, $requestId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $collaboratorRequest = Collaborator::findOrFail($requestId);
        $workspace = $collaboratorRequest->workspace;

        // Verificar se o usuário atual é o proprietário
        if ($workspace->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o proprietário pode rejeitar solicitações.'
            ], 403);
        }

        $collaboratorRequest->update([
            'status' => 'rejected',
            'responded_at' => now(),
            'response_reason' => $request->reason
        ]);

        // Notificar o usuário que foi rejeitado
        if ($collaboratorRequest->user_id) {
            $user = User::find($collaboratorRequest->user_id);
            if ($user) {
                $user->notify(new EditRequestStatusNotification($collaboratorRequest, $workspace, 'rejected', $request->reason));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitação rejeitada.'
        ]);
    }

    /**
     * Listar solicitações de edição pendentes
     */
    public function listPendingRequests($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);

        if ($workspace->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado.'
            ], 403);
        }

        $requests = Collaborator::with(['user', 'inviter'])
            ->where('workspace_id', $workspaceId)
            ->where('request_type', 'edit_request')
            ->where('status', 'pending')
            ->orderBy('requested_at', 'desc')
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'user_id' => $request->user_id,
                    'user_name' => $request->user ? $request->user->name : $request->invitation_email,
                    'user_email' => $request->user ? $request->user->email : $request->invitation_email,
                    'message' => $request->request_message,
                    'requested_at' => $request->requested_at,
                    'role' => $request->role
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Listar histórico de solicitações de edição
     */
    public function listAllRequests($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);

        if ($workspace->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado.'
            ], 403);
        }

        $requests = Collaborator::with(['user', 'inviter'])
            ->where('workspace_id', $workspaceId)
            ->where('request_type', 'edit_request')
            ->orderBy('requested_at', 'desc')
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'user_name' => $request->user ? $request->user->name : $request->invitation_email,
                    'user_email' => $request->user ? $request->user->email : $request->invitation_email,
                    'message' => $request->request_message,
                    'status' => $request->status,
                    'requested_at' => $request->requested_at,
                    'responded_at' => $request->responded_at,
                    'response_reason' => $request->response_reason,
                    'role' => $request->role
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }
}