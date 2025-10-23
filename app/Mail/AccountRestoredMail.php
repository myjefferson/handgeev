<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountRestoredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $restoredAt;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $restoredAt)
    {
        $this->user = $user;
        $this->restoredAt = $restoredAt;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🎉 Bem-vindo de volta! Sua conta foi restaurada!')
                    ->view('sendmail.account-restored')
                    ->with([
                        'userName' => $this->user->name,
                        'restoredAt' => $this->restoredAt,
                    ]);
    }
}