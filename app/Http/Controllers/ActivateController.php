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

    public function set(Request $request, $token, ActivateEmailService $activateEmail)
    {
        try {
            $user = $activateEmail->activate($token);
        } catch (\Throwable $e){
            return redirect()
            ->to(\Config::get('services.frontend.url').'/auth_error?msg=invalid_link');
        }
       
        try {
            $token = JWTAuth::fromUser($user);
         } catch (\Throwable $e){
            return redirect()
            ->to(\Config::get('services.frontend.url').'/auth_error?msg=invalid_link');
        }

        return redirect()
            ->to(\Config::get('services.frontend.url').'/auto_login?token='.$token.'&msg=activate');
    }
}
