<?php

namespace Larapix\Functions;

use Auth;
use Route;
use Request;
use Illuminate\Support\Str;
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
        // $getMenu     = Routes::select(['id'])->where('alias_route', self::routeIsExists() )->count();
        $getMenu    = self::routeIsExists();
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
        // $getMenu   = Routes::where('alias_route','=', $endRoute )->first();
        $count     = count($getRoute);

        echo '';
        for($i=0; $i<$count; $i++){
            echo ' - '.ucfirst($getRoute[$i]);
        }
    }

    public static function breadcrumb( $tag = 'ul', $titleBreadcrumb = null ){
        $route     = str_replace('-', ' ', self::getURI());
        $getRoute  = explode('/', $route);
        if(in_array('{id}', $getRoute)):
            $arr        = array_pop($getRoute);
            $endRoute   = ( empty($titleBreadcrumb) ) ? end($getRoute) : $titleBreadcrumb ;
        else:
            $endRoute   = ( empty($titleBreadcrumb) ) ? end($getRoute) : $titleBreadcrumb ;
        endif;
        // $getMenu   = Routes::where('alias_route', $endRoute )->first();
        $count     = count($getRoute);
        $breadcrumb = '<'.$tag.' class="breadcrumb">';
        for($i=0; $i<$count; $i++){
            $active     = ( $i == ($count-1) ) ? 'class="active"' : '';
            $iconHome   = ( $i == 0 ) ? '<i class="icon-home2 position-left"></i>' : '';
            $named      = ( $i == 0 ) ? 'Beranda' : $getRoute[$i];
            $named      = ( $i == ($count-1) ) ? $endRoute : $named;
            $breadcrumb .= '<li '.$active.'>'.$iconHome.''.ucfirst($named).'</li>';
        }
        $breadcrumb .= '</'.$tag.'>';
        return $breadcrumb;
    }

    public static function createController($dirController, $nameController, $methodPost = true){
        date_default_timezone_set("Asia/Jakarta");
        $today          = date("Y/m/d H:i:s");
        $dirController  = Str::studly($dirController);
        $dirView        = str_replace( ['\\', '/'], '.', $dirController);
        $nameController = Str::camel( $nameController );
        $pathController = app_path('Http/Controllers/BackEnd/'.$dirController);

        if(!file_exists($pathController)) {
            mkdir($pathController, 0755, true);
        }
        $functionPost   = 'public function post(){
        /* INSERT YOUR POST METHOD HERE */

        }';
        if($methodPost){
            $methodPost = $functionPost;
        }elseif($methodPost === FALSE){
            $methodPost = '';
        }else{
            $methodPost = 'public function '.$methodPost.'(){
            /* INSERT YOUR POST METHOD HERE */

            }';
        }
        $createFile     = $pathController.'/'.$nameController.'Controller.php';
        $namespace      = str_replace('/','\\', $dirController);
        $content        = '<?php
namespace Controllers\BackEnd\\'.$namespace.';
/*
| Name Controller    : '.$nameController.'Controller
| Controller Created : '.$today.'
|
*/
use Illuminate\Http\Request;

class '.$nameController.'Controller
{
    public function index(){
        return view(\'BackEnd.requires.'.$dirView.'.'.$nameController.'\');
    }

    '.$methodPost.'
    /* Please DON\'T DELETE THIS COMMENT */
    /* INSERT HERE */
}';
        if(file_exists($createFile)){
            return response()->json([
                'error' => true,
                'message' => 'Sorry, your Controller already exists.',
                'path' => $pathController,
                'file' => $createFile
            ], 500);
        } else {
            $fp   = fopen($createFile,"wb");
            if (fwrite($fp, $content) === FALSE) {
                return response()->json([
                    'error' => true,
                    'message' => 'Error, Cannot write to file',
                    'path' => $pathController,
                    'file' => $createFile
                ], 500);
            }
            return response()->json([
                'error' => false,
                'message' => 'Success create Controller',
                'path' => $pathController,
                'file' => $createFile
            ]);
            fclose($fp);
        }
    }

    public static function createBlade($dirView, $nameView){
        date_default_timezone_set("Asia/Jakarta");
        $today  = date("Y/m/d H:i:s");
        $pathView   = resource_path().'/views/BackEnd/requires/'.Str::studly($dirView);
        $nameView   = Str::camel( Str::studly($nameView) );

        if(!file_exists($pathView)) {
            mkdir($pathView, 0755, true);
        }

        $createFile = $pathView.'/'.$nameView.'.blade.php';
        $content    = '{{-- This Blade has been automatically generated for use by Web Dev , DATE CREATED : '.$today.' --}}
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
            return response()->json([
                'error' => true,
                'message' => 'Sorry, your Blade already exists',
                'path' => $pathView,
                'file' => $createFile
            ], 500);
        } else {
            $fp   = fopen($createFile,"wb");
            if (fwrite($fp,str_replace("  ",'',$content)) === FALSE) {
                return response()->json([
                    'error' => ture,
                    'message' => 'Cannot write to file',
                    'path' => $pathView,
                    'file' => $createFile
                ],500);
            }
            return response()->json([
                'error' => false,
                'message' => 'Sorry, your Blade already exists',
                'path' => $pathView,
                'file' => $createFile
            ]);
            fclose($fp);
        }
    }

    public static function getController($foo = NULL){
        $val    = (!$foo) ? self::currentRoute() : $foo ;
        $routes = Routes::where('alias_route','=', $val)->first();
        if($routes){
            if($routes->parent_id == 0){
                $getController = $routes->nm_route.'/index'.self::remSpace($routes->nm_route).'Controller';
            }else{
                $parent        = Routes::where('id','=',$routes->parent_id)->first();
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
