<?php
namespace Mhpix\App\Controllers\BackEnd\Users;
/*
| Name Controller    : editUsersController
| Controller Created : 2017/11/14 16:37:44
|
*/

use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Mhpix\App\Model\t_Admin;

class editUsersController
{
      public function index($id){
          $editUsers   = t_Admin::where('id',$id)->first();
          return view('BackEnd.requires.Users.editUsers',['editUsers' => $editUsers]);

      }

      public function post(Request $req){
        /* ======== Validation =================*/
        $rules = array(
         'name'       => 'required',
         'username'   => 'required',
         'email'      => 'required|email'
        );
        
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
         $messages = $validator->messages();
         return redirect()->route('users.edit', $req->id)->withErrors($validator);
         /* ======== Validation =================*/
        }else{
          $active             = ($req->active == 'on' ) ? '1':'0';
          $t_Users            = t_Admin::find($req->id);
          $t_Users->name      = $req->name;
          $t_Users->username  = $req->username;
          $t_Users->email     = $req->email;
          $t_Users->role_id   = $req->role;
          $t_Users->active    = $active;

          /* ======== Validation Password =================*/
          if(strlen($req->password) >= 1):
              $rules_pass = array(
                'password'   => 'required|min:5',
                'repassword' => 'required|min:5|same:password'
              );
              $valid_pass = Validator::make($req->all(), $rules_pass);
              if ($valid_pass->fails()) :
                 $messages = $valid_pass->messages();
                 return redirect()->route('users.edit', $req->id)->withErrors($valid_pass);
              endif;
             $t_Users->password    = Hash::make($req->password);
          endif;
           /* ======== Validation Password =================*/

          if($t_Users->save()):
            return redirect()->route('users')->with('succ',"Success edit Users ".$req->name);
          else:
            return redirect()->route('users')->with('err','Something Wrong !! Code: 121');  // Jika gagal menyimpan data baru
          
          endif;
          //return json_encode($req->all());
        }
      }

      /* Please DON'T DELETE THIS COMMENT */
      /* INSERT HERE */
}
