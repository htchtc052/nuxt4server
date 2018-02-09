<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\ActivateEmailService;

class RegisterController extends Controller
{
    
    public function register(Request $request, ActivateEmailService $activateEmail)
    {
        $rules = [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|confirmed|min:4',
            'agree' => 'required',
        ];
        
        $messages =  [
            'email.required' => 'Please enter an email address',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This e-mail is already taken. ',
            'name.required' => 'Please enter your name',
            'password.required' => 'Please enter a password',
            'password.min' => 'Passwords must be 4 characters or more',
            'agree.required' => 'Please agree to the terms of service'
        ];

    	$validator= Validator::make($request->all(),$rules, $messages);

    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
        
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'is_verified' => 0,
        ]);

        $activateEmail->sendMail($user);

        $token = JWTAuth::fromUser($user);
      
        return response()->json(compact('token', 'user'));
    }
}
