<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Student;
class EnsureGoogleFormSubmitted
{
    

    public function handle(Request $request, Closure $next)
    {
         $code_std = DB::connection('mysql_SECOND')->table('wp_vxcf_leads_detail')
        ->where('name',1)->pluck('value');
        dd($code_std[0]);
         //$data = DB::connection('mysql_SECOND')->table('wp_vxcf_leads_detail')->get();

         return response()->json($data);
         
         
        $student = Student::where('username',auth()->id())->pluck('google_form_submitted')[0];
        if ($student) {
            return $next($request);
        }
        return redirect('https://docs.google.com/forms/d/e/1FAIpQLScgdy2YHk-K39f4fmNGn4uTed3a0Af4JdNXYijaibAFTf5WSQ/viewform?fbzx=-8036253183995492463');
        //return redirect('https://docs.google.com/forms/d/e/1FAIpQLSf6ARDKdrkS1ysvS13FH_enZZKC1PcLACCGSiXEulufFVLSDQ/viewform');
    }
}
