<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActivateEmail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ActivateController extends Controller
{
    //
    public function send(Request $request, ActivateEmail $activateEmail)
    {
        $user = $request -> user();
        
        try {
            $activateEmail->sendMail($user);
        } catch (\Throwable $e){
            return response()->json([
    		    'success' => false,
    		    'error' => 'Error! '.$e->getMessage(),
    		], 422);
        }
        
        return response()->json(['success' => true, 'message' => 'Email send to '.$user->email], 200);
    }

    public function set(Request $request, ActivateEmail $activateEmail)
    {
        try {
            $user = $activateEmail->activate($request->get('token'));
        } catch (\Throwable $e){
            return response()->json([
    		    'success' => false,
    		    'error' => 'Error! '.$e->getMessage(),
    		], 422);
        }
       
        $token =  JWTAuth::fromUser($user);

        return response()->json([
            'success'=>true,
            'user' => $user,
            'token' => $token,
            'message'=>'you have successfully verified your account'
        ], 200);
    }
}
