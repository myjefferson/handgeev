<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmailChangeConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $newEmail;

    public function __construct($user, $token, $newEmail)
    {
        $this->user = $user;
        $this->token = $token;
        $this->newEmail = $newEmail;
    }

    public function build()
    {
        try {
            Log::info('Tentando enviar email para: ' . $this->newEmail);
            
            return $this->subject('Confirme sua alteração de email - Handgeev')
                        ->view('sendmail.change-confirmation');
        } catch (\Exception $e) {
            Log::error('Erro ao construir email: ' . $e->getMessage());
            throw $e;
        }
    }

    // Adicione este método para verificar falhas
    public function failed(\Exception $exception)
    {
        Log::error('Falha no envio do email: ' . $exception->getMessage());
    }
}