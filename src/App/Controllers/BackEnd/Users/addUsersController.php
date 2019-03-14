<?php
namespace Mhpix\App\Controllers\BackEnd\Users;
/*
| Name Controller    : addUsersController
| Controller Created : 2017/11/14 16:39:48
|
*/
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Mhpix\App\Model\t_Admin;

class addUsersController
{
      public function index(){

          return view('BackEnd.requires.Users.addUsers');

      }
      public function post(Request $req){
        $rules = array(
         'name'       => 'required',
         'username'   => 'required',
         'email'      => 'required|email',
         'password'   => 'required|min:5',
         'repassword' => 'required|min:5|same:password'
        );
        $validator = Validator::make($req->all(), $rules);
        // check if the validator failed -----------------------
        if ($validator->fails()) {
         // get the error messages from the validator
         $messages = $validator->messages();
         // redirect our user back to the form with the errors from the validator
         return redirect()->route('users.add')->withErrors($validator);
       }else{
          $active   = ($req->active == 'on' ) ? '1':'0';
          $t_Users  = new t_Admin;
          $t_Users->name      = $req->name;
          $t_Users->username  = $req->username;
          $t_Users->email     = $req->email;
          $t_Users->password  = Hash::make($req->password);
          $t_Users->role_id   = $req->role;
          $t_Users->active    = $active;
          
          if($t_Users->save()){
            return redirect()->route('users')->with('succ',"Success add Users ".$req->name);
          }else{
            return redirect()->route('users')->with('err','Something Wrong !! Code: 121');  // Jika gagal menyimpan data baru
          }
          /* INSERT YOUR POST METHOD HERE */
        }
      }

      /* Please DON'T DELETE THIS COMMENT */
      /* INSERT HERE */
      }
