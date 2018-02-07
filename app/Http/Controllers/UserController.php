<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
	public function show(Request $request)
	{
		$user = $request->user();

		return response()->json(['userController' => true, 'user' => $user]);
	}

	public function updateProfile(Request $request)
	{
		$rules = [
			'name'  => 'required',
			'email' => 'required|email|',
		];

		$this->validate($request, $rules);

		$user = $request->user();
		$user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->save();

		return response()->json(compact('user'));
	}

	public function updatePassword(Request $request)
	{
		$rules = [
			'new_password'         => 'required',
			'confirm_new_password' => 'required|same:new_password'
		];

		$this->validate($request, $rules);

		$user = $request->user();
		$user->password = bcrypt($request->input('new_password'));
		$user->saveOrFail();

		return response()->json(compact('user'));
	}
}