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
            if (!$this->auth->parseToken()->authenticate()) { // Check user not found. Check token has expired.
                return response()->json(['Token invalid'], 401);
            }
            return $next($request); // Token is valid. User logged. Response without any token.
        } catch (TokenExpiredException $t) { // Token expired. User not logged.
            $payload = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
            $key = 'block_refresh_token_for_user_' . $payload['sub'];
            $cachedBefore = (int) \Cache::has($key);
            //printf("%s  %s %s ",$key, $cachedBefore, \Cache::get($key));
           
            if ($cachedBefore) { // If a token alredy was refreshed and sent to the client in the last JWT_BLACKLIST_GRACE_PERIOD seconds.
                \Auth::onceUsingId($payload['sub']); // Log the user using id.
                return $next($request); // Token expired. Response without any token because in grace period.
            }

            try {
                $newtoken = $this->auth->refresh(); // Get new token.
                $this->auth->authenticate();
                $gracePeriod = $this->auth->manager()->getBlacklist()->getGracePeriod();
                $expiresAt = Carbon::now()->addSeconds($gracePeriod);
                \Cache::put($key, $newtoken, $expiresAt);
            //   printf("%s %s %s %s", $newtoken, $gracePeriod, $expiresAt,  \Cache::has($key), \Auth::user()->id);
            } catch (JWTException $e) {
                return response()->json([$e->getMessage()], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['Token invalid'], 401);
        }
        
        $response = $next($request); // Token refreshed and continue.

        return $this->setAuthenticationHeader($response, $newtoken); // Response with new token on header Authorization.

        
      
    }
}
