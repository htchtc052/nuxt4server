<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.change_email', [
                'user' => $this->data['user'], 
                'email' => $this->data['email'], 
                'token' => $this->data['token']
            ]
        )->subject(trans('emails.change_email.subject', ['appname' => config('app.name')]));
    }
}
