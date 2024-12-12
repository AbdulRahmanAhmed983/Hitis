<?php

namespace App\Http\Middleware;

use App\Http\Traits\UserTrait;
use Closure;
use Illuminate\Http\Request;

class SingleSession
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
        if (auth()->user()->last_session != session()->getId()) {
            return $this->logout($request, ['هناك تسجيل الدخول من جهاز آخر الآن',
                'لذلك إذا لم يكن هذا أنت فعليك تغير كلمة المرور',
                'أو عليك إستخدام جهاز واحد فقط و متصفح واحد فقط']);
        }
        return $next($request);
    }
}
