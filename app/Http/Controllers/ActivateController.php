<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\{JwtFactory, JwtAuth};
use Throwable;


class ActivateController extends Controller
{
  
    public function send(Request $request)
    {
        $user = $request->user();

        try {
            $user->sendActivateToken();
        } catch (Throwable $e){
            return response()->json(['server_send_token_error '.$e->getMessage()], 500);
        }
        
        return response()->json(['success'], 200);
    }

    public function set(Request $request)
    {   
        try {
           $user = $this->checkToken($request->get('activate_token'));
        }  catch (Throwable $e){
            return response()->json(['activate_token_invalid '.$e->getMessage()], 403);
           //return redirect()->to(config('services.frontend.url').'/login?msg=activation_error');
        }
        
        try {
            $user->setActivate();
                
            JWTAuth::setToken($request->get('activate_token'));
            JWTAuth::invalidate(); 
            $new_token = JWTAuth::fromUser($user);

        } catch (Throwable $e) {
            // return redirect()->to(config('services.frontend.url').'/login?msg=activation_error');
            return response()->json(['server_error '.$e->getMessage()], 403);
        }

        return response()->json(compact('new_token', 'user'));
    }

    private function checkToken($activate_token)
    {
        JWTauth::setToken($activate_token);
        $payload = JWTAuth::getPayload();
        $user = JWTAuth::authenticate();

        if ($payload["action"] != config('services.mail_actions.activate')) {
            throw new \Exception("token_wrong_action");
        }

        return $user;
    }

   
}
