<?php
namespace Mhpix\App\Controllers\BackEnd\Roles;
/*
| Name Controller    : editRolesController
| Controller Created : 2017/11/14 10:23:10
|
*/

use Auth;
use Validator;

use Mhpix\App\Model\t_Routes;
use Mhpix\App\Model\t_Admin;
use Mhpix\App\Model\t_Routes_Role;
use Mhpix\App\Model\t_Role_Bread;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class editRolesController
{
      public function index($id){
          $t_Routes         = t_Routes::all();
          $t_Routes_Role    = t_Routes_Role::where('id',$id)->first();
          return view('BackEnd.requires.Roles.editRoles', [
              't_Routes' => $t_Routes,
              't_Routes_Role' => $t_Routes_Role
          ]);

      }
      public function post(Request $req){

        $rules = array(
            'name'   => 'required',
            'password'   => 'required',
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            // get the error messages from the validator
            $messages = $validator->messages();
            // redirect our user back to the form with the errors from the validator
            return redirect()->route('roles.edit',$req->id)->withErrors($validator);
        }else{
            $password   = $req->password;
            $t_Users    = t_Admin::where('username',Auth::user()->username)->first();
            if (Hash::check($password, $t_Users->password)) {
                /* ========= LOOPING FOR B.R.E.A.D ===============*/
                $route_array    = t_Routes::all();
                foreach($route_array as $row){
                    $alias      = $row->alias_route;
                    $browse     = (isset($req[$alias.'_browse'])) ? 1:0;
                    $read       = (isset($req[$alias.'_read'])) ? 2:0;
                    $edit       = (isset($req[$alias.'_edit'])) ? 3:0;
                    $add        = (isset($req[$alias.'_add'])) ? 4:0;
                    $delete     = (isset($req[$alias.'_delete'])) ? 5:0;
                    $summ_bread = array_sum([$browse,$read,$edit,$add,$delete]);
                    if ($summ_bread == 0) continue;
                    $r_bread[]  = [$row->id => $browse.','.$read.','.$edit.','.$add.','.$delete];
                }
                /* ========= LOOPING FOR B.R.E.A.D ===============*/

                /* ========= VARIABLE ============================*/
                $role_id        = $req->role_id;
                $username       = Auth::user()->username;
                $now            = Carbon::now('Asia/Jakarta');
                /* ========= VARIABLE ============================*/

                /* ========= BAKING B.R.E.A.D ===============*/
                foreach($r_bread as $array){
                    foreach ($array as $routes_id => $bread) {
                        $check = t_Role_Bread::where('role_id',$role_id)->where('routes_id',$routes_id)->first();
                        if($check):
                            $t_Role_Bread   = t_Role_Bread::where('role_id',$role_id)->where('routes_id',$routes_id)->update([
                                'role_bread' => $bread,
                                'created_by' => $username,
                                'updated_at' => $now,

                            ]);
                        else:
                            $t_Role_Bread   = new t_Role_Bread;
                            $t_Role_Bread->role_id      = $role_id;
                            $t_Role_Bread->routes_id    = $routes_id;
                            $t_Role_Bread->role_bread   = $bread;
                            $t_Role_Bread->created_by   = $username;
                            $t_Role_Bread->created_at   = $now;
                            $t_Role_Bread->updated_at   = NULL;
                            $t_Role_Bread->save();
                        endif;
                        $arr_routes[]               = $routes_id;
                    }
                    // t_Routes_Role::where('role_id',$role_id)->whereNotIn('routes_id',[$arr_routes])->delete();
                }
                /* ========= BAKING B.R.E.A.D ===============*/

                $t_Routes_Role  = t_Routes_Role::find($role_id);
                $t_Routes_Role->name        = $req->name;
                $t_Routes_Role->routes_id   = implode(',',$arr_routes);
                $t_Routes_Role->modified_by = $username;
                $t_Routes_Role->updated_at  = $now;
                if($t_Routes_Role->save()):
                    t_Role_Bread::where('role_id',$role_id)->whereNotIn('routes_id',$arr_routes)->delete();
                    return redirect()->route('roles')->with('succ',"Success edit Roles <b>$req->name</b>");
                else:
                    return redirect()->route('roles.edit',$req->id)->withErrors('Password Error');
                endif;

            }else{
                return redirect()->route('roles.edit',$req->id)->withErrors('Password Error');
            }
        }
      }

      /* Please DON'T DELETE THIS COMMENT */
      /* INSERT HERE */
}
