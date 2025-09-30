<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\User;
use App\Models\Collaborator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkspaceInvitation;
use App\Notifications\WorkspaceInviteNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CollaboratorController extends Controller
{
    public function indexCollaborations()
    {
        $user = auth()->user();
        // Agora usando o relacionamento correto
        $collaborations = $user->collaborations()
            ->with(['workspace' => function($query) {
                $query->withCount('topics');
            }])
            ->orderBy('joined_at', 'desc')
            ->get();
        return view('pages.dashboard.collaborations.my-collaborations', compact('collaborations'));
    }

    /**
     * Workspace colaborador
     */
    public function showCollaboration($id)
    {
        $user = Auth::user();
        
        // Primeiro verifica se o usuário é o dono do workspace
        $isOwner = Workspace::where('id', $id)
            ->where('user_id', $user->id)
            ->exists();

        // Se não é dono, verifica se é colaborador ACEITO através da tabela workspace_collaborators
        $collaborator = Collaborator::where('workspace_id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$collaborator) {
            abort(404, 'Workspace não encontrado ou você não tem permissão para acessá-lo');
        }

        // Carrega o workspace usando o ID da colaboração (mais seguro)
        $workspace = Workspace::with(['topics' => function($query) {
                $query->orderBy('order')->with(['fields' => function($query) {
                    $query->orderBy('order');
                }]);
            }])
            ->where('id', $collaborator->workspace_id) // ← AQUI É A CHAVE: usa o ID da colaboração
            ->firstOrFail();
        
        // Obter informações de limite de campos (apenas para dono)
        $canAddMoreFields = $isOwner ? $user->canAddMoreFields($workspace->id) : false;
        $fieldsLimit = $isOwner ? $user->getFieldsLimit() : null;
        $currentFieldsCount = $isOwner ? $user->getCurrentFieldsCount($workspace->id) : null;
        $remainingFields = $isOwner ? $user->getRemainingFieldsCount($workspace->id) : null;
        
        // Obter role do usuário
        $userRole = $isOwner ? 'owner' : ($collaborator->role ?? 'viewer');
        
        return view('pages.dashboard.workspace.workspace', compact(
            'workspace',
            'canAddMoreFields',
            'fieldsLimit',
            'currentFieldsCount',
            'remainingFields',
            'isOwner',
            'userRole'
        ));
    }

    /**
     * Convidar colaborador
     */
    public function inviteCollaborator(Request $request, $workspaceId)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:viewer,editor,admin'
        ]);

        $workspace = Workspace::findOrFail($workspaceId);
        
        if (!$workspace->userHasAccess(auth()->user(), 'collaborator.invite')) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }

        $invitedUser = User::where('email', $request->email)->first();
        
        // Verificar se já existe convite pendente
        $existingInvite = Collaborator::where('workspace_id', $workspaceId)
            ->where(function($query) use ($request, $invitedUser) {
                $query->where('invitation_email', $request->email)
                    ->orWhere('user_id', $invitedUser?->id);
            })
            ->whereIn('status', ['pending', 'accepted']) // Verificar tanto convites pendentes quanto ativos
            ->first();

        if ($existingInvite) {
            $statusMessage = $existingInvite->status === 'pending' ? 'Convite já existe e está pendente' : 'Usuário já é colaborador';
            return response()->json(['error' => $statusMessage], 422);
        }

        $invitationToken = Str::random(60);

        // Criar convite
        $collaborator = Collaborator::create([
            'workspace_id' => $workspaceId,
            'user_id' => $invitedUser?->id,
            'role' => $request->role,
            'invitation_token' => $invitationToken,
            'invitation_email' => $request->email,
            'invited_by' => auth()->id(),
            'invited_at' => now(),
            'status' => 'pending'
        ]);

        try {
            // Se usuário existe, enviar notificação no sistema
            if ($invitedUser) {
                $invitedUser->notify(new WorkspaceInviteNotification($collaborator, $workspace, auth()->user()));
            }

            // Enviar email sempre - usando queue se configurado
            Mail::to($request->email)->send(
                new WorkspaceInvitation($collaborator, $workspace, auth()->user())
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Convite enviado com sucesso!',
                'user_exists' => !is_null($invitedUser),
                'invitation_token' => $invitationToken // Útil para testes
            ]);

        } catch (\Exception $e) {
            // Log do erro
            \Log::error('Erro ao enviar convite: ' . $e->getMessage());
            
            // Opcional: deletar o colaborador se o email falhar
            $collaborator->delete();
            
            return response()->json([
                'error' => 'Falha ao enviar o convite. Por favor, tente novamente.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Listar colaboradores
     */
    public function listCollaborators($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        
        if (!$workspace->userHasAccess(auth()->user(), 'workspace.view')) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $collaborators = $workspace->collaborators()
            ->with(['user', 'inviter'])
            ->get()
            ->map(function($collab) {
                return [
                    'id' => $collab->id,
                    'email' => $collab->user ? $collab->user->email : $collab->invitation_email,
                    'role' => $collab->role,
                    'status' => $collab->status,
                    'invited_by' => $collab->inviter->name ?? 'Sistema',
                    'invited_at' => $collab->invited_at,
                    'joined_at' => $collab->joined_at
                ];
            });

        return response()->json(['success' => true, 'data' => $collaborators]);
    }

    /**
     * Remover colaborador
     */
    public function removeCollaborator($workspaceId, $collaboratorId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        
        if (!$workspace->userHasAccess(auth()->user(), 'collaborator.manage')) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }

        $collaborator = Collaborator::where('workspace_id', $workspaceId)
            ->where('id', $collaboratorId)
            ->firstOrFail();

        // Não remover owner
        if ($collaborator->isOwner()) {
            return response()->json(['error' => 'Não pode remover owner'], 422);
        }

        $collaborator->delete();

        return response()->json(['success' => true, 'message' => 'Colaborador removido']);
    }



    public function acceptInvite($token)
    {
        try {
            $collaborator = Collaborator::where('invitation_token', $token)
                ->whereNull('joined_at')
                ->firstOrFail();

            // Verificar se usuário logado é o dono do email do convite
            if (auth()->check() && auth()->user()->email !== $collaborator->invitation_email) {
                return redirect()->route('login')
                    ->with('error', 'Este convite é para outro usuário. Por favor, faça login com o email correto.');
            }

            // Se usuário não está logado, redirecionar para registro/login
            if (!auth()->check()) {
                session(['invitation_token' => $token]);
                return redirect()->route('register')
                    ->with('info', 'Por favor, crie uma conta ou faça login para aceitar o convite.');
            }

            // Aceitar o convite
            $collaborator->update([
                'user_id' => auth()->id(),
                'joined_at' => now(),
                'invitation_token' => null,
                'status' => 'accepted'
            ]);

            return redirect()->route('workspace.show', $collaborator->workspace_id)
                ->with('success', 'Convite aceito com sucesso!');

        } catch (\Exception $e) {
            return redirect()->route('workspaces.index')
                ->with('error', 'Convite inválido ou expirado.');
        }
    }

    /**
     * Aceitar convite por ID (via notificação)
     */
    public function acceptInviteById($id)
    {
        try {
            $collaborator = Collaborator::findOrFail($id);
            
            // Verificar permissões
            if ($collaborator->invitation_email !== auth()->user()->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este convite não é para você.'
                ], 403);
            }

            $collaborator->update([
                'joined_at' => now(),
                'invitation_token' => null,
                'status' => 'accepted'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Convite aceito com sucesso!',
                'redirect' => route('workspace.show', $collaborator->workspace_id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aceitar convite.'
            ], 500);
        }
    }

    /**
     * Recusar convite por ID (via notificação)
     */
    public function rejectInviteById($id)
    {
        try {
            $collaborator = Collaborator::findOrFail($id);
            
            // Verificar permissões
            if ($collaborator->invitation_email !== auth()->user()->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este convite não é para você.'
                ], 403);
            }

            $collaborator->update([
                'status' => 'rejected',
                'rejected_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Convite recusado.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recusar convite.'
            ], 500);
        }
    }

    /**
     * Recusar convite por token
     */
    public function rejectInvite($token)
    {
        try {
            $collaborator = Collaborator::where('invitation_token', $token)
                ->firstOrFail();

            $collaborator->update([
                'status' => 'rejected',
                'rejected_at' => now()
            ]);

            return redirect()->route('dashboard')
                ->with('info', 'Convite recusado.');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Convite inválido.');
        }
    }
}