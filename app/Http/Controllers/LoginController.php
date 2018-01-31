<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
	public function login(LoginFormRequest $request)
	{

		$credentials = $request->only('email', 'password');

		try {
			if(!$token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' => 'Invalid login credential'], 401);
			}
		} catch(JWTException $e) {
			return response()->json(['error' => 'Could not create token'], 500);
		}

		$user = $request->user();

		return response()->json(compact('token', 'user'));
	}
}