<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\ActivateEmail;

class RegisterController extends Controller
{
    
    public function register(Request $request, ActivateEmail $activateEmail)
    {
        $rules=[
    		'name'=>'required|max:255',
    		'email'=>'required|max:255|unique:users',
    		'password'=>'required|confirmed|min:6'
    	];

    	$validator= Validator::make($request->all(),$rules);

    	if($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
        
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'is_verified' => 0,
        ]);

        $activateEmail->sendMail($user);

        $token =  JWTAuth::fromUser($user);
        
        $message = 'Thanks for signing up! Please check your email to complete your registration.';

        return response()->json(compact('token', 'user', 'message'));
    }
}
