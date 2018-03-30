<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ChangeEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $new_email)
    {
        $this->token = $token;
        $this->new_email = $new_email;
    }

    

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $notifiable->email = $this->new_email;

        return (new MailMessage)
            ->subject(trans('emails.change_email.subject', ['appname' => config('app.name')]))
            ->markdown('emails.change_email', [
                'user' => $notifiable,
                'new_email' => $this->new_email,
                'token' => $this->token,
            ]);
    }

}
