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
            return response()->json(['Server_error_send_mail'], 500);
        }

        
        try {
            $token = JWTAuth::fromUser($user);
         } catch (\Throwable $e){
            return response()->json(['Server_error_create_token'], 500);
        }
        
        return response()->json(compact('token'), 200);
    }
}
