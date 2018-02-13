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

    	if($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
      
        try {
            $user = $changePassword->sendMail($request->get('email'));
        } catch (\Throwable $e){
            return response()->json(['Email not sended'], 500);
        }
      
        return response()->json(['Email send to '.$user->email], 200);
    }
    
    public function set(Request $request, ChangePasswordService $changePassword)
    {
        
        $rules =  [
			'password' => 'required|min:4',
			'confirm_password' => 'required|same:password'
        ];
    
        $messages =  [
            'password.required' => 'Please enter a new password',
            'password.min' => 'New passwords must be 4 characters or more',
        ];

        $validator= Validator::make($request->all(),$rules);

    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
       
        try {
            $user = $changePassword->change($request->get('email'), $request->get('token'), $request->get('password'));
        } catch (\Throwable $e){
            return response()->json(['New password save problem'], 500);
        }
      
        
        try {
            $token = JWTAuth::fromUser($user);
         } catch (\Throwable $e){
            return response()->json(['Auth problem'], 500);
        }

        $message = 'Password set successfully!';

        return response()->json(compact('user', 'message'), 200)->withHeaders([
            'Access-Control-Expose-Headers' => 'auth_token',
            'auth_token' => $token,
        ]);

    }
   
}
