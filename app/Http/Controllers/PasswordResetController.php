<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator, Throwable, Exception;
use App\User;

class PasswordResetController extends Controller
{
  

    public function send(Request $request)
    {
        $rules =  [
            'email' => 'required|email|exists:users',
        ];
        
        $validator= Validator::make($request->all(),$rules);

    	if($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
      
        $user = User::where('email', $request->get('email'))->first();
       
        try {
            $user -> sendPasswordResetToken();
        } catch (Throwable $e){
            return response()->json(['server_send_token_error'], 500);
        }

        return response()->json(['success'], 200);
    }
    

    public function check_before_set(Request $request) 
    {
        
        try {
            $user = $this->checkToken($request->get('email'));
        }  catch (Throwable $e){
             
             return response()->json(['password_reset_token_invalid', $e->getMessage()], 403);
        }

         return response()->json(compact('user'));
        
    }


    public function set(Request $request)
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
            $user = $this->checkToken($request->get('email'));
        }  catch (Throwable $e){
             return response()->json(['password_reset_token_invalid', $e->getMessage()], 403);
        }

        try {
            $token = $this->setNewPassword($user, $request->get('password'));
        }  catch (Throwable $e){
             return response()->json(['server_error', $e->getMessage()], 500);
        }

        return response()->json(compact('token', 'user'));
      
    }

    private function checkToken($email)
    {
        $payload = auth()->parseToken()->getPayload();
        $user = auth()->user();

        if ($payload["action"] != config('services.mail_actions.password_reset')) {
            throw new Exception("token_wrong_action");
        }

        if (!$email || $email != $payload['email'] || $email != $user->email) {
            throw new Exception("token_wrong_check_email");
        }

        return $user;
    }


    private function setNewPassword($user, $new_password)
    {
        //так как перейдя по ссылке он активировался, если не был активирован
        if (!$user->verified) {
            $user->setActivate();
        }

        $user->updatePassword($new_password);
        auth()->invalidate();

        return auth()->login($user);
    }
   
}
