<?php

namespace App\Services;

use App\User;
use App\ChangePassword;
use Carbon\Carbon;
use App\Mail\ChangePass;
use Illuminate\Support\Str;
use Mail;

class ChangePasswordService
{
    public function sendMail($email)
    {
        $user = User::where('email', $email)->first();

        $changePassword =  ChangePassword::where('user_id', $user->id)
                ->first();

        $token = $this->getToken();

        if (!$changePassword) {
            ChangePassword::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);
        } else {
            $changePassword->forceFill([
                'token' => $token,
            ])->save();
        }

        Mail::to($user->email)->send(new ChangePass(array(
            'user' => $user,
            'token' => $token,
            ))
        );

        return $user;

    }

    public function change($email, $token, $password)
    {

        if (!$changePassword = ChangePassword::where('token', $token)
                ->where('updated_at', '>', Carbon::now()->addMinutes(-60))
                    ->first()
            ) {
            throw new \Exception("Link expired or invalid");
        }

        $user =  User::where('id', '=', $changePassword->user_id)
            -> where('email', '=', $email)
                ->first();
     
        if (!$user) {
            throw new \Exception("User for change password not found");
        }

        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        ChangePassword::where([['user_id', '=', $user->id], ['token', '=', $token]])->delete();
        
        return $user;
    }

    private function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

}
