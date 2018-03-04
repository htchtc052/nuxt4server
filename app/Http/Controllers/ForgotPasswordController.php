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
    
        
        $validator= Validator::make($request->all(),$rules);

    	if($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
      
        try {
            $user = $changePassword->sendMail($request->get('email'));
        } catch (\Throwable $e){
            return response()->json(['Server_error_send_mail'], 500);
        }
      
        return response()->json(['success'], 200);
    }
    
    public function set(Request $request, ChangePasswordService $changePassword)
    {
        
        $rules =  [
			'password' => 'required|min:4',
			'confirm_password' => 'required|same:password'
        ];
    
        $validator= Validator::make($request->all(),$rules);

    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
       
        try {
            $user = $changePassword->change($request->get('email'), $request->get('token'), $request->get('password'));
        } catch (\Throwable $e){
            return response()->json(['server_error'], 500);
        }
      
        
        try {
            $token = JWTAuth::fromUser($user);
         } catch (\Throwable $e){
            return response()->json(['server_error_create_token'], 500);
        }

        return response()->json(compact('token'), 200);

    }
   
}
