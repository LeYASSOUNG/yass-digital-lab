<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $verifyUrl = $frontendUrl . '/verify-email?url=' . urlencode($signedUrl);

        return (new MailMessage)
            ->subject('Vérifiez votre adresse email - Yass Digital Lab')
            ->greeting('Bienvenue ' . $notifiable->name . ' !')
            ->line('Merci de vous être inscrit sur Yass Digital Lab. Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse email.')
            ->action('Vérifier mon adresse email', $verifyUrl)
            ->line('Si vous n\'avez pas créé de compte, vous pouvez ignorer cet email.');
    }
}
