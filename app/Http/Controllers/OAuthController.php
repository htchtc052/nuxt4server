<?php

namespace App\Http\Controllers;

use App\User;
use App\OAuthProvider;
use App\Http\Controllers\Controller;
use App\Exceptions\EmailTakenException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class OAuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        config([
            'services.github.redirect' => route('oauth.callback', 'github'),
            'services.facebook.redirect' => route('oauth.callback', 'facebook'),
            'services.twitter.redirect' => route('oauth.callback', 'twitter'),
            'services.google.redirect' => route('oauth.callback', 'google'),
        ]);
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return [
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ];
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param  string $driver
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $user_socialite = Socialite::driver($provider)->stateless()->user();
       
        $oauthProvider = OAuthProvider::where('provider', $provider)
        ->where('provider_user_id', $user_socialite->getId())
        ->first();


        if ($oauthProvider) {
            $oauthProvider->update([
                'access_token' => $user_socialite->token,
                'refresh_token' => $user_socialite->refreshToken,
            ]);

            $user = $oauthProvider->user;
        } else {
            if (!$user_socialite -> email) {
                return redirect()
                ->to(\Config::get('services.frontend.url').'/auth_error?msg=social_no_email');
            }
        
            if (!$user =  User::where('email', $user_socialite->getEmail())->first()) {
                 $user =  $this->createUser($provider, $user_socialite);
            }   
        }

        $this->guard()->setToken(
            $token = $this->guard()->login($user)
        );

        return redirect()
            ->to(\Config::get('services.frontend.url').'/auto_login?token='.$token.'&msg=social');
    }

    /**
     * @param  string $provider
     * @param  \Laravel\Socialite\Contracts\User $sUser
     * @return User
     */
    protected function createUser($provider, $user_socialite)
    {
        $user = User::create([
            'name' => $user_socialite->getName(),
            'email' => $user_socialite->getEmail(),
            'is_verified' => 1,
        ]);

        $user->oauthProviders()->create([
            'provider' => $provider,
            'provider_user_id' => $user_socialite->getId(),
            'access_token' => $user_socialite->token,
            'refresh_token' => $user_socialite->refreshToken,
        ]);

        return $user;
    }

}
