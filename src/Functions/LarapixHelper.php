<?php
namespace Larapix\Functions;

use Request;

use Models\Routes;
use Symfony\Component\Routing\Route;

trait LarapixHelper{

    public static function route($route){
        return route($route);
    }

    public static function path(){
        return base_path().'/vendor/mhpix/larapix/src';
    }

    public static function getURI(){
        return Request::route()->uri();
    }

    public static function routeIsExists(){
        $arr		= explode('/',self::getURI());
        $URI		= '';
        if ( isset($arr[1]) ) :
            $ROOT	= $arr[1];
            $route	= Routes::with('children')
                    ->select(['id'])
                    ->where('alias_route', $ROOT)->first();
            $countChildren  = $route->children->count();
            if ( $countChildren >= 1):
                if ( isset($arr[2]) ):
                    $PARENT = $arr[2];
                    $URI    = ( $route->children->count() > 0 ) ? $PARENT : $ROOT;
                else:
                    $URI = $ROOT;
                endif;
            else:
                $URI = $ROOT;
            endif;
        endif;
        return $URI;
    }

    public static function currentRoute(){
        $route =  explode('/',self::getURI());
        return end($route);
    }

    public static function Routes2(){
        $r = session(['username' => Auth::user()->role_id]);
        return $r;
    }

    public static function remSpace($val){
        $str = preg_replace('/\s+/', '', $val);
        $str = str_replace('-', '', $str);
        return $str;
    }

    public static function set($a, $b, $c, $d) {
        if (strcasecmp($a, $b)==0):
            return $c;
        else :
            return $d;
        endif;
    }

    public static function isOurServer(){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $clientIpAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $clientIpAddress = $_SERVER['REMOTE_ADDR'];
        }
        $ourIP	= array('202.43.161.114','202.93.26.134', '36.88.58.123','::1','192.168.2.81','127.0.0.1','192.168.2.89');
        if (in_array($clientIpAddress, $ourIP)) :
            return TRUE;
        else:
            return true;
        endif;
    }

    public static function fileManager($field_id){
        $akey	= '2444282ef4344e3dacdedc7a78f8877d';
        return url('plugin/filemanager/dialog.php?type=1&amp;akey='.$akey.'&amp;field_id='.$field_id);
    }
}
