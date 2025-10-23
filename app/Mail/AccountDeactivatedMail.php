<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountDeactivatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $deletedAt;
    public $daysRemaining;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $deletedAt, $daysRemaining)
    {
        $this->user = $user;
        $this->deletedAt = $deletedAt;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Sua conta foi desativada - Ainda há tempo para voltar!')
                    ->view('sendmail.account-deactivated')
                    ->with([
                        'userName' => $this->user->name,
                        'deletedAt' => $this->deletedAt,
                        'daysRemaining' => $this->daysRemaining,
                    ]);
    }
}