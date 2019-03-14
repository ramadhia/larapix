<?php
namespace Mhpix\App\Controllers\BackEnd\Events\EventsRegister;
/*
| Name Controller    : addEventsRegisterController
| Controller Created : 2018/11/21 22:27:05
|
*/
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Customer;
use Mhpix\App\Model\t_Transaksi;
use Mhpix\App\Model\t_Event_Register;

class addEventsRegisterController
{
    public function index(){
        $t_Event    = t_Event::whereActive(1)->get();
        return view('BackEnd.requires.Events.EventsRegister.addEventsRegister', [ 't_Event' => $t_Event]);
    }

    public function post(Request $req){

        $TrxID          = 'REG'.str_pad(rand(1,5000), 4, '0', STR_PAD_LEFT);
        $deposit        = str_replace(',', '', $req->deposit);
        $E_Register     = new t_Event_Register;
        $E_Register->event_id   = $req->id_event;
        $E_Register->cust_id    = $req->id_cust;
        $E_Register->deposit    = ( $req->jns_pem == 1 ) ? $deposit : 0;
        $E_Register->created_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $E_Register->transaksi_id = $TrxID;

        /* ============= SET NIPL =========== */
        $getRegister    = t_Event_Register::where('event_id', $req->id_event)->orderBy('nipl','DESC')->first();
        $getNIPL        = ( empty($getRegister) ) ? 1 : ( (int) $getRegister->nipl ) +1;
        $setNIPL        = str_pad($getNIPL, 3, '0', STR_PAD_LEFT);
        $E_Register->nipl       = $setNIPL;
        /* ============= SET NIPL =========== */
        
        
        $t_Transaksi    = new t_Transaksi;
        $t_Transaksi->id        = $TrxID;
        $t_Transaksi->cust_id   = $req->id_cust;
        $t_Transaksi->amount    = $deposit;
        $t_Transaksi->status    = 0;
        $t_Transaksi->type_transaksi    = 1;
        $t_Transaksi->created_at        = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $t_Transaksi->created_by        = 'system';
        $t_Transaksi->updated_at        = NULL;
        $t_Transaksi->updated_by        = NULL;
        
        if($E_Register->save()):
            $t_Transaksi->save();
            return redirect()->route('events-register')->with('succ',"Success Register");
        else:
            return redirect()->route('events-register.add')->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
        endif;
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}