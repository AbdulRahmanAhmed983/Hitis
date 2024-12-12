<?php

namespace App\Http\Middleware;

use App\Http\Traits\UserTrait;
use Closure;
use Illuminate\Http\Request;

class CheckUserStatus
{
    use UserTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->password_status == 1) {
            return $next($request);
        } else if (auth()->user()->password_status == 0) {
            return redirect()->route('change.password')->with('info', 'برجاء تغير كلمة السر للمتابعة');
        } else {
            return $this->logout($request, 'هذا الحساب معلق');
        }
    }
}
