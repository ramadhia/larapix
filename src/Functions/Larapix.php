<?php

namespace Larapix\Functions;

use Auth;
use Route;
use Request;
use Illuminate\Support\Facades\File;

use Models\Routes;
use Models\RouteRole;

class Larapix
{
    use LarapixHelper;
    use MenuNavigation;
    use LarapixRoute;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function titleHeader(){
        $getRoute    = explode('/', self::getURI());
        $getMenu     = Routes::where('alias_route','=', self::Routes() )->first();
        if(in_array('{id}', $getRoute)):
            $arr      = array_pop($getRoute);
            $endRoute = end($getRoute);
        else:
            $endRoute  = end($getRoute);
        endif;
        $titleHeader = ($getMenu) ? str_replace('-',' ', $endRoute ) : 'Dashboard';
        return ucwords($titleHeader);
    }

    public static function title(){
        $route     = str_replace('-', ' ', self::getURI());
        $getRoute  = explode('/', $route);
        if(in_array('{id}', $getRoute)):
            $arr      = array_pop($getRoute);
            $endRoute = end($getRoute);
        else:
            $endRoute  = end($getRoute);
        endif;
        $getMenu   = Routes::where('alias_route','=', $endRoute )->first();
        $count     = count($getRoute);

        echo '';
        for($i=0; $i<$count; $i++){
            echo ' - '.ucfirst($getRoute[$i]);
        }
    }

    public static function breadcrumb(){
        $route     = str_replace('-', ' ', self::getURI());
        $getRoute  = explode('/', $route);
        if(in_array('{id}', $getRoute)):
            $arr        = array_pop($getRoute);
            $endRoute   = end($getRoute);
        else:
            $endRoute   = end($getRoute);
        endif;
        $getMenu   = Routes::where('alias_route','=', $endRoute )->first();
        $count     = count($getRoute);
        $breadcrumb = '<ol class="breadcrumb">';
        for($i=0; $i<$count; $i++){
            $breadcrumb .= '<li class="breadcrumb-item">'.ucfirst($getRoute[$i]).'</li>';
        }
        $breadcrumb .= '</ol>';
        return $breadcrumb;
    }

    public static function createController($foo,$bar, $Boolean){
        date_default_timezone_set("Asia/Jakarta");
        $dir    = self::remSpace($foo);
        $dir2   = str_replace( ['\\', '/'], '.',self::remSpace($foo));
        $file   = self::remSpace($bar);
        $tggl   = date("Y/m/d H:i:s");
        $Folder = self::path().'/App/Controllers/BackEnd/'.$dir;

        if(!file_exists($Folder)) {
            mkdir($Folder, 0755, true);
        }
        $c_post     = 'public function post(){
        /* INSERT YOUR POST METHOD HERE */

        }';
        if($Boolean === TRUE){
            $methodPost = $c_post;
        }elseif($Boolean === FALSE){
            $methodPost = '';
        }else{
            $methodPost = 'public function '.$Boolean.'(){
            /* INSERT YOUR POST METHOD HERE */

            }';
        }
        $createFile     = $Folder.'/'.$file.'Controller.php';
        $nm_Controller  = str_replace('/','\\', $dir);
        $content        = '';
        if(file_exists($createFile)){
            return 'Sorry, your Controller already exists.';
        } else {
            $fp   = fopen($createFile,"wb");
            if (fwrite($fp, $content) === FALSE) {
                return "Cannot write to file ($createFile)";
                exit;
            }
            return TRUE;
            fclose($fp);
        }
    }

    public static function createBlade($dir,$file){
        date_default_timezone_set("Asia/Jakarta");
        $tggl = date("Y/m/d H:i:s");
        $Folder = resource_path().'/views/BackEnd/requires/'.self::remSpace($dir);

        if(!file_exists($Folder)) {
            mkdir($Folder, 0755, true);
        }

        $createFile = $Folder.'/'.self::remSpace($file).'.blade.php';
        $content = '{{-- This Blade has been automatically generated for use by Web Dev , DATE CREATED : '.$tggl.' --}}
        @extends(\'BackEnd.layouts.body\')
        @push(\'styles\')
        {{-- Insert your Stylesheet Here --}}
        @endpush

        @section(\'content\')
        {{-- Insert your Content Here --}}
        @endsection

        @push(\'scripts\')
        {{-- Insert your logic Javascript --}}
        @endpush
        ';
        if(file_exists($createFile)){
            return 'Sorry, your Blade already exists.';
        } else {
            $fp   = fopen($createFile,"wb");
            if (fwrite($fp,str_replace("  ",'',$content)) === FALSE) {

                return "Cannot write to file ($createFile)";
            }
            return true;
            fclose($fp);
        }
    }

    public static function getController($foo = NULL){
        $val    = (!$foo) ? self::currentRoute() : $foo ;
        $routes = Routes::where('alias_route','=', $val)->first();
        if($routes){
            if($routes->id_parent == 0){
                $getController = $routes->nm_route.'/index'.self::remSpace($routes->nm_route).'Controller';
            }else{
                $parent        = Routes::where('id','=',$routes->id_parent)->first();
                $getController = $parent->nm_route.'/'.self::remSpace($routes->nm_route).'Controller';
            }
        } else {
            $getController = 'Something Wrong !! Code: 11'; // Gagal mendapatkan Controller
        }
        return $getController;
    }

    public static function listDIR($dir){
        $ffs = File::allFiles(self::path().'\App\Controllers\BackEnd');
        $sel = '<ol>';
        $str = 'C:\xampp\htdocs\lapp/vendor/Controllers\BackEnd\\';
        foreach($ffs as $ff){
            $file   = str_replace($str,'',$ff);
            $file   = str_replace('.php','',$file);
            $sel .= '<li>'.$file;
            $sel .= '</li>';
        }
        $sel .= '</ol>';
        $file = self::path().'\App\Controllers\BackEnd\\'.self::getController().'.php';
        if(File::exists($file)){
            $check = self::getController().'';
        }else{
            $check = self::getController().' Not Found';
        }
        return $check;
    }

}
