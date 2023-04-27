<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $verifocationUrl = env('EMAIL_VERIFICATION_URL', "https://www.mbparvez.me/verify?verify_url") . $url;

            $subject = env('APP_NAME', 'mbparvez.me') . 'Verify Email Address';
            return (new MailMessage)->subject($subject)
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $verifocationUrl);
        });

    }
}
