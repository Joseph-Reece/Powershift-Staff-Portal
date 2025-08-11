<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Farmer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(session('authUser') != null && session('authUser')['userCategory'] == 'farmer'){
            return $next($request);
        }else{
            return redirect('/login')->with('error','You do not have permission to access this resource');
        }
    }
}
