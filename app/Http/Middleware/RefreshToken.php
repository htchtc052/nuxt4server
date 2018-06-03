<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Http\Middleware;
use \Tymon\JWTAuth\Http\Middleware\BaseMiddleware as VendorMiddleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Facades\{JwtFactory, JwtAuth};
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;


class RefreshToken extends VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Log::info("RefreshToken.php request_token ".$request->get('token'));
    
       

        try
        {
            //$user = JWTAuth::setToken($request->get('token'))->authenticate();
            JWTAuth::parseToken();

            \Log::info("RefreshToken.php   after parse token ".JWTauth::getToken());
            $user = JWTauth::authenticate();


            if (!$user) {
                
                \Log::info("RefreshToken.php error_get_user");
                 return response()->json(['error_get_user'], 401);
            }

            ///$token = JWTAuth::getToken();
            
            \Log::info("RefreshToken.php get user from current  user ".$user->email);
            //return response()->json(compact('user'), 200);
        } catch (TokenExpiredException $e) {
            \Log::info("RefreshToken.php token_expired ".$e->getMessage());
            try  {
                $new_token = JWTAuth::refresh(JWTAuth::getToken());
                $user = JWTAuth::setToken($new_token)->toUser();
                
                \Log::info("RefreshToken.php sucess_refresh new_token ".$new_token);
                //return response()->json(compact('token', 'user'), 200);

            } catch (JWTException $e) {
                
                 \Log::info("RefreshToken.php error_refreshed ");
                return response()->json(['error_refreshed'.$e->getMessage()], 401);
            }
        } catch (JWTException $e) {
            \Log::info("RefreshToken.php error_parse ");
            return response()->json(['error_parse'.$e->getMessage()], 401);
        }
        

        
        $response = $next($request);
        
        if (isset($new_token)) {
            \Log::info("RefreshToken.php set_new_token to header ".$new_token);
            $response->headers->set('new_token', $new_token);
        } else {
            \Log::info("RefreshToken.php not set new_token to header ");
        }

        return $response;
     

    }
}
