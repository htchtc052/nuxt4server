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

        $this->createActivation($user, $token);

        Mail::to($user->email)->send(new ActivateMail(array(
            'user' => $user,
            'token' => $token,
            ))
        );

    }

    public function activate($token)
    {
        if (!$activation = Activation::where('token', $token)->first()) {
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


    private function createActivation($user, $token)
    {
        $activation = Activation::where('user_id', $user->id)->first();

        if (!$activation) {
            Activation::insert([
                'user_id' => $user->id,
                'token' => $token,
            ]);
        } else {
            $activation->forceFill([
                'token' => $token,
            ])->save();
        }

    }

    private function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

}
