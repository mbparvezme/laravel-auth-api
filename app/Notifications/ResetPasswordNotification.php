<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url') . '/reset-password?' . http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject(__('app.PASSWORD_RESET_SUBJECT'))
            ->line(__('app.PASSWORD_RESET_LINE'))
            ->action(__('app.PASSWORD_RESET_ACTION'), $frontendUrl)
            ->line(__('app.PASSWORD_RESET_EXPIRY'));
    }
}
