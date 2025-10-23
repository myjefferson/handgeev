<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationCode;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $verificationCode)
    {
        $this->user = $user;
        $this->verificationCode = $verificationCode;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🔐 Código de Verificação - Handgeev')
                    ->view('sendmail.verification-code')
                    ->with([
                        'user' => $this->user,
                        'verificationCode' => $this->verificationCode,
                    ]);
    }
}