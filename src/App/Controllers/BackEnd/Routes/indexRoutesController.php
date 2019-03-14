<?php

namespace Mhpix\App\Controllers\BackEnd\Routes;

use DB;
use Illuminate\Http\Request;
use Mhpix\App\Model\t_Routes;

class indexRoutesController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req){
        if(
            $req->state == 'order' &&  
            $req->order != '' &&
            $req->id != '' 
        ):
            $Routes = t_Routes::where('id', $req->id )->first();
            $Routes->sort = $req->order;
            if ( $Routes->save() ):
            return response()->json(['result' => 'success']);
            else:
            return response()->json(['result' => 'error'], 400);
            endif;
        endif;
        $t_Routes = t_Routes::with('children')
                    ->orderBy('sort','ASC')
                    //->where('id_parent','=',0)
                    ->get();
        return view('BackEnd.requires.Routes.indexRoutes')->with('t_Routes', $t_Routes);
    }

    public function destroy(Request $req){
        if($req->id){
            if( t_Routes::where('lock',0)->where('id',$req->id)->delete() ){
                //Start Remove routes_id in Role
                $Menu_role  = DB::table('t_routes_role')->whereRaw($req->id, 'routes_id')->get();
                foreach($Menu_role AS $role){
                    $Arr_menu   = explode(',', $role->routes_id);
                    $rm_array   = array_diff($Arr_menu,array($req->id));
                    $val        = implode(',', $rm_array);
                    DB::table('t_routes_role')->where('role_id', $role->role_id)->update([ 'routes_id' => $val]);
                }
                $result = array('succ' => 'Success');
                //return redirect()->route('routes')->with('succ','Success !!');
            }else{
                //return redirect()->route('routes')->with('err','Something Wrong !! Code: 102');
                $result = array('err' => 'Something Wrong !! Code: 900');  // Jika gagal menghapus
            }
        }else{
            //return redirect()->route('routes')->with('err','Something Wrong !! Code: 103');
            $result = array('err' => 'Something Wrong !! Code: 901  ');  // Jika Parameter ID tidak ditemukan
        }
        return json_encode($result);
    }
}
