<?php

namespace Mhpix\App\Controllers\BackEnd\Routes;

use DB;
use Auth;
use Validator;
use Illuminate\Http\Request;
use Mhpix;
use Mhpix\App\Model\t_Routes;
use Mhpix\App\Model\t_Routes_Role;
use Mhpix\App\Model\t_Role_Bread;

class addRoutesController
{
    public function index(){
      $t_Routes = t_Routes::with('children')
                  ->where('id_parent','=',0)
                  ->orderBy('sort','ASC')
                  ->get();
      return view ('BackEnd.requires.Routes.addRoutes')->with('t_Routes', $t_Routes);
    }

    public function post(Request $req){

      $rules = array(
       'nm_route'   => 'required',   // just a normal required validation
       'alias_route'=> 'required',   // required and has to match the password field
       'role'       => 'required'   // required and has to match the password field
      );
      $msg = array(
       'nm_route.required'   => 'Check your names route',   // just a normal required validation
       'role.required'       => 'Make sure your role is right. Sorry, I forgot to set \'ROLE\' to be required :D'   // just a normal required validation
      );
      $validator = Validator::make($req->all(), $rules, $msg);
      // check if the validator failed -----------------------
      if ($validator->fails()) {
       // get the error messages from the validator
       $messages = $validator->messages();
       // redirect our user back to the form with the errors from the validator
       return redirect()->route('routes.add')->withErrors($validator);
      } else {
        $tampil               = ($req->publish == 'on') ? '1' : '0';
        $controller           = ($req->controller == 'on') ? '1': '0';
        $blade                = ($req->blade == 'on') ? '1': '0';
        $lock                 = ($req->lock == 'on') ? '1': '0';
        $bread_browse         = ($req->bread_browse == 'on') ? '1' : '0';
        $bread_read           = ($req->bread_read   == 'on') ? '2' : '0';
        $bread_add            = ($req->bread_add    == 'on') ? '3' : '0';
        $bread_edit           = ($req->bread_edit   == 'on') ? '4' : '0';
        $bread_delete         = ($req->bread_delete == 'on') ? '5' : '0';
        $bread                = $bread_browse.','.$bread_read.','.$bread_add.','.$bread_edit.','.$bread_delete;
        $B = $R = $E = $A = $D  = '';
        $msg_blade = $msg_Cont  = NULL;
        $t_Routes               = new t_Routes;
        $t_Routes->id           = t_Routes::max('id')+1;
        $t_Routes->id_parent    = $req->id_parent;
        $t_Routes->alias_route  = $req->alias_route;
        $t_Routes->nm_route     = ucfirst($req->nm_route);
        $t_Routes->shortcut     = NULL;
        $t_Routes->tampil       = $tampil;
        $t_Routes->sort         = t_Routes::max('sort')+1;
        $t_Routes->type         = $req->type;
        // $t_Routes->method       = $req->method;
        // $t_Routes->blade        = $blade;
        // $t_Routes->controller   = $controller;
        $t_Routes->icon         = $req->icon;
        $t_Routes->bread        = $bread;
        $t_Routes->created_by   = Auth::user()->username;
        $t_Routes->modified_by  = NULL;
        $t_Routes->lock         = $lock;

        //return json_encode($req->all());
        // Start Save
        if($t_Routes->save()){
          if($req->role){
            $find             = t_Routes::find($t_Routes->id_parent);
            $dir_Menu         = ( ($t_Routes->id_parent) == 0) ? Mhpix::remSpace($t_Routes->nm_route) : Mhpix::remSpace($find['nm_route']).DIRECTORY_SEPARATOR.Mhpix::remSpace($t_Routes->nm_route);
            $nm_controller    =  Mhpix::remSpace($t_Routes->nm_route);

            foreach($req->role as $role){
              $Menu_role    = t_Routes_role::where('role_id', $role)->first();
              $Arr_menu     = explode(',' , $Menu_role->routes_id);
              if(!in_array($t_Routes->id,$Arr_menu)){

                /* ======= ADD ROUTES IN ROLE =======================*/
                $Menu_id    = array($Menu_role->routes_id);
                array_push($Menu_id, $t_Routes->id );
                $val        = implode(',', $Menu_id);
                t_Routes_Role::where('role_id', $role)->update([ 'routes_id' => $val]);
                /* ======= ADD ROUTES IN ROLE =======================*/

                /* ======== ADD BREAD IN ROLE *=======================*/
                $Bread            = new t_Role_Bread;
                $Bread->id        = t_Role_Bread::max('id')+1;
                $Bread->role_id   = $role;
                $Bread->routes_id = $t_Routes->id;
                $Bread->role_bread= $bread;
                $Bread->save();
                /* ======== ADD BREAD IN ROLE *=======================*/

              }
            }
    /* ============ BREAD =========================*/
            /* ======== BREAD BROWSE AND DELETE *=======================*/
            if($req->bread_browse == 'on' && $req->bread_delete == 'on' ){
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;

              if (Mhpix::createController($dir_Menu, 'index'.$nm_controller, 'destroy') == TRUE){$B = 'B';$D = 'D'; } else { $B = 'B Error';  $D = 'D Error';}

              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( (Mhpix::createBlade($dir_Menu, 'index'.$nm_controller) ) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';

            }elseif($req->bread_browse == 'on'){
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;
              if (Mhpix::createController($dir_Menu, 'index'.$nm_controller, FALSE) == TRUE){ $B = 'B'; } else{ $B = 'B Error';}
              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( (Mhpix::createBlade($dir_Menu, 'index'.$nm_controller)) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';

            }
            /* ======== BREAD BROWSE AND DELETE *=======================*/

            /* ======== BREAD READ *=======================*/
            if($req->bread_read   == 'on'){
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;
              if (Mhpix::createController($dir_Menu, 'read'.$nm_controller, TRUE) == TRUE ) { $R = 'R'; } else {  $R = 'R Error'; }

              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( ( Mhpix::createBlade($dir_Menu, 'read'.$nm_controller) ) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';

            }
            /* ======== BREAD READ *=======================*/

              /* ======== BREAD ADD *=======================*/
            if($req->bread_add    == 'on'){
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;
              if (Mhpix::createController($dir_Menu, 'add'.$nm_controller, TRUE)  == TRUE){ $A = 'A'; } else { $A = 'A Error'; }

              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( (Mhpix::createBlade($dir_Menu, 'add'.$nm_controller)) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';

            }
            /* ======== BREAD ADD *=======================*/

            /* ======== BREAD EDIT *=======================*/
            if($req->bread_edit   == 'on'){
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;
              if (Mhpix::createController($dir_Menu, 'edit'.$nm_controller, TRUE) == TRUE ) { $E = 'E'; } else { $B = 'E Error'; }

              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( (Mhpix::createBlade($dir_Menu, 'edit'.$nm_controller)) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';

            }
              /* ======== EDIT BREAD *=======================*/
      /* ============ BREAD =========================*/

            $msg_Cont = '& Success Create Controller '.$B.','.$R.','.$E.','.$A.','.$D.' ';
            return redirect()->route('routes')
            ->with('succ',"Success add Routes ".$req->controller." '".$t_Routes->nm_route."' ".$msg_Cont.$msg_blade."");

          }else{
            return redirect()->route('add-routes')->with('err',"Something Wrong !! Code: 920"); // Jika Role tidak ada di menu add
          }
        }else{
          return redirect()->route('routes')->with('err','Something Wrong !! Code: 921');  // Jika gagal menyimpan data baru
        }
        // End Save
      }
    }

}
