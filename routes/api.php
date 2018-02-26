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


Route::post('logout', 'LoginController@logout');

Route::group(['middleware' => 'guest:api'], function () {

    Route::post('login', 'LoginController@login');
    Route::post('register', 'RegisterController@register');
    
    Route::post('password_send_email', 'ForgotPasswordController@send');
    Route::post('password_set', 'ForgotPasswordController@set');

    Route::post('oauth/{driver}', 'OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'OAuthController@handleProviderCallback')->name('oauth.callback');    
});

Route::post('activate_set', 'ActivateController@set');
Route::get('activate_set/{token}', 'ActivateController@set')->name('activate_set');

Route::post('email_set', 'ChangeEmailController@setEmail');
Route::get('email_set/{token}', 'ChangeEmailController@set')->name('email_set');


Route::group(['middleware' => 'jwt_token_custom'], function()
{
    Route::get('user', 'UserController@show');
    Route::post('user/update', 'UserController@updateProfile');
    Route::post('user/password', 'UserController@updatePassword');
    Route::post('user/email', 'ChangeEmailController@sendMail');
    Route::post('activate_send_email', 'ActivateController@send');
   
});





