<?php

namespace App\Http\Middleware;

use Closure;


use \Tymon\JWTAuth\Facades\JWTAuth;
use \Tymon\JWTAuth\Exceptions\JWTException;
use \Tymon\JWTAuth\Exceptions\UnauthorizedHttpException;
use \Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use \Tymon\JWTAuth\Exceptions\TokenExpiredException;
use \Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Carbon\Carbon;

class GetUserFromToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->checkForToken($request); 
        
        try {
            if (!$this->auth->parseToken()->authenticate()) { 
                return response()->json(['error' => 'token_invalid'], 401);
            }
            return $next($request); 
        } catch (TokenExpiredException $t) { 
            $payload = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
            $key = 'block_refresh_token_for_user_' . $payload['sub'];
            $cachedBefore = (int) \Cache::has($key);
            //printf("%s  %s %s ",$key, $cachedBefore, \Cache::get($key));
            if ($cachedBefore) { 
                \Auth::onceUsingId($payload['sub']); 
                return $next($request); 
            }

            try {
                $newtoken = $this->auth->refresh(); 
                $this->auth->authenticate();
                $gracePeriod = $this->auth->manager()->getBlacklist()->getGracePeriod();
                $expiresAt = Carbon::now()->addSeconds($gracePeriod);
                \Cache::put($key, $newtoken, $expiresAt);
            //   printf("%s %s %s %s", $newtoken, $gracePeriod, $expiresAt,  \Cache::has($key), \Auth::user()->id);
            } catch (JWTException $e) {
                return response()->json(['error' => 'token_expired'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_invalid'], 401);
        }
        
        $response = $next($request); 

        return $this->setAuthenticationHeader($response, $newtoken); 
    }

    /**
     * Set the authentication header.
     *
     * @param  \Illuminate\Http\Response|\Illuminate\Http\JsonResponse  $response
     * @param  string|null  $token
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function setAuthenticationHeader($response, $token = null)
    {
        if ($token) {
            $response->headers->set('Access-Control-Expose-Headers', 'Authorization');
            $response->headers->set('Authorization', $token);
        }

        return $response;
    }
}
