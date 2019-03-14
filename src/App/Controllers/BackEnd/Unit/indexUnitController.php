<?php
namespace Mhpix\App\Controllers\BackEnd\Unit;
/*
| Name Controller    : indexUnitController
| Controller Created : 2018/11/17 23:20:36
|
*/
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Unit;

class indexUnitController
{
    public function index(){
        return view('BackEnd.requires.Unit.indexUnit');
    }

    public function destroy(Request $req){
        $t_Unit    = t_Unit::find($req->id);
        if($req->id){
            if( !$t_Unit ){
                return redirect()->route('unit')->with('err','Data Unit tidak ditemukan.');
            }else{
                if( $t_Unit->whereId($req->id)->delete() ){
                    return redirect()->route('unit')->with('succ','Success Delete');
                }else{
                    return redirect()->route('unit')->with('err','Something Wrong');
                }
            }
        }else{
            return redirect()->route('unit')->with('err','Parameter ID not Found');
        }
    }

    public function listUnit(Request $req){
        $t_Unit = t_Unit::where('status',0);
        if($req->id):
            $getUnit = $t_Unit->where('id',$req->id)->orderBy('created_at')->first();
        else:
            $getUnit = $t_Unit->orderBy('created_at')->get();
        endif;

        if( empty($getUnit) ):
            abort(404);
        endif;

        // $C_Unit = collect($getUnit)->map(function($items, $key){
        //     if( is_object($items) ):
        //         $Data    = collect($items)->map(function($SubItems, $SubKey){
        //             if($SubKey == 'jenis' ):
        //                 $subData    = ( $SubItems == 1 ) ? 'MOTOR' : 'MOBIL'; 
        //             else:
        //                 $subData    = $SubItems;
        //             endif;
        //             return $subData;
        //         });

        //     else:
        //         if($key == 'jenis' ):
        //             $Data    = ( $items == 1 ) ? 'MOTOR' : 'MOBIL'; 
        //         else:
        //             $Data    = $items;
        //         endif;

        //     endif;
        //     return $getUnit;
        // });
        
        return ['data' => $getUnit];

    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}