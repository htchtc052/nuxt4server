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
      

        $user = $request->user();
        

		try {
			$changeEmailService->sendChangeEmailMail($user, $request->get('email'));
		} catch (\Throwable $e){
            return response()->json([
    		    'success' => false,
    		    'error' => 'Error! '.$e->getMessage(),
    		], 422);
        }

        $message =  'Change link sending to '.$request->get('email');
        
        return response()->json(compact('user', 'message'));
	}

    
    public function setEmail(Request $request, ChangeEmailService $changeEmailService)
    {
       
        try {
            $user = $changeEmailService->setEmail($request->get('token'));
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
            'message' => 'New email '.$user->email.' set successfully!',
        ], 200);

    }
   
}
