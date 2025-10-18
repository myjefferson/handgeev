<?php

namespace App\Notifications;

use App\Models\Collaborator;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class WorkspaceInviteNotification extends Notification
{
    use Queueable;

    public $collaborator;
    public $workspace;
    public $inviter;

    public function __construct(Collaborator $collaborator, Workspace $workspace, User $inviter)
    {
        $this->collaborator = $collaborator;
        $this->workspace = $workspace;
        $this->inviter = $inviter;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'workspace_invite',
            'workspace_id' => $this->workspace->id,
            'workspace_title' => $this->workspace->title,
            'inviter_id' => $this->inviter->id,
            'inviter_name' => $this->inviter->name,
            'role' => $this->collaborator->role,
            'invitation_id' => $this->collaborator->invitation_token,
            'message' => "{$this->inviter->name} convidou você para colaborar no workspace '{$this->workspace->title}' como " . $this->getRoleText($this->collaborator->role),
            'action_url' => route('collaborations.index'),
            'created_at' => now()->toDateTimeString()
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'workspace_invite',
            'data' => $this->toDatabase($notifiable),
            'read_at' => null,
            'created_at' => now()->toDateTimeString()
        ]);
    }

    protected function getRoleText($role)
    {
        $roles = [
            'viewer' => 'Visualizador',
            'editor' => 'Editor', 
            'admin' => 'Administrador',
            'owner' => 'Proprietário'
        ];
        
        return $roles[$role] ?? $role;
    }
}