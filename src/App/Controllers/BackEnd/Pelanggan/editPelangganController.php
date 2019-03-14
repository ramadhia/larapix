<?php
namespace Mhpix\App\Controllers\BackEnd\Pelanggan;
/*
| Name Controller    : editPelangganController
| Controller Created : 2018/11/17 22:40:51
|
*/
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Admin;
use Mhpix\App\Model\t_Customer;

class editPelangganController
{
    public function index(){
        return view('BackEnd.requires.Pelanggan.editPelanggan');
    }
    
    public function post(Request $req){
        $Tgl_lahir  = Carbon::parse($req->tgl_lahir)->format('Y-m-d');

        $t_Cust         = t_Customer::find($req->id)->first();
        $t_Cust->nama   = $req->nama;
        $t_Cust->email  = $req->email;
        $t_Cust->alamat = $req->alamat;
        $t_Cust->agama  = (int) $req->agama;
        $t_Cust->no_identitas   = (int) $req->no_identitas;
        $t_Cust->tanggal_lahir  = $Tgl_lahir;
        $t_Cust->no_telepon     = $req->telepon;
        $t_Cust->no_mobile      = $req->mobile;
        $t_Cust->provinsi       = $req->provinsi;
        $t_Cust->kota           = $req->kota;
        $t_Cust->kecamatan      = $req->kecamatan;
        $t_Cust->kelurahan      = $req->kelurahan;
        $t_Cust->updated_at     = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $t_Cust->updated_by     = Auth::user()->username;

        // return $req->all();
        if($t_Cust->save()):
            $t_Admin    = t_Admin::find($t_Cust->user_id);
            $t_Admin->name      = $req->nama;
            $t_Admin->username  = $req->username;
            $t_Admin->email     = $req->email;
            $t_Admin->updated_at= Carbon::now('Asia/Jakarta')->toDateTimeString();
            $t_Admin->save();
            return redirect()->route('pelanggan.read', $req->id)->with('succ', 'Success update Profile');
        else:
            return redirect()->route('pelanggan.read', $req->id)->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
        endif;
        // return $req->all();
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}