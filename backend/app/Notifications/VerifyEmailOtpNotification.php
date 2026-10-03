<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailOtpNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Code de vérification de votre adresse email')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Merci de vous être inscrit sur Yass Digital Lab.')
            ->line('Veuillez utiliser le code ci-dessous pour vérifier votre adresse email :')
            ->line(new \Illuminate\Support\HtmlString('<div style="text-align: center; margin: 20px 0;"><span style="font-size: 24px; font-weight: bold; padding: 10px 20px; background-color: #f3f4f6; border-radius: 5px; letter-spacing: 2px;">' . $notifiable->otp_code . '</span></div>'))
            ->line('Ce code expirera dans 15 minutes.')
            ->line('Si vous n\'avez pas créé de compte, aucune action n\'est requise.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
