<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Config;
use \App\Notifications\{Activate, PasswordReset};

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, TokenSendableTrait;

    protected $fillable = [
        'name', 'email', 'password', 'is_verified'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function setActivate()
    {
        $this->forceFill([
            'is_verified' => true
        ])->save();
    }

    public function updatePassword($password)
    {
        $this->forceFill([
            'password' => bcrypt($password),
        ])->save();
    }

    public function updateName($name)
    {
        $this->forceFill([
            'name' => $name,
        ])->save();
    }

    public function updateEmail($email)
    {
        $this->forceFill([
            'email' => $email,
        ])->save();
    }
}
