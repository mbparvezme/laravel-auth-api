<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyNewEmailNotification extends Notification
{
    public function __construct(private string $newEmail) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $backendUrl = URL::temporarySignedRoute(
            'account.email.verify',
            now()->addHours(24),
            [
                'id'    => $notifiable->id,
                'email' => $this->newEmail,
            ]
        );

        $parsed = parse_url($backendUrl);
        parse_str($parsed['query'], $params);

        $frontendUrl = config('app.frontend_url') . '/verify-new-email?' . http_build_query([
            'id'        => $notifiable->id,
            'email'     => $this->newEmail,
            'expires'   => $params['expires'],
            'signature' => $params['signature'],
        ]);

        return (new MailMessage)
            ->subject(__('app.VERIFY_NEW_EMAIL_SUBJECT'))
            ->action(__('app.VERIFY_EMAIL_ACTION'), $frontendUrl);
    }
}
