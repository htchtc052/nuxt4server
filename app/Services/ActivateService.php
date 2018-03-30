<?php

namespace App\Services;

use App\User;
use JWTAuth, Exception;
use App\Notifications\Activate;

class ActivateService
{
    public function sendToken($user)
    {
        $token = auth()
            ->setTTL(4)
                ->claims(['action' => config('services.mail_actions.activate')])
                    ->login($user);

        $user->notify(new Activate($token));
    }

    public function check()
    {

        $payload = JWTAuth::parseToken()->getPayload();
  
        if ($payload["action"] != config('services.mail_actions.activate')) {
            throw new Exception("token_wrong_action");
        }
        
        $user = JWTAuth::parseToken()->authenticate();
        
        return $user;
    }
}

