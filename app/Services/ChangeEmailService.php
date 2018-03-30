<?php

namespace App\Services;

class ChangeEmailService
{
    private $new_email;
    private $new_token;
    private $user;

    public function getUser()
    {
        return $this->user;
    }

    public function getNewToken()
    {
        return $this->new_token;
    }

    public function check()
    {
        $payload = auth()->parseToken()->getPayload();

        if ($payload["action"] != config('services.mail_actions.change_email')) {
            throw new Exception("token_wrong_action");
        }

        $this->new_email = $payload["new_email"];

        $this->user = auth()->user();

        if (!$this->new_email || $this->new_email == $this->user->email) {
            throw new Exception("token_wrong_new_email");
        }
        
    }

    public function set()
    {
        $this->user->updateEmail($this->new_email);
        auth()->invalidate();
        $this->new_token = auth()->login($this->user);
    }
}
