<?php

namespace App\Services;

use App\User;
use App\ChangeEmail;
use Carbon\Carbon;
use App\Mail\ChangeEmailMail;
use Illuminate\Support\Str;
use Mail;

class ChangeEmailService
{
    public function sendChangeEmailMail($user, $email)
    {
        $token = $this->getToken();

        $changeEmail =  ChangeEmail::where('user_id', $user->id)
                ->first();

        if (!$changeEmail) {
            ChangeEmail::create([
                'user_id' => $user->id,
                'token' => $token,
                'email' => $email,
            ]);
        } else {
            $changeEmail->forceFill([
                'token' => $token,
                'email' => $email,
            ])->save();
        }

        \Mail::to($email)->send(
            new ChangeEmailMail(array(
                'user' => $user,
                'email' => $email,
                'token' => $token,
            ))
        );
    }

    public function setEmail($token)
    {
        if (!$changeEmail = ChangeEmail::where('token', $token)
        ->where('updated_at', '>', Carbon::now()->addMinutes(-60))
            ->first()
            ) {
            throw new \Exception("Link expired or invalid");
        }

        $user =  User::where('id', '=', $changeEmail->user_id)
                ->first();

        if (!$user) {
            throw new \Exception("User for set email not found");
        }

        $user->updateEmail($changeEmail->email);

        ChangeEmail::where([['user_id', '=', $user->id], ['token', '=', $token]])->delete();

        return $user;
        
    }

    private function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

}
