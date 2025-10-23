<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangeConfirmation extends Mailable
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
        return $this->subject('Confirme sua alteração de email - Handgeev')
                    ->view('sendmail.change-confirmation');
    }
}