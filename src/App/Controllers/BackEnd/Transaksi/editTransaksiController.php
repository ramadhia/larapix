<?php
namespace Mhpix\App\Controllers\BackEnd\Transaksi;
/*
| Name Controller    : editTransaksiController
| Controller Created : 2019/01/18 03:12:56
|
*/
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;

use Mhpix\App\Model\t_Transaksi;
use Mhpix\App\Model\t_Pembayaran;
use Mhpix\App\Model\t_Event_Lot;
use Mhpix\App\Model\t_Event_Register;

class editTransaksiController extends Controller
{
    public function index(){
        return view('BackEnd.requires.Transaksi.editTransaksi');
    }
    public function post(){
    /* INSERT YOUR POST METHOD HERE */

    }

    public function pembayaran(Request $req){
        // return $req->all();
        if( $req->state != 'konfirmasi' ):
            if( $req->metode == 2 ):
                $path   = $req->file('bukti_tf')->store('bukti');
                $bukti  = str_replace('bukti/','',$path);
                $Lelang_Rek     = explode('-', $req->bank_tf);
            else:
                $bukti  = NULL;
                $Lelang_Rek     = [NULL,NULL];
            endif;
            $jumlah = str_replace(',','', $req->jumlah);

            $t_Pembayaran   = new t_Pembayaran;
            $t_Pembayaran->metode   = $req->metode;
            $t_Pembayaran->nama     = $req->nama;
            $t_Pembayaran->bank     = $req->bank;
            $t_Pembayaran->rekening = $req->no_rek;
            $t_Pembayaran->jumlah   = $jumlah;
            $t_Pembayaran->bukti    = $bukti;
            $t_Pembayaran->transfer_bank    = $Lelang_Rek[0];
            $t_Pembayaran->transfer_rek     = $Lelang_Rek[1];
            $t_Pembayaran->tanggal_bayar    = $req->tanggal_bayar;
            $t_Pembayaran->created_at   = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $t_Pembayaran->created_by   = Auth::user()->username;
            $t_Pembayaran->updated_at   = NULL;
            $t_Pembayaran->updated_by   = NULL;

            if( $t_Pembayaran->save() ):
                $t_Transaksi    = t_Transaksi::find($req->trx_id);
                $t_Transaksi->pembayaran_id = $t_Pembayaran->id;
                $t_Transaksi->status        = ( $req->metode == 1 ) ? 1 : 3 ;
                $t_Transaksi->updated_at    = Carbon::now('Asia/Jakarta')->toDateTimeString();
                $t_Transaksi->updated_by    = 'system-pembayaran';

                if( $t_Transaksi->save() ):
                    return redirect()->route('transaksi.read',  $req->trx_id)->with('succ', 'Terimakasih sudah melakukan pembayaran, pembayaran akan diverifikasi oleh kami.');
                else:
                    return redirect()->route('transaksi.read',  $req->trx_id)->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
                endif;

            else:
                return redirect()->route('transaksi.read',  $req->trx_id)->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
            endif;
        
        else:
            if( Auth::user()->role_id == 100 ):
                return redirect()->route('transaksi.read',  $req->trx_id)->with('err','Something Wrong !! Code: 121');
            endif;

            $t_Transaksi    = t_Transaksi::find($req->trx_id);
            $t_Transaksi->status    = 1;
            $t_Transaksi->updated_at= Carbon::now('Asia/Jakarta');
            $t_Transaksi->updated_by= Auth::user()->username;
            
            if( $t_Transaksi->save() ):
                if( $t_Transaksi->type_transaksi == 1 ):
                    $Detail_Trx = t_Event_Register::where('transaksi_id', $req->trx_id)->first();
                    $Detail_Trx->deposit    = $t_Transaksi->amount;
                else:
                    $Detail_Trx = t_Event_Lot::where('transaksi_id', $req->trx_id)->first();
                    $Detail_Trx->transaksi_status   = 1;
                endif;
                
                $t_Pembayaran   = t_Pembayaran::find($t_Transaksi->pembayaran_id);
                $t_Pembayaran->updated_at   = Carbon::now('Asia/Jakarta');
                $t_Pembayaran->updated_by   = Auth::user()->username;
                
                $Detail_Trx->save();
                $t_Pembayaran->save();
                
                return redirect()->route('transaksi')->with('succ', 'Pembayaran transaksi #'.$req->trx_id.' berhasil dikonfirmasi.');
            else:
                return redirect()->route('transaksi.read',  $req->trx_id)->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
            endif;

        endif;
    }
    public function bukti($file){
        $path       = 'bukti/'.$file;
        try{
            $download   = Storage::download($path);
        }catch(FileNotFoundException $e){
            abort(404);
        }
        return $download;
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}