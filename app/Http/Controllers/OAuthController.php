<?php

namespace App\Http\Controllers;

use App\User;
use App\OAuthProvider;
use App\Http\Controllers\Controller;
use App\Exceptions\EmailTakenException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
            'services.vkontakte.redirect' => route('oauth.callback', 'vkontakte'),
            'services.yandex.redirect' => route('oauth.callback', 'yandex'),
            'services.odnoklassniki.redirect' => route('oauth.callback', 'odnoklassniki'),
            'services.mailru.redirect' => route('oauth.callback', 'mailru'),
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

    public function handleProviderCallback(Request $request, $provider)
    {
        if (!$request->has('code') || $request->has('denied')) {
            return redirect()
            ->to(\Config::get('services.frontend.url').'/auth_error?msg=social_no_email');
        }

        try {
            $user_socialite = Socialite::driver($provider)->stateless()->user();
        } catch (Exception $e) {
            return redirect()
                ->to(\Config::get('services.frontend.url').'/auth_error?msg=social_error');
        }
        
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
            $user_socialite_email = $this->getUserSocialiteEmail($user_socialite);
            
            if (!$user_socialite_email) {
                return redirect()
                ->to(\Config::get('services.frontend.url').'/auth_error?msg=social_no_email');
            }
        
            if (!$user =  User::where('email', $user_socialite_email)->first()) {
                $user = User::create([
                    'name' => $user_socialite->getName(),
                    'email' => $user_socialite_email,
                    'is_verified' => 1,
                ]);
        
                $user->oauthProviders()->create([
                    'provider' => $provider,
                    'provider_user_id' => $user_socialite->getId(),
                    'access_token' => $user_socialite->token,
                    'refresh_token' => $user_socialite->refreshToken,
                ]);
            }   
        }

        $this->guard()->setToken(
            $token = $this->guard()->login($user)
        );

        return redirect()
            ->to(\Config::get('services.frontend.url').'/auto_login?token='.$token.'&msg=social');
    }

    private function getUserSocialiteEmail($user_socialite)
    {
        if ($user_socialite -> getEmail()) {
            return $user_socialite -> getEmail();
        }

        if (isset($user_socialite->accessTokenResponseBody["email"])) {
            return $user_socialite->accessTokenResponseBody["email"];
        }

        return null;

    }
}
