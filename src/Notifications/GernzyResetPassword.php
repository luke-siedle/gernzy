<?php

namespace Lab19\Cart\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GernzyResetPassword extends Notification
{
    use Queueable;

    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', route('password.reset.token', ['token' => $this->token]))
            ->line('If you did not request a password reset, no further action is required.');
    }


    //Get the token.
    public function getToken()
    {
        return $this->token;
    }
}
