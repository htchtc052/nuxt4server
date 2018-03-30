<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActivateService;
use Throwable;


class ActivateController extends Controller
{
  
    public function send(Request $request)
    {
        $user = $request->user();

        try {
            $user->sendActivateToken();
        } catch (Throwable $e){
            return response()->json(['server_send_token_error'], 500);
        }
        
        return response()->json(['success'], 200);
    }

    public function set(Request $request)
    {   
        try {
           $user = $this->checkToken();
        }  catch (Throwable $e){
            //dd($e->getMessage());
            return redirect()->to(config('services.frontend.url').'/activate_set?msg=invalid_link');
        }
        
        try {
            $new_token =  $this -> setActivate($user);
        } catch (Throwable $e) {
              //dd($e->getMessage());
            return redirect()->to(config('services.frontend.url').'/activate_set?msg=server_error');
        }

        return redirect()
            ->to(config('services.frontend.url').'/activate_set?msg=success&token='.$new_token);
    }

    private function checkToken()
    {
        $payload = auth()->parseToken()->getPayload();
        $user = auth()->user();

        if ($payload["action"] != config('services.mail_actions.activate')) {
            throw new Exception("token_wrong_action");
        }

        return $user;
    }

    private function setActivate($user)
    {
        $user->setActivate();
        auth()->invalidate();

        return auth()->login($user);
    }
}
