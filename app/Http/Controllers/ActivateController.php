<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActivateEmailService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ActivateController extends Controller
{
    //
    public function send(Request $request, ActivateEmailService $activateEmail)
    {
        $user = $request->user();
        
        try {
            $activateEmail->sendMail($user);
        } catch (\Throwable $e){
            return response()->json(['Email not sended'], 500);
        }
        
        return response()->json(['Email send to '.$user->email], 200);
    }

    public function set(Request $request, ActivateEmailService $activateEmail)
    {
        try {
            $user = $activateEmail->activate($request->get('token'));
        } catch (\Throwable $e){
            return response()->json(['Link expired or invalid'], 500);
        }
       
        try {
            $token = JWTAuth::fromUser($user);
         } catch (\Throwable $e){
            return response()->json(['Auth problem'], 500);
        }

        $message = 'You have successfully verified your account';

        return response()->json(compact('user', 'message'), 200)->withHeaders([
            'Access-Control-Expose-Headers' => 'auth_token',
            'auth_token' => $token,
        ]);
    }
}
