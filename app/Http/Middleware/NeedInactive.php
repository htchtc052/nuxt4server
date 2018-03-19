<?php

namespace App\Http\Middleware;

use Closure;

class NeedInactive
{
    public function handle($request, Closure $next)
    {
        if ($request->user()->is_verified) {
            return response()->json(['need_inactive'], 403);
        }

        return $next($request);

    }
}
