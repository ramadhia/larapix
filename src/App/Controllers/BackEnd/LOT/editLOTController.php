<?php
namespace Mhpix\App\Controllers\BackEnd\LOT;
/*
| Name Controller    : editLOTController
| Controller Created : 2018/12/19 00:00:12
|
*/
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Event_Lot;

class editLOTController
{
    public function index($id){
        $t_Event_Lot    = t_Event_Lot::with('event','unit')->whereId($id)->first();
        $t_Event        = t_Event::whereNotIn('id', [$t_Event_Lot->event_id])->whereActive(1)->get();
        
        
        if( empty($t_Event_Lot) ):
            abort(404);
        endif;

        return view('BackEnd.requires.LOT.editLOT', [
            't_Event' => $t_Event,
            't_Event_Lot' => $t_Event_Lot
            ]);
    }
    public function post(Request $req){
        if( $req->change_event == 'on'):
            $rules  = ['id_event' => 'required'];
        else:
            $rules  = [];
        endif;
        $messages   = [
            'required' => 'Isi kolom <b>Ganti Event</b> dengan benar.'
        ];
        $validator  = Validator::make($req->all(), $rules, $messages);
        if ( $validator->fails() ) {
            return redirect()->route('lot.edit', $req->id)->withErrors($validator);
        }else{

            $t_Event    = t_Event::find($req->id_event);
            /* ============= SET LOT =========== */
                $getLastLOT = t_Event_Lot::where('event_id', $req->id_event)->orderBy('name','DESC')->first();

                if( empty($getLastLOT) ):
                    $setLOT    = 1;
                else:
                    $setLOT    = preg_replace('/[a-zA-Z]+/','', $getLastLOT->name);
                    $setLOT    = ( (int) $setLOT ) + 1;
                endif;
                
                $Jenis_LOT      = ( $t_Event->jenis_unit == 1 ) ? 'A' : 'B';
                $setNameLOT     = $Jenis_LOT.str_pad($setLOT, 3, '0', STR_PAD_LEFT);
                /* ============= SET LOT =========== */
                // return $req->all();
                $Event_LOT        = t_Event_Lot::whereId($req->id)->first();
                $Event_LOT->name  = $setNameLOT;
                $Event_LOT->event_id  = $req->id_event;
                $Event_LOT->updated_at= Carbon::now('Asia/Jakarta')->toDateTimeString();
                $Event_LOT->updated_by= Auth::user()->username;
                
                if($Event_LOT->save()):
                    return redirect()->route('lot')->with('succ', 'Sukses Edit LOT');
                else:
                    return redirect()->route('lot.add')->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
                endif;

            return redirect()->route('lot');
        }
    } 
}