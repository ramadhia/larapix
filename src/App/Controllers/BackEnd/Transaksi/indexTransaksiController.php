<?php
namespace Mhpix\App\Controllers\BackEnd\Transaksi;
/*
| Name Controller    : indexTransaksiController
| Controller Created : 2019/01/18 03:12:56
|
*/
use Auth;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Customer;
use Mhpix\App\Model\t_Transaksi;

class indexTransaksiController
{
    public function index(){

        if( Auth::user()->role_id == 100 ):
            $Customer       = t_Customer::where('user_id', Auth::user()->id )->first();
            $t_Transaksi    = t_Transaksi::with('customer')->orderBy('created_at','DESC')->where('cust_id', $Customer->id )->get();
        else:
            $t_Transaksi    = t_Transaksi::with('customer')->orderBy('created_at','DESC')->get();
        endif;

        // return $t_Transaksi;
        return view('BackEnd.requires.Transaksi.indexTransaksi', ['t_Transaksi' => $t_Transaksi]);
    }
    public function destroy(Request $req){
        $t_Transaksi    = t_Transaksi::find($req->id);
        if($req->id){
            if( !$t_Transaksi ){
                return redirect()->route('transaksi')->with('err','Transaksi not Found');
            }else{
                if( t_Transaksi::whereId($req->id)->delete() ){
                    return redirect()->route('transaksi')->with('succ','Success Delete Transaksi');
                }else{
                    return redirect()->route('transaksi')->with('err','Something Wrong');
                }
            }
        }else{
            return redirect()->route('events')->with('err','Parameter ID not Found');
        }
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}