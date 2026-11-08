<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Restrict a route to users with the requested role. */
class RoleMiddleware
{
    /** Abort before the controller when the signed-in user lacks the required role. */
    public function handle(Request $request, Closure $next, string $role)
    {
        abort_unless($request->user() && $request->user()->role === $role, 403, 'Not authorized.');

        return $next($request);
    }
}
