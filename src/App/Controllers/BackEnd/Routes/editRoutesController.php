<?php

namespace Mhpix\App\Controllers\BackEnd\Routes;

use Illuminate\Http\Request;
use Validator;
use Auth;
use Mhpix;
use Mhpix\App\Model\t_Routes;
use Mhpix\App\Model\t_Role_Bread;
use Mhpix\App\Model\t_Routes_Role;

class editRoutesController
{
    public function index($id){

      if(!$id){
        return redirect()->route('routes')->with('err','Something Wrong !!');
      }
      $t_Routes     = t_Routes::with('children')
                    ->where('id_parent',0)
                    ->orderBy('sort','ASC')
                    ->get();
      $editRoutes   = t_Routes::where('id',$id)->first();

      // $editroles    = t_Routes_Role::whereIn('routes_id', [25,1] )->get();
      $editroles    = t_Routes_Role::where('routes_id','like', '%'.$id.'%')->get();
      // return $editroles;
      return view ('BackEnd.requires.Routes.editRoutes',
                    [
                      'editRoutes' => $editRoutes,
                      'editroles' => $editroles
                    ]
                  )->with('t_Routes', $t_Routes);
      //return redirect()->action('\Mhpix\App\Controllers\BackEnd\indexController@index');
    }

    public function post(Request $req){
      $rules = array(
       'nm_route'   => 'required',   // just a normal required validation
       'icon'       => 'required',  // required and must be unique in the ducks table
       'alias_route' => 'required'   // required and has to match the password field
      );

      $msg = array(
       'nm_route.required'   => 'Check your names route',   // just a normal required validation
      );
      $validator = Validator::make($req->all(), $rules, $msg );
        // check if the validator failed -----------------------
      if ($validator->fails()) {
        // get the error messages from the validator
       $messages = $validator->messages();
        // redirect our user back to the form with the errors from the validator
       return redirect()->route('routes.edit',$req->id)->withErrors($validator);
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
        $t_Routes               = t_Routes::find($req->id);
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

        if($t_Routes->save()){

            $find             = t_Routes::find($t_Routes->id_parent);
            $dir_Menu         = ( ($t_Routes->id_parent) == 0) ? Mhpix::remSpace($t_Routes->nm_route)         : Mhpix::remSpace($find['nm_route']);
            $nm_controller    = Mhpix::remSpace($t_Routes->nm_route);
            //$file_controller  = ( ($t_Routes->id_parent) == 0) ? 'index'.Mhpix::remSpace($t_Routes->nm_route) : Mhpix::remSpace($t_Routes->nm_route);

    /* ============ BREAD =========================*/
            /* ======== BREAD BROWSE AND DELETE *=======================*/
            if($req->bread_browse == 'on' && $req->bread_delete == 'on' ){
              /* ============== Controller =============================*/
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;
              if (Mhpix::createController($dir_Menu, 'index'.$nm_controller, 'destroy') == TRUE){$B = 'B';$D = 'D'; } else { $B = 'B Error';  $D = 'D Error';}
              /* ============== Controller =============================*/

              /* ============== Blade =============================*/
              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( (Mhpix::createBlade($dir_Menu, 'index'.$nm_controller) ) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';
              /* ============== Blade =============================*/

            }elseif($req->bread_browse == 'on'){
              /* ============== Controller =============================*/
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;
              if (Mhpix::createController($dir_Menu, 'index'.$nm_controller, FALSE) == TRUE){ $B = 'B'; } else{ $B = 'B Error';}
              /* ============== Controller =============================*/

              /* ============== Blade =============================*/
              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( (Mhpix::createBlade($dir_Menu, 'index'.$nm_controller)) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';
              /* ============== Blade =============================*/
            }
            /* ======== BREAD BROWSE AND DELETE *=======================*/

            /* ======== BREAD READ *=======================*/
            if($req->bread_read   == 'on'){
              /* ============== Controller =============================*/
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;
              if (Mhpix::createController($dir_Menu, 'read'.$nm_controller, TRUE) == TRUE ) { $R = 'R'; } else {  $R = 'R Error'; }
              /* ============== Controller =============================*/

              /* ============== Blade =============================*/
              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( ( Mhpix::createBlade($dir_Menu, 'read'.$nm_controller) ) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';
              /* ============== Blade =============================*/
            }
            /* ======== BREAD READ *=======================*/

            /* ======== BREAD ADD *=======================*/
            if($req->bread_add    == 'on'){
              /* ============== Controller =============================*/
              $dir_Cont   = 'vendor/mhpix/App/Controllers/BackEnd/'.$dir_Menu;
              if (Mhpix::createController($dir_Menu, 'add'.$nm_controller, TRUE)  == TRUE){ $A = 'A'; } else { $A = 'A Error'; }
              /* ============== Controller =============================*/

              /* ============== Blade =============================*/
              $dir_blade  = 'resources/views/BackEnd/requires/'.$dir_Menu;
              $msg_blade  = ( (Mhpix::createBlade($dir_Menu, 'add'.$nm_controller)) == TRUE ) ? '& Success Create Blade <strong>'.$dir_blade.'</strong>' : '& Error Create Blade';
              /* ============== Blade =============================*/
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
          return redirect()->route('routes')->with('err','Something Wrong !! Code: 910');  // Jika gagal menyimpan data edit
        }

      }
    }
}
