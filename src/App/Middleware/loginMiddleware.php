<?php
namespace LYTO\App\Middleware;

use Closure;
use Route;
use Lyto;
use DB;
class loginMiddleware
{
    public function handle($request, Closure $next)
    {
        if(session()->has('username')){
            return $next($request);
        }
        else{
            return redirect()->route('roj.member.login')->with('error', 'You don\'t have Permission to Access');
        }
    }
}
