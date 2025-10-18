<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EditRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $collaboratorRequest;
    public $workspace;
    public $requestedUser;

    public function __construct($collaboratorRequest, $workspace, $requestedUser)
    {
        $this->collaboratorRequest = $collaboratorRequest;
        $this->workspace = $workspace;
        $this->requestedUser = $requestedUser;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nova Solicitação de Edição - ' . $this->workspace->title)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line($this->requestedUser->name . ' (' . $this->requestedUser->email . ') solicitou permissão para editar seu workspace "' . $this->workspace->title . '".')
            ->line('Plano do usuário: ' . $this->getUserPlanName())
            ->line('Mensagem: ' . ($this->collaboratorRequest->request_message ?: 'Nenhuma mensagem fornecida.'))
            ->action('Ver Solicitações', route('workspace.settings', $this->workspace->id))
            ->line('Obrigado por usar nosso serviço!');
    }

    private function getUserPlanName()
    {
        if ($this->requestedUser->isAdmin()) return 'Administrador';
        if ($this->requestedUser->isPremium()) return 'Premium';
        if ($this->requestedUser->isPro()) return 'Pro';
        if ($this->requestedUser->isStart()) return 'Start';
        return 'Free';
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'edit_request',
            'workspace_id' => $this->workspace->id,
            'workspace_title' => $this->workspace->title,
            'requested_by_id' => $this->requestedUser->id,
            'requested_by_name' => $this->requestedUser->name,
            'requested_by_email' => $this->requestedUser->email,
            'requested_by_plan' => $this->getUserPlanName(),
            'request_id' => $this->collaboratorRequest->id,
            'message' => $this->collaboratorRequest->request_message
        ];
    }
}