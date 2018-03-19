<?php

namespace App\Http\Middleware;

use Closure;

class NeedActive
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()->is_verified) {
            return response()->json(['need_active'], 403);
        }

        return $next($request);

    }
}
