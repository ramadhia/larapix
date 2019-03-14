<?php
namespace Mhpix\App\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Mhpix\App\Model\t_Routes;
use Mhpix\App\Model\t_Role_Bread;
use Mhpix\App\Model\t_Routes_Role;
use Mhpix;
use DB;
use Route;

class roleMiddleware
{
    public function handle($request, Closure $next)
    {
        // $response = $next($request);
        if(Auth::check()){
            $id       = Mhpix::currentRoute();
            $arr      = explode('/', Mhpix::getURI() );
            /* Check Parent Routes */
            $now_route  = ( count($arr) > 2 ) ? $arr[2] : $arr[1];
            $Parent     =  t_Routes::where('id_parent', $arr[1])->get();
            $Cur_Route  = Mhpix::Routes();
            $static_route = [
                'index.json'
            ];
            /* Check Parent Routes */
            $menu     = t_Routes::where('alias_route','=', ''.$Cur_Route.'')->first();
            $role_id  = Auth::user()->role_id;
            $role     = t_Routes_Role::where('role_id',$role_id)->first();
            $arr_Role = explode(',',$role->routes_id);

            if(in_array( $menu['id'], $arr_Role )){
                $role_bread  = t_Role_Bread::where('role_id',$role_id)->where('routes_id',$menu['id'])->first();
                    if (strpos( Route::currentRouteName() , '.') == TRUE) {
                    $a = explode('.', Route::currentRouteName() );
                    $b = $a[1];
                }else{
                    $b = 'index';
                }
                switch($b){
                    case 'index':$bread=1;break;
                    case 'read':$bread=2;break;
                    case 'edit':$bread=3;break;
                    case 'add':$bread=4;break;
                    case 'destroy':$bread=5;break;
                    default:$bread=1;break;
                }
                $arr_bread    = explode(',',$role_bread['role_bread']);
                if(in_array($bread, $arr_bread)){
                //session(['succ_log' => $bread.' '.Route::currentRouteName()]);
                    // return $response;
                    return $next($request);
                }else{
                    return redirect()->route('admin-index')->with('error_role', 'You don\'t have Permission to '.ucfirst($b).'' );
                }
            }elseif( in_array($now_route, $static_route) ){
                // return $response;
                return $next($request);
            }else{
                return redirect()->route('admin-index')->with('error_role', 'You don\'t have Permission to Access' );
            }
        }else{
            return redirect()->route('login')->with('error_role', 'You don\'t have Permission to Access');
        }
    }
}
