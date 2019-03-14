<?php
namespace Mhpix\App\Middleware;

use Closure;
use Route;
use Mhpix;
class webMiddleware
{
    public function handle($request, Closure $next)
    {
        // $local = array('127.0.0.1','::1');
        // if(in_array($_SERVER['REMOTE_ADDR'], local)):
        //     return $next($request);
        // else:
            if(Mhpix::isOurServer() == TRUE):
                return $next($request);
            else:
                // return redirect()->route('roj.preregister');
                abort(404);
                // return $next($request);
            endif;
        // endif;
    }
}
