<?php

namespace Mhpix\Functions;

use Route;
use Request;
use Auth;
use DB;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\File;

use Mhpix\App\Model\t_Routes;
use Mhpix\App\Model\t_Routes_Role;
use Mhpix\Functions\MhpixHelper;

class Mhpix
{
    use MhpixHelper;
    use MhpixDataTables;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function Nav()
    {
        $Prefix     = Request::route()->getPrefix().'/';
        $User_Role  = Auth::user()->role_id;
        $role       = t_Routes_Role::where('role_id',$User_Role)->first();
        $role_id    = explode(',',$role->routes_id);
        $menu       = t_Routes::with('children')
                      ->where('id_parent',0)
                      ->where('type',0)
                      ->where('tampil',1)
                      ->whereIn('id', $role_id )
                      ->get();

        $nav  = NULL;
        $nav .= '<li style="padding:15px 0px 0"><i data-toggle="tooltip" data-placement="right" data-original-title="Developer" class="icon-cog2"></i><span style="padding-left:10px;" data-i18n="nav.category.support">Developer</span>';
        foreach($menu as $Routes){
            $urlMenu  = $Prefix.''.$Routes->alias_route;
            $Active   = ( Mhpix::Routes() == $Routes->alias_route ) ? 'class="active"' : '';
            if($Routes->children->count() > 0):
                $nav .= "\r\t\t\t".'<li class="nav-item">
                <a href="'.url($urlMenu).'"><i class="'.$Routes->icon.'"></i>
                <span>'.$Routes->nm_route.'</span></a>
                <div id="subPages" class="collapse ">
                <ul class="nav">';
                $nav .='<li><a href="'.url($urlMenu).'" '.$Active.'>'.$Routes->nm_route.'</a></li>';
                foreach($Routes->children as $subRoutes){
                    if(in_array($subRoutes->id,$role_id) && ($subRoutes->tampil) == 1){        // Check jika sub menu tidak terdaftar di Role ID
                        $urlMenuParent = $Prefix.$Routes->alias_route.'/'.$subRoutes->alias_route;
                        $subActive     = ( Mhpix::Routes() == $subRoutes->alias_route ) ? 'class="active"' : '';
                        $nav .='<li><a href="'.url($urlMenuParent).'" '.$subActive.'><i class="'.$subRoutes->icon.'"></i>'.$subRoutes->nm_route.'</a></li>';
                    }
                }
                $nav .='</ul></div></li>';
            else :
                $nav .= "\r\t\t\t".'<li>
                <a '.$Active.' href="'.url($urlMenu).'">
                <i class="'.$Routes->icon.'"></i>
                <span>'.$Routes->nm_route.'</span>
                </a>
                </li>';
            endif;
          }
        //   $nav .= '
        //         <li class=" nav-item"><a href=""><i class="icon-document-text"></i><span data-i18n="nav.support_documentation.main" class="menu-title">Documentation</span></a>
        //         </li>';
        $html = str_replace("><", ">\r\n<", $nav);
        return ($nav);
    }

    public static function titleHeader(){
        $getRoute    = explode('/', self::getURI());
        $getMenu     = t_Routes::where('alias_route','=', self::Routes() )->first();
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
        $getMenu   = t_Routes::where('alias_route','=', $endRoute )->first();
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
        $getMenu   = t_Routes::where('alias_route','=', $endRoute )->first();
        $count     = count($getRoute);
        echo '<ol class="breadcrumb">';
        for($i=0; $i<$count; $i++){
            echo '<li class="breadcrumb-item">'.ucfirst($getRoute[$i]).'</li>';
        }
        echo '</ol>';
    }

    public static function createController($foo,$bar,$Boolean){
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
            $POST = $c_post;
        }elseif($Boolean === FALSE){
            $POST = '';
        }else{
            $POST     = 'public function '.$Boolean.'(){
            /* INSERT YOUR POST METHOD HERE */

            }';
        }
        $createFile     = $Folder.'/'.$file.'Controller.php';
        $nm_Controller  = str_replace('/','\\', $dir);
        $content        = '<?php
namespace Mhpix\App\Controllers\BackEnd\\'.$nm_Controller.';
/*
| Name Controller    : '.$file.'Controller
| Controller Created : '.$tggl.'
|
*/
use Illuminate\Http\Request;

class '.$file.'Controller
{
    public function index(){
        return view(\'BackEnd.requires.'.$dir2.'.'.$file.'\');
    }
      '.$POST.'
    /* Please DON\'T DELETE THIS COMMENT */
    /* INSERT HERE */
}';
        if(file_exists($createFile)){
            return 'Sorry, your Controller already exists.';
        } else {
            $fp   = fopen($createFile,"wb");
            if (fwrite($fp, $content) === FALSE) {
                return "Cannot write to file ($filename)";
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
        {{-- Insert your javascript Here, start with <script> --}}
        @endpush
        ';
        if(file_exists($createFile)){
            return 'Sorry, your Blade already exists.';
        } else {
            $fp   = fopen($createFile,"wb");
            if (fwrite($fp,str_replace("  ",'',$content)) === FALSE) {
                return "Cannot write to file ($filename)";
                exit;
            }
            return true;
            fclose($fp);
        }
    }

    public static function getController($foo = NULL){
        $val    = (!$foo) ? self::currentRoute() : $foo ;
        $routes = t_Routes::where('alias_route','=', $val)->first();
        if($routes){
            if($routes->id_parent == 0){
                $getController = $routes->nm_route.'/index'.self::remSpace($routes->nm_route).'Controller';
            }else{
                $parent        = t_Routes::where('id','=',$routes->id_parent)->first();
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
        $str = 'C:\xampp\htdocs\lapp/vendor/Mhpix\App\Controllers\BackEnd\\';
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

  public static function Mailer($to,$subject,$content){

    	try {

            $mail = new PHPMailer(true); //New instance, with exceptions enabled
        	$mail->CharSet = 'UTF-8';

        	$mail->IsSMTP();                           // tell the class to use SMTP
        	$mail->SMTPAuth   = true;                  // enable SMTP authentication
        	$mail->Port       = 465;                   // set the SMTP server port
        	$mail->Host       = "mail2.gravindo.id";   // SMTP server
        	$mail->Username   = "no-reply@gravindo.id";// SMTP server username
        	$mail->Password   = "no123456";            // SMTP server password

        	//$mail->IsSendmail();  // tell the class to use Sendmail
        	// $mail->SMTPDebug = 2;
        	$mail->SMTPSecure = 'ssl';
        	$mail->AddReplyTo("no-reply@gravindo.id");

        	$mail->From       = "no-reply@gravindo.id";
        	$mail->FromName   = "GRAVINDO";

        	$mail->AddAddress($to);
        	$mail->Subject  = $subject;

        	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        	$mail->WordWrap   = 80; // set word wrap

        	$mail->MsgHTML($content);

        	$mail->IsHTML(true); // send as HTML
        	$mail->Send();
            return true;
        }catch (phpmailerException $e){
            dd($e);
    		return $e->errorMessage();
    	}catch(Exception $e){
         	dd($e);
            return $e->errorMessage();
         }

    }


}
