<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $cachedToken = Cache::get('password_reset_token_' . $notifiable->id);

            if ($cachedToken) {
                return (new MailMessage)
                    ->greeting('Reset Password')
                    ->line('Dear user,')
                    ->line('We got a request to reset your Moncip password. Here are your verification token:')
                    ->salutation("This code is valid for 5 minutes. If you didn't request this, you can ignore this email and your password won't be changed.")
                    ->markdown('vendor.notifications.email', [
                        'code' => $cachedToken
                    ]);
            }

            $url = config('app.frontend_url') . "/reset-password/$token?email={$notifiable->getEmailForPasswordReset()}";

            return (new MailMessage)
                ->greeting('Reset Password')
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->action('Reset Password', $url)
                ->salutation('This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.');
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $cachedCode = Cache::get('email_verification_' . $notifiable->id);

            if ($cachedCode) {
                return (new MailMessage)
                    ->greeting('Verify Your Email')
                    ->line('Dear user,')
                    ->line('Please use this verification code to verify your email.')
                    ->salutation('This code is valid for 5 minutes.')
                    ->markdown('vendor.notifications.email', [
                        'code' => $cachedCode
                    ]);
            }

            return (new MailMessage)
                ->greeting('Verify Your Email')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Now', $url);
        });
    }
}
