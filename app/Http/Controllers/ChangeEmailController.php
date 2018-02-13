<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Services\ChangeEmailService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ChangeEmailController extends Controller
{
    //
    public function sendMail(Request $request, ChangeEmailService $changeEmailService)
	{
		$rules =  [
            'email' => ['required', 'email', 'unique:users'],
        ];
    
        $messages =  [
            'email.required' => 'Please enter an email',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This e-mail is already taken.',
        ];
        
        $validator= Validator::make($request->all(),$rules);

    	if($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
      
        $user = \Auth::user();
       
		try {
			$changeEmailService->sendChangeEmailMail($user, $request->get('email'));
		} catch (\Throwable $e){
            return response()->json(['Change link not sended'], 500);
        }

        return response()->json(['Change link sended '.$user->email], 200);
	}

    
    public function setEmail(Request $request, ChangeEmailService $changeEmailService)
    {
       
        try {
            $user = $changeEmailService->setEmail($request->get('token'));
        } catch (\Throwable $e){
            return response()->json(['Link expired or invalid'], 500);
        }
      
        try {
            $token = JWTAuth::fromUser($user);
         } catch (\Throwable $e){
            return response()->json(['Auth problem'], 500);
        }

        $message = 'New email '.$user->email.' set successfully!';

        return response()->json(compact('user', 'message'), 200)->withHeaders([
            'Access-Control-Expose-Headers' => 'auth_token',
            'auth_token' => $token,
        ]);

    }
   
}
