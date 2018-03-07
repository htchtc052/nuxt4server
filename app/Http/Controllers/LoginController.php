<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
	use AuthenticatesUsers;

	

    protected $maxAttempts = 5;

    protected $decayMinutes = 1;

	
	protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if ($token) {
            $this->guard()->setToken($token);

            return true;
        }

        return false;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);
	 
		try {
			
			$token = (string) $this->guard()->getToken();
		} catch(JWTException $e) {
			return response()->json(['Server_error_token'], 500);
		}

		return response()->json(compact('token'), 200);
    }


	
	public function logout(Request $request)
    {
        $this->guard()->logout();
    }
}