<?php

namespace App\Services;

use App\User;
use App\Activation;
use Carbon\Carbon;
use App\Mail\ActivateMail;
use Mail;

class ActivateEmail
{
    public function sendMail($user)
    {
        if ($user -> is_verified) {
            throw new \Exception("User already verified");
        }

        $token = $this->getToken();

        $activation =  Activation::where('user_id', $user->id)
                ->first();

        if (!$activation) {
            Activation::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);
        } else {
            $activation->forceFill([
                'token' => $token,
            ])->save();
        }

        Mail::to($user->email)->send(new ActivateMail(array(
            'user' => $user,
            'token' => $token,
            ))
        );

    }

    public function activate($token)
    {
        if (!$activation = Activation::where('token', $token)
                ->where('updated_at', '>', Carbon::now()->addMinutes(-2))
                    ->first()
            ) {
            throw new \Exception("Verification link invalid");
        }

        $user = User::find($activation->user_id);
 
        if (!$user) {
            throw new \Exception("User for verification not found");
        }

        if ($user -> is_verified) {
            throw new \Exception("User already verified");
        }

        $user->forceFill([
            'is_verified' => true
        ])->save();

        Activation::where('token', $token)->delete();

        return $user;

    }

    private function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

}
