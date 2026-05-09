<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('auth.otp_subject', [], 'en'))
            ->greeting(__('auth.otp_greeting', ['name' => $notifiable->name], 'en'))
            ->line(__('auth.otp_line1', [], 'en'))
            ->line("**{$this->code}**")
            ->line(__('auth.otp_line2', [], 'en'))
            ->line(__('auth.otp_line3', [], 'en'));
    }
}
