<?php
namespace Mhpix\App\Controllers\BackEnd\Events\EventsRegister;
/*
| Name Controller    : addEventsRegisterController
| Controller Created : 2018/11/21 22:27:05
|
*/
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Customer;
use Mhpix\App\Model\t_Transaksi;
use Mhpix\App\Model\t_Event_Register;

class CustController
{
    public function index($id){
        // return 'test';
        $t_Event    = t_Event::where('id',$id)->first();
        if( empty($t_Event) ):
            abort(404);
        endif;
        $t_Cust     = t_Customer::where('user_id', Auth::user()->id )->first();
        return view('BackEnd.requires.Events.EventsRegister.CustRegister', [ 
            't_Event' => $t_Event,
            'Cust' => $t_Cust
            ]);
    }

    public function post(Request $req){
        // return 'test';
        $TrxID      = 'REG'.str_pad(rand(1,5000), 4, '0', STR_PAD_LEFT);
        $t_Cust     = t_Customer::where('user_id', Auth::user()->id )->first();
        $deposit    = str_replace(',', '', $req->deposit);
        $E_Register = new t_Event_Register;
        $E_Register->event_id   = $req->id_event;
        $E_Register->cust_id    = $t_Cust->id;
        $E_Register->deposit    = 0;
        $E_Register->created_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $E_Register->updated_at = NULL;
        $E_Register->updated_by = NULL;
        $E_Register->transaksi_id = $TrxID;

        /* ============= SET NIPL =========== */
        $getRegister    = t_Event_Register::where('event_id', $req->id_event)->orderBy('nipl','DESC')->first();
        $getNIPL        = ( empty($getRegister) ) ? 1 : ( (int) $getRegister->nipl ) +1;
        $setNIPL        = str_pad($getNIPL, 3, '0', STR_PAD_LEFT);
        $E_Register->nipl       = $setNIPL;
        /* ============= SET NIPL =========== */
        
        
        $t_Transaksi    = new t_Transaksi;
        $t_Transaksi->id        = $TrxID;
        $t_Transaksi->cust_id   = $t_Cust->id;
        $t_Transaksi->amount    = $deposit;
        $t_Transaksi->status    = 0;
        $t_Transaksi->type_transaksi    = 1;
        $t_Transaksi->created_at        = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $t_Transaksi->created_by        = 'system';
        $t_Transaksi->updated_at        = NULL;
        $t_Transaksi->updated_by        = NULL;
        
        if($E_Register->save()):
            $t_Transaksi->save();
            return redirect()->route('transaksi.read', $TrxID)->with('succ',"Success Register Event");
        else:
            return redirect()->route('events-register.add')->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
        endif;
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}