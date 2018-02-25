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

    
    public function set(Request $request, $token, ChangeEmailService $changeEmailService)
    {
       
        try {
            $user = $changeEmailService->setEmail($token);
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
        ->to(\Config::get('services.frontend.url').'/auto_login?token='.$token.'&msg=email_set&email='.$user->email);

    }
   
}
