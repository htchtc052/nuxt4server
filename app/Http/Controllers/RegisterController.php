<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RegisterController extends Controller
{
    
    public function register(RegisterFormRequest $request)
    {
        $user = User::create([
            'name' => $request->json('name'),
            'email' => $request->json('email'),
            'password' => bcrypt($request->json('password')),
        ]);

        $token =  JWTAuth::fromUser($user);

        return response()->json(compact('token', 'user'));
    }
}
