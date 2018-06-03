<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator, Throwable, Exception;
use App\User;
use Tymon\JWTAuth\Facades\{JwtFactory, JwtAuth};

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
           $token =  $user -> sendPasswordResetToken();
        } catch (Throwable $e){
            return response()->json(['server_send_token_error'.$e->getMessage()], 500);
        }

        return response()->json(['success '.$token], 200);
    }
    

    public function check_before_set(Request $request) 
    {
        try {
            $user = $this->checkToken($request->get('reset_password_token'), $request->get('email'));
        }  catch (Throwable $e){
             
             return response()->json(['check_token_error '.$e->getMessage()], 403);
        }

         return response()->json(['check_password_token_ok '], 200);
        
    }


    public function set(Request $request)
    {
        try {
            $user = $this->checkToken($request->get('reset_password_token'), $request->get('email'));
        }  catch (Throwable $e){
             return response()->json(['check_token_error'], 403);
        }

        $rules =  [
			'password' => 'required|min:4',
			'confirm_password' => 'required|same:password'
        ];
    
        $validator= Validator::make($request->all(),$rules);

    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
        }
        
       

        try {
            //так как перейдя по ссылке он активировался, если не был активирован
            if (!$user->verified) {
                $user->setActivate();
            }
            $user->updatePassword($request->get('password'));
            
            JWTAuth::setToken($request->get('reset_password_token'));
            JWTAuth::invalidate(); 
            $token = JWTAuth::fromUser($user);
        }  catch (Throwable $e){
             return response()->json(['set_password_error '.$e->getMessage()], 500);
        }

        return response()->json(compact('user', 'token'));
      
    }

    private function checkToken($reset_password_token, $email)
    {
      
        JWTauth::setToken($reset_password_token);
        $payload = JWTAuth::getPayload();
        $user = JWTAuth::authenticate();
        
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
    
    }
   
}
