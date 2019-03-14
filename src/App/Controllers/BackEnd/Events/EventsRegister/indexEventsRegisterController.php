<?php
namespace Mhpix\App\Controllers\BackEnd\Events\EventsRegister;
/*
| Name Controller    : indexEventsRegisterController
| Controller Created : 2018/11/21 22:27:05
|
*/
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Customer;
use Mhpix\App\Model\t_Event_Log;
use Mhpix\App\Model\t_Event_Lot;
use Mhpix\App\Model\t_Event_Register;

class indexEventsRegisterController
{
    protected $Event_Register;

    public function __construct(t_Event_Register $Event_Register){
        $this->Event_Reg = $Event_Register;
    }
    public function index(Request $req){
        $t_Event_Reg    = $this->Event_Reg;
        $typeEvent      = ( $req->event != NULL ) ? $req->event : false;

        if( Auth::user()->role_id != 100 ):
            if( $typeEvent ):
                if($typeEvent == 'all'):
                    $t_Event_Reg = $t_Event_Reg->with('event','customer')->orderBy('created_at','DESC')->get();
                else:
                    $t_Event_Reg = $t_Event_Reg->with('event', 'customer')->where('event_id', $typeEvent)->orderBy('created_at', 'DESC')->get();
                endif;
            else:
                $t_Event_Reg = $t_Event_Reg->with('event', 'customer','transaksi')->whereHas('event', function($query){
                        return $query->where('active', 1);
                })->orderBy('created_at', 'DESC')->get();
            endif;

            $t_Event        = t_Event::where('active', 1)->get();
        else:
            $Customer       = t_Customer::where('user_id', Auth::user()->id )->first();
            if( $typeEvent ):
                if($typeEvent == 'all'):
                    $t_Event_Reg = $t_Event_Reg->with('event','customer')->where('cust_id', $Customer->id )->orderBy('created_at','DESC')->get();
                else:
                    $t_Event_Reg = $t_Event_Reg->with('event', 'customer')->where('cust_id', $Customer->id )->where('event_id', $typeEvent)->orderBy('created_at', 'DESC')->get();
                endif;
            else:
                $t_Event_Reg = $t_Event_Reg->with('event', 'customer','transaksi')
                            ->where('cust_id', $Customer->id )
                            ->whereHas('event', function($query){
                                    return $query->where('active', 1);
                            })->orderBy('created_at', 'DESC')->get();
            endif;

            $t_Event        = t_Event::where('active', 1)->get();
        endif;
        // return $t_Event_Reg;
        return view('BackEnd.requires.Events.EventsRegister.indexEventsRegister', [
                't_Event_Register' => $t_Event_Reg,
                't_Event' => $t_Event
            ]);
    }
    public function destroy(Request $req){
        $t_Event_Register    = t_Event_Register::find($req->id);
        if($req->id){
            if( !$t_Event_Register ){
                return redirect()->route('events-register')->with('err','Data Register Event tidak ditemukan.');
            }else{
                if( $t_Event_Register->whereId($req->id)->delete() ){
                    return redirect()->route('events-register')->with('succ','Success Delete');
                }else{
                    return redirect()->route('events-register')->with('err','Something Wrong');
                }
            }
        }else{
            return redirect()->route('events-register')->with('err','Parameter ID not Found');
        }
    }
    
    public function listRegister(Request $req){
        if( $req->id ):
            $Register    = t_Event_Lot::where('event_id', $req->id)->get();
            $getRegister = collect();
            foreach($Register as $row){
                if($row->register_id == NULL) continue;
                $getRegister->push($row->register_id);
            }
            $NotIn      = $getRegister->unique()->values()->all();

            $Event_Register = $this->Event_Reg->whereHas('transaksi',function($query){
                $query->where('status',1);
            })->with('transaksi','customer:id,nama')
            ->where('event_id', $req->id)
            ->whereNotIn('id',$NotIn)
            ->orderBy('nipl', 'ASC')
            ->get();

            $Data   = [];
            foreach($Event_Register as $key => $row ){
                $Data[$key]['id'] = $row->id;
                $Data[$key]['event_id']   = $row->event_id;
                $Data[$key]['cust_id']    = $row->cust_id;
                $Data[$key]['cust_name']  = $row->customer->nama;
                $Data[$key]['nipl']       = $row->nipl;
                $Data[$key]['deposit']    = $row->deposit;
                $Data[$key]['transaksi_id']   = $row->transaksi_id;
                $Data[$key]['created_at'] = $row->created_at;
            }
            return ['data' => $Data];
        else:
            abort(404);
        endif;
    }
    public function setPemenang(Request $req){
        $t_Lot  = t_Event_Lot::with('unit')->where('id', $req->lot_id)->first();
        $harga  = str_replace(',', '', $req->harga_terbentuk);

        if( empty($t_Lot)):
            return redirect()->route('events')->with('err','Lot tidak ditemukan');
        endif;

        if( $harga < $t_Lot->unit->harga_limit ):
            return redirect()->route('events.read', $t_Lot->event_id )->with('err','Harga Terbentuk tidak boleh lebih kecil dari Harga Limit.');
        endif;

        $Lot    = t_Event_Lot::find($req->lot_id);
        $Lot->status  = 1;
        $Lot->register_id = $req->register_id;
        $Lot->konfirmasi  = 0;
        $Lot->updated_at  = Carbon::now('Asia/Jakarta');
        $Lot->updated_by  = Auth::user()->username;
        $Lot->harga_terbentuk = $harga;
        

        /* ==== LOG ==== */
        $Reg    = $this->Event_Reg->where('id', $req->register_id)->first();
        $Log    = new t_Event_Log;
        $Log->event_id  = $Lot->event_id;
        $Log->cust_id   = $Reg->cust_id;
        $Log->lot       = $Lot->name;
        $Log->nipl      = $Reg->nipl;
        $Log->tawaran   = $harga;
        $Log->created_at= Carbon::now('Asia/Jakarta');
        // return $Log;
        /* ==== LOG ==== */
        if($Lot->save()):
            $Log->save();
            return redirect()->route('events.read', $t_Lot->event_id )->with('succ', 'Sukses Set Pemenang');
        else:
            return redirect()->route('events.read', $t_Lot->event_id )->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
        endif;

        // return $req->all();
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}