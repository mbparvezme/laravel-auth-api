<?php

namespace App\Features\MagicLink\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MagicLinkNotification extends Notification
{
    public function __construct(private string $rawToken, private string $email) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url') . '/magic-login?' . http_build_query([
            'token' => $this->rawToken,
            'email' => $this->email,
        ]);

        return (new MailMessage)
            ->subject(__('app.MAGIC_LINK_SUBJECT'))
            ->line(__('app.MAGIC_LINK_LINE'))
            ->action(__('app.MAGIC_LINK_ACTION'), $url)
            ->line(__('app.MAGIC_LINK_EXPIRY'));
    }
}
