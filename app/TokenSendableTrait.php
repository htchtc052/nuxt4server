<?php

namespace App;

use App\Notifications\{Activate, ChangeEmail, PasswordReset};

trait TokenSendableTrait
{
    public function sendActivateToken()
    {
        $token = auth()
            ->setTTL(8)
                ->claims(['action' => config('services.mail_actions.activate')])
                    ->login($this);
 
        $this->notify(new Activate($token));
    }
   
    public function sendPasswordResetToken()
    {
        $token = auth()
            ->setTTL(4)
                ->claims(['action' => config('services.mail_actions.password_reset'), 'email' => $this->email])
                    ->login($this);

        $this->notify(new PasswordReset($token));
    }

    public function sendChangeEmailToken($new_email)
    {
        $token = auth()
            ->setTTL(8)
                ->claims(['action' => config('services.mail_actions.email_change'), 'new_email' => $new_email])
                    ->login($this);
 
        $this->notify(new ChangeEmail($token, $new_email));
    }
    
}