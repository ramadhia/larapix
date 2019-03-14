<?php
namespace Mhpix\App\Controllers\BackEnd\Events;
/*
| Name Controller    : addEventsController
| Controller Created : 2018/11/17 23:28:16
|
*/
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;

class addEventsController
{
    public function index(){
        
        return view('BackEnd.requires.Events.addEvents');
    }

    public function post(Request $req){
        // return $req->all();
        $rules      = [
            'nm_event' => 'required',
            'jenis' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ];
        $messages   = [
            'unit_id.required' => 'Pilih <b>Unit</b> untuk membuat Event Baru'
        ];
        $validator  = Validator::make($req->all(), $rules, $messages);
        if ( $validator->fails() ) {
            return redirect()->route('events.add')->withErrors($validator);
        }else{
            $Now        = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
            $t_Event            = new t_Event;
            $t_Event->name      = $req->nm_event;      
            $t_Event->active    = ( $req->active == 'on') ? 1 : 0;     
            $t_Event->closing_date  = NULL;
            $t_Event->start_date    = $req->start_date.':00';
            $t_Event->end_date      = $req->end_date.':00';
            $t_Event->jenis_unit    = (int) $req->jenis;
            $t_Event->created_at    = $Now;
            $t_Event->created_by    = Auth::user()->username;
            
            if($t_Event->save()):
                return redirect()->route('events')->with('succ',"Success add Event <b>".$req->nm_event."</b>");
            else:
                return redirect()->route('events.add')->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
            endif;
        }
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}