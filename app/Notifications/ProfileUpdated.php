<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $user;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $message = 'قام المستخدم بتحديث بياناته الشخصية')
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'message' => $this->message,
            'avatar' => $this->user->avatar_url,
            'type' => 'profile_update',
        ];
    }
}
