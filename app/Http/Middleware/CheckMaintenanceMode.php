<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;
class CheckMaintenanceMode
{

    public function handle(Request $request, Closure $next)
    {
        $maintenance_mood = DB::table('data')->where('data_key', 'maintenance_mood')->value('value');
        if($maintenance_mood){
                abort(503);
        }
        else{
            return $next($request);
        }
    }
}
