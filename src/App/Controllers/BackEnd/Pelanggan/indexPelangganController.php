<?php
namespace Mhpix\App\Controllers\BackEnd\Pelanggan;
/*
| Name Controller    : indexPelangganController
| Controller Created : 2018/11/17 22:40:51
|
*/
use PDF;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Admin;
use Mhpix\App\Model\t_Customer;

class indexPelangganController
{
    public function index(){
        $Cust   = t_Customer::orderBy('created_at','DESC')->get();
        $pdf = PDF::loadView('BackEnd.requires.Pelanggan.CustPDF', ['Cust' => $Cust]);

        // return $pdf->download('customers.pdf');
        // return $pdf->stream('customers.pdf',[
        //     'attachment' => false
        // ]);

        return view('BackEnd.requires.Pelanggan.indexPelanggan', [ 't_Cust' => $Cust ]);
    }
    
    public function destroy(Request $req){
        $t_Cust    = t_Customer::find($req->id);
        if($req->id){
            if( !$t_Cust ){
                return redirect()->route('pelanggan')->with('err','Data Pelanggan tidak ditemukan.');
            }else{
                t_Admin::where('id', $t_Cust->user_id)->delete();
                if( $t_Cust->whereId($req->id)->delete() ){
                    return redirect()->route('pelanggan')->with('succ','Success Delete');
                }else{
                    return redirect()->route('pelanggan')->with('err','Something Wrong');
                }
            }
        }else{
            return redirect()->route('pelanggan')->with('err','Parameter ID not Found');
        }

    }
    public function listCust(Request $req){
        if( empty($req->q) ):
            abort(404);
            // return response()->json(['data not found'], 404);
        endif;
        $query      = str_replace(' ', '%', $req->q);
        $t_Customer = t_Customer::where('nama','like', "%${query}%")
                    ->orWhere('no_identitas','like', "%${query}%")
                    ->orWhere('alamat','like', "%${query}%")
                    ->get();
        return response()->json(['items' => $t_Customer ]);
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}