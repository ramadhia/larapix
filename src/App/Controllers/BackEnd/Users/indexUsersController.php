<?php
namespace Mhpix\App\Controllers\BackEnd\Users;


/*
| Name Controller    : indexUsersController
| Controller Created : 2017/11/14 16:29:27
|
*/
use Auth;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Admin;
use Mhpix\App\Model\t_Customer;

class indexUsersController
{
      public function index(){
        
        if( Auth::user()->role_id == 99):
            $t_Users = t_Admin::all();
        else:
            $t_Users = t_Admin::where('role_id', 100)->get();
        endif;
        return view('BackEnd.requires.Users.indexUsers',
            ['t_Users' => $t_Users ]
        );

      }
       public function destroy(Request $req){
        if($req->id){
          if( Auth::user()->id == $req->id ){
            $result = array('err' => 'Something Wrong !! Code: 902  ');  // Jika Parameter ID tidak ditemukan
          }else{
            if( t_Admin::where('id',$req->id)->delete() ){
                $t_Pelanggan    = t_Customer::where('user_id', $req->id)->delete();
                $result = array('succ' => 'Success');
                //return redirect()->route('routes')->with('succ','Success !!');
            }else{
                $result = array('err' => 'Something Wrong !! Code: 900');  // Jika gagal menghapus
            }
          }
        }else{
            //return redirect()->route('routes')->with('err','Something Wrong !! Code: 103');
            $result = array('err' => 'Something Wrong !! Code: 901  ');  // Jika Parameter ID tidak ditemukan
        }
        return json_encode($result);
    }
}
