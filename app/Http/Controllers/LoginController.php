<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
	public function login(Request $request)
	{
		//return response()->json(['Error single test'], 500);
		
		$rules = [
			'email' => 'required|email',
			'password' => 'required'
		];

		$validator= Validator::make($request->all(),$rules);

    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}

		$credentials = $request->only('email', 'password');

		try {
			if(!$token = JWTAuth::attempt($credentials)) {
				return response()->json(['errors' => ['email' => ['Invalid login credential']]], 422);
			}
		} catch(JWTException $e) {
			return response()->json(['Server login error'], 500);
		}

		$user = \Auth::user();

		$message = "Welcome back ".$user->name;

		return response()->json(compact('token', 'message'), 200);
	}

	public function logout(Request $request)
    {
        \Auth::logout();
    }
}