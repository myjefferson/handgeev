<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EditRequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $editRequest;
    public $workspace;
    public $status;
    public $reason;

    public function __construct($editRequest, $workspace, $status, $reason = null)
    {
        $this->editRequest = $editRequest;
        $this->workspace = $workspace;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        if ($this->status === 'approved') {
            return (new MailMessage)
                ->subject('Solicitação de Edição Aprovada - ' . $this->workspace->title)
                ->greeting('Olá ' . $notifiable->name . '!')
                ->line('Sua solicitação para editar o workspace "' . $this->workspace->title . '" foi aprovada!')
                ->line('Você agora é um colaborador editor deste workspace.')
                ->action('Acessar Workspace', route('workspace.show', $this->workspace->id))
                ->line('Obrigado por usar nosso serviço!');
        } else {
            $mail = (new MailMessage)
                ->subject('Solicitação de Edição Rejeitada - ' . $this->workspace->title)
                ->greeting('Olá ' . $notifiable->name . '!')
                ->line('Sua solicitação para editar o workspace "' . $this->workspace->title . '" foi rejeitada.');

            if ($this->reason) {
                $mail->line('Motivo: ' . $this->reason);
            }

            $mail->line('Obrigado por seu interesse!');

            return $mail;
        }
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'edit_request_status',
            'workspace_id' => $this->workspace->id,
            'workspace_title' => $this->workspace->title,
            'status' => $this->status,
            'reason' => $this->reason,
            'request_id' => $this->editRequest->id
        ];
    }
}