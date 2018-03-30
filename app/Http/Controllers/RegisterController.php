<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Throwable;

class RegisterController extends Controller
{
 
    public function register(Request $request)
    {

        //return response()->json(['Error single test'], 500);
        $request['agree'] =  $request['agree'] ? 1 : '';

    	$rules =  [
            'email' => 'required|email|unique:users',
            'name' => 'required',
			'password' => 'required|min:4',
			'confirm_password' => 'required|same:password',
            'agree' => 'required',
        ];
       
        $validator= Validator::make($request->all(),$rules);
        
    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}
        
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'is_verified' => 0,
        ]);

        try {
            $user->sendActivateToken();
        } catch (Throwable $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

        
        try {
            $token = auth()->login($user);
         } catch (Throwable $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
        return response()->json(compact('token'), 200);
    }
}
