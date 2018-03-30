<?php

namespace App\Services;

use App\User;
use JWTAuth, Exception;
use App\Notifications\PasswordReset;

class PasswordResetService
{
    public function sendToken($user)
    {
        $token = auth()
            ->setTTL(4)
                ->claims(['action' => config('services.mail_actions.password_reset')])
                    ->login($user);

        $user->notify(new PasswordReset($token));
    }

    public function checkTokenAndEmail($token, $email)
    {
        
        $payload = JWTAuth::parseToken()->getPayload();
        
        if ($payload["action"] != config('services.mail_actions.password_reset')) {
            throw new Exception("token_wrong_action");
        }

        $user = JWTAuth::parseToken()->authenticate();

        if ($user->email != $email) {
            throw new Exception("email_wrong");
        }

        return $user;
    }
}

