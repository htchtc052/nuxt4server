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

        //return response()->json(['Error single test'], 500);
        $request['agree'] =  $request['agree'] ? 1 : '';

    	$rules =  [
            'email' => 'required|email|unique:users',
            'name' => 'required',
			'password' => 'required|min:4',
			'confirm_password' => 'required|same:password',
            'agree' => 'required',
        ];
    
        $messages =  [
            'email.required' => 'Please enter an email address',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This e-mail is already taken. ',
            'name.required' => 'Please enter your name',
            'password.required' => 'Please enter a new password',
            'password.min' => 'New passwords must be 4 characters or more',
            'agree.required' => 'Please agree to the terms of service'
        ];


        $validator= Validator::make($request->all(),$rules);

        
    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
        
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'is_verified' => 0,
        ]);

        try {
            $activateEmail->sendMail($user);
        } catch (\Throwable $e){
            return response()->json(['Send registration link failed'], 500);
        }

        
        try {
            $token = JWTAuth::fromUser($user);
         } catch (\Throwable $e){
            return response()->json(['Auth problem'], 500);
        }
        
        $message = "Activation link sended to your email ".$user->email;

        return response()->json(compact('token', 'message'), 200);
    }
}
