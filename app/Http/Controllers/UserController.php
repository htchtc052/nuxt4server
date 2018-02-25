<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use Auth;
use App\Services\ChangeEmailService;

class UserController extends Controller
{
	public function show(Request $request)
	{
		$user = Auth::user();

		return response()->json(compact('user'));
	}

	public function updateProfile(Request $request)
	{
		$rules = [
			'name'  => 'required',
		];

		$messages =  [
            'name.required' => 'Please enter your name',
        ];

		$validator= Validator::make($request->all(),$rules, $messages);

    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}

		$user = $request->user();
		$user->updateName($request->get('name'));

		$message = 'Profile update successfully';

		return response()->json(compact('user', 'message'));
	}

	public function updatePassword(Request $request)
	{
		$rules =  [
			'password' => 'required|min:4',
			'confirm_password' => 'required|same:password'
        ];
    
        $messages =  [
            'password.required' => 'Please enter a new password',
            'password.min' => 'New passwords must be 4 characters or more',
        ];

		$validator= Validator::make($request->all(),$rules, $messages);

    	if ($validator->fails()){
    		return response()->json(['errors' => $validator->messages()], 422);
    	}

		$user = $request->user();

        $user->updatePassword($request->get('password'));

		$message = 'Password update successfully';
		
		return response()->json(compact('user', 'message'));
	}




}