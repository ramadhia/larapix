<?php
namespace LYTO\App\Middleware;

use Closure;
use Route;
use Lyto;
class guestMiddleware
{
    public function handle($request, Closure $next)
    {
        if(session()->has('username')){
          return redirect()->route('roj.member');
        }else{
          return $next($request);
        }
    }
}
