<?php
namespace Mhpix\App\Controllers\BackEnd\Events;
/*
| Name Controller    : editEventsController
| Controller Created : 2018/11/17 23:28:16
|
*/
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;

class editEventsController
{
    public function index($id){
        $t_Event  = t_Event::whereId($id)->with('log.customer','winner.customer')->first();
        // return $t_Event;
        if( empty($t_Event) ):
            abort(404);
        endif;
        
        return view('BackEnd.requires.Events.editEvents', [ 
            't_Event' => $t_Event, 
            't_Event_Log' => $t_Event->log 
        ]);
    }
    public function post(Request $req){

        $validatedData = $req->validate([
            'nm_event' => 'required',
            // 'jenis' => 'required',
            'start_date' => 'required|date_format:Y-m-d H:i',
            'end_date' => 'required|date_format:Y-m-d H:i',
        ]);
    
        $Now        = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $Active     = ( $req->closing_date == 'on') ? 0 : ( $req->active == 'on') ? 1 : 0;
        $t_Event    = t_Event::find($req->id);
        $t_Event->name      = $req->nm_event;        
        $t_Event->active    = $Active;     
        $t_Event->closing_date  = ( $req->closing_date == 'on') ? $Now : NULL;
        // $t_Event->jenis_unit    = $req->jenis;
        $t_Event->start_date    = $req->start_date.':00';  
        $t_Event->end_date      = $req->end_date.':00';
        $t_Event->updated_at    = $Now;
        $t_Event->updated_by    = Auth::user()->username;

        if($t_Event->save()):
            return redirect()->route('events')->with('succ',"Success edit Event <b>".$req->nm_event."</b>");
        else:
            return redirect()->route('events.edit', $req->id)->with('err','Something Wrong !! Code: 121');  // Jika gagal menyimpan data baru
        endif;

    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}