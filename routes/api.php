<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('login', 'LoginController@login');
Route::post('register', 'RegisterController@register');


Route::group(['middleware' => 'jwt_token_custom'], function()
{
    Route::get('user', 'UserController@show');
    Route::post('user/profile/update', 'UserController@updateProfile');
    Route::post('user/password/update', 'UserController@updatePassword');

    Route::post('activate_send_email', 'ActivateController@send');
});


Route::post('activate_set', 'ActivateController@set');

Route::post('password_send_email', 'ForgotPasswordController@send');

Route::post('password_set', 'ForgotPasswordController@set');


