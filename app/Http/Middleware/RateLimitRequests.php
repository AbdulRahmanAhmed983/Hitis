<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitRequests
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // return $next($request);
        if ($request->route()->methods[0] != 'GET') {
            $executed = RateLimiter::attempt($request->route()->getName() . 'limit' . auth()->id(), 1,
                function () {
                }, 8);
            if ($executed) {
                return $next($request);
            } else {
                return redirect()->back()->with('rate-limit',
                    'لقد تم تنفيذ طلبك هذه الصفحة مقفلة يمكنك المحاولة مرة أخرى خلال ' .
                    RateLimiter::availableIn($request->route()->getName() . 'limit' . auth()->id()) . ' ثوان')
                    ->withInput();
            }
        } else {
            return $next($request);
        }
    }
}
