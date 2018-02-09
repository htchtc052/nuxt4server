<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Services\ChangePasswordService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ForgotPasswordController extends Controller
{
    //
    public function send(Request $request, ChangePasswordService $changePassword)
    {
        $rules =  [
            'email' => 'required|email|exists:users',
        ];
    
        $messages =  [
            'email.required' => 'Please enter an email',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'This e-mail is not registered. ',
        ];
        
        $validator= Validator::make($request->all(),$rules, $messages);

        if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}

        try {
            $user = $changePassword->sendMail($request->get('email'));
        } catch (\Throwable $e){
            return response()->json([
    		    'success' => false,
    		    'error' => 'Error! '.$e->getMessage(),
    		], 422);
        }
      
        return response()->json(['success' => true, 'message' => 'Email send to '.$user->email], 200);


    }
    
    public function set(Request $request, ChangePasswordService $changePassword)
    {
       $rules =  [
            'password' => 'required|confirmed|min:4',
        ];
    
        $messages =  [
            'password.required' => 'Please enter a new password',
            'password.min' => 'New passwords must be 4 characters or more',
            'password.confirmed' => 'The password confirmation does not match.',
        ];


        $validator= Validator::make($request->all(),$rules, $messages);

        if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
       
        try {
            $user = $changePassword->change($request->get('email'), $request->get('token'), $request->get('password'));
        } catch (\Throwable $e){
            return response()->json([
    		    'success' => false,
    		    'error' => 'Error! '.$e->getMessage(),
    		], 422);
        }
      
        $token =  JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'Password set successfully!',
        ], 200);

    }
   
}
