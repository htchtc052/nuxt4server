<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator, Throwable, Exception;

class ChangeEmailController extends Controller
{
    public function sendMail(Request $request)
	{
		$rules =  [
            'email' => ['required', 'email', 'unique:users'],
        ];
    
        
        $validator= Validator::make($request->all(),$rules);

    	if($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
      
        $user = $request->user();
       
		try {
			$user->sendChangeEmailToken($request->get('email'));
		} catch (Throwable $e){
            return response()->json(['Server_error_send_email', $e->getMessage()], 500);
        }

        return response()->json(['success'], 200);
	}

    
    public function set(Request $request)
    {
        try {
           $user =  $this -> checkToken($request->get('email'));
         }  catch (Throwable $e){
           // dd($e->getMessage());
            return redirect()->to(config('services.frontend.url').'/email_set?msg=invalid_link');
         }

         try {
            $new_token =  $this -> setNewEmail($user, $request->get('email'));
         } catch (Throwable $e) {
            //  dd($e->getMessage());
             return redirect()->to(config('services.frontend.url').'/email_set?msg=server_error');
         }
        return redirect()
        ->to(config('services.frontend.url').'/email_set?msg=success&token='.$new_token.'&email='.$user->email);

    }

    private function checkToken($new_email)
    {
        $payload = auth()->parseToken()->getPayload();
        $user = auth()->user();

        if ($payload["action"] != config('services.mail_actions.email_change')) {
            throw new Exception("token_wrong_action");
        }

        if (!$new_email || $new_email != $payload['new_email'] || $new_email == $user->email) {
            throw new Exception("token_wrong_new_email");
        }

        return $user;
    }

    private function setNewEmail($user, $new_email)
    {
        $user->updateEmail($new_email);
        auth()->invalidate();

        return auth()->login($user);
    }
}
