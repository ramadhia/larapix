<?php
namespace Mhpix\App\Controllers\BackEnd\Events\EventsRegister;
/*
| Name Controller    : editEventsRegisterController
| Controller Created : 2018/11/21 22:27:05
|
*/
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event_Register;

class editEventsRegisterController
{
    public function index($id){
        $E_Register   = t_Event_Register::with('event', 'customer')->whereId($id)->first();
        // return $E_Register;
        if( empty($E_Register) ):
            abort(404);
        endif;
        return view('BackEnd.requires.Events.EventsRegister.editEventsRegister', ['E_Register' => $E_Register]);
    }

    public function post(Request $req){

        $E_Register = t_Event_Register::find($req->id);
        $find       = ['/,/', '/Rp./'];
        $replace    = ['', ''];
        $tarikan    = preg_replace($find, $replace, $req->tarikan);
        $tarikan    = ( $tarikan > $E_Register->deposit ) ? $E_Register->deposit : $tarikan;
        
        $E_Register->tarikan        = $tarikan;
        $E_Register->sisa_deposit   = ( $E_Register->deposit - $tarikan );
        $E_Register->updated_by     = Auth::user()->username;
        $E_Register->updated_at     = Carbon::now('Asia/Jakarta')->toDateTimeString();

        if($E_Register->save()):
            return redirect()->route('events-register')->with('succ', 'Success update');
        else:
            return redirect()->route('events-register.edit', $req->id )->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
        endif;

    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}