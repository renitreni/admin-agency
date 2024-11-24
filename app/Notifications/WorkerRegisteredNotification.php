<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkerRegisteredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected $worker) {}

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
        $agency = $this->worker->worker->agency;
        $worker = $this->worker->worker;

        return (new MailMessage)
            ->from($agency->email)
            ->subject("{$agency->name} Viewed Your Application")
            ->bcc('renier.trenuela@gmail.com')
            ->line("Your application has been view by {$agency->name}.")
            ->line("NOTE: Please save this code {$worker->code} for your personal identification.");
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
