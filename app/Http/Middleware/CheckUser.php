<?php

namespace App\Http\Middleware;

use App\Http\Traits\UserTrait;
use Closure;
use Illuminate\Http\Request;

class CheckUser
{
    use UserTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next, $role)
    {
        $roles = explode(';', $role);
        if ($roles[0] == '*') {
            if (in_array(auth()->user()->role, $roles)) {
                abort(403);
            } else {
                return $next($request);
            }
        }
        if (in_array(auth()->user()->role, $roles)) {
            if ($this->canUserGoRoute(auth()->id(), auth()->user()->role, $request->route()->getName())) {
                return $next($request);
            }
        }
        abort(403);
    }
}
