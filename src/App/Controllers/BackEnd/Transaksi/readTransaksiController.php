<?php
namespace Mhpix\App\Controllers\BackEnd\Transaksi;
/*
| Name Controller    : readTransaksiController
| Controller Created : 2019/01/18 03:12:56
|
*/
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Transaksi;
use Mhpix\App\Model\t_Event_Lot;
use Mhpix\App\Model\t_Event_Register;

class readTransaksiController
{
    public function index($id){ 
        $t_Trx    = t_Transaksi::with('customer','pembayaran')->find($id);
        
        if( empty($t_Trx) ):
            abort(404);
        endif;
        
        if( $t_Trx->type_transaksi == 1 ):
            $Detail_Trx = t_Event_Register::with('event')->where('transaksi_id', $t_Trx->id)->first();
        else:
            $Detail_Trx = t_Event_Lot::with('event','unit')->where('transaksi_id', $t_Trx->id)->first();
        endif;

        // return $Detail_Trx;
        return view('BackEnd.requires.Transaksi.readTransaksi',[
            't_Trx' => $t_Trx,
            'Detail' => $Detail_Trx
            ]);
    }
      public function post(){
        /* INSERT YOUR POST METHOD HERE */

        }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}