<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Collaborator;
use App\Models\Workspace;
use App\Models\User;

class WorkspaceInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $collaborator;
    public $workspace;
    public $inviter;
    public $acceptUrl;
    public $isExistingUser;

    /**
     * Create a new message instance.
     */
    public function __construct(Collaborator $collaborator, Workspace $workspace, ?User $inviter = null)
    {
        $this->collaborator = $collaborator;
        $this->workspace = $workspace;
        $this->inviter = $inviter;
        $this->isExistingUser = (bool) $collaborator->user_id;
        
        // URL de aceitação baseada no token
        $this->acceptUrl = route('collaboration.invite.accept', $collaborator->invitation_token);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->getSubject();
        
        return $this->subject($subject)
                    ->view('sendmail.workspace-invitation')
                    ->with([
                        'workspace' => $this->workspace,
                        'collaborator' => $this->collaborator,
                        'inviter' => $this->inviter,
                        'acceptUrl' => $this->acceptUrl,
                        'isExistingUser' => $this->isExistingUser,
                    ]);
    }

    /**
     * Get the subject line based on user status
     */
    protected function getSubject(): string
    {
        if ($this->isExistingUser) {
            return "📋 Você foi convidado para o workspace: {$this->workspace->title}";
        }
        
        return "🎉 Convite para o Handgeev: {$this->workspace->title}";
    }
}