<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public $verificationUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
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
                    ->subject('Sahkan Alamat E-mel Anda')
                    ->greeting('Helo ' . $notifiable->name . '!')
                    ->line('Terima kasih kerana mendaftar dengan E-Tempah sistem.')
                    ->line('Sila sahkan alamat e-mel anda dengan mengklik butang di bawah:')
                    ->action('Sahkan E-mel', $this->verificationUrl)
                    ->line('Pautan ini akan tamat tempoh dalam 24 jam.')
                    ->line('Jika anda tidak membuat pendaftaran ini, sila abaikan e-mel ini.')
                    ->salutation('Sekian, Pasukan E-Tempah');
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
