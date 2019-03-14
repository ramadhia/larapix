<?php
namespace Mhpix\App\Controllers\BackEnd\LOT;
/*
| Name Controller    : addLOTController
| Controller Created : 2018/12/19 00:00:12
|
*/
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Event_Lot;

class addLOTController
{
    public function index(){
        $t_Event    = t_Event::whereActive(1)->get();
        return view('BackEnd.requires.LOT.addLOT', ['t_Event' => $t_Event ]);
    }

    public function post(Request $req){
        $rules      = [
            'unit_id' => 'required',
            'id_event' => 'required'
        ];
        $messages   = [
            'required' => 'Kolom <b>Pilih Event</b> & <b>Pilih Unit</b> harus diisi '
        ];
        $validator  = Validator::make($req->all(), $rules, $messages);

        if ( $validator->fails() ):
            return redirect()->route('lot.add')->withErrors($validator);
        else:
            
            $Event_Lot  = t_Event_Lot::with('event', 'unit')->where('unit_id', $req->unit_id)->first();
            
            /* ============= GET EVENT =========== */
            $t_Event    = t_Event::find($req->id_event);
            if( empty($t_Event) ):
                return redirect()->route('lot.add')->with('err', 'Event tidak ditemukan.');
            endif;
            /* ============= GET EVENT =========== */
        
            if( empty($Event_Lot) ):

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
                $new_LOT        = new t_Event_Lot;
                $new_LOT->name  = $setNameLOT;
                $new_LOT->event_id  = $req->id_event;
                $new_LOT->unit_id   = $req->unit_id;
                $new_LOT->created_at= Carbon::now('Asia/Jakarta')->toDateTimeString();
                $new_LOT->created_by= Auth::user()->username;

                if($new_LOT->save()):
                    return redirect()->route('lot')->with('succ', 'Sukses tambah LOT');
                else:
                    return redirect()->route('lot.add')->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
                endif;

            else:
                
                $Used_Lot   = '<b>LOT <a href="'.route('lot.edit', $Event_Lot->id).'">'.$Event_Lot->name.' ('.$Event_Lot->event->name.')</a></b>';
                return redirect()->route('lot.add')->with('err','Unit sudah digunakan di '.$Used_Lot)->withInput();
            endif;
            
            return $req->all();
        endif;
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}