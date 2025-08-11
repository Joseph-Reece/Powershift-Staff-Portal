<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HOD
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
        if(session('authUser')['HOD'] != null){
            return $next($request);
        }else{
            return redirect('/dashboard')->with('error','You do not have permission to access this resource');
        }
    }
}
