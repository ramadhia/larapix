<?php
namespace Mhpix\App\Controllers\BackEnd;
/*
| Name Controller    : printController
| Controller Created : 2018/11/17 23:28:16
|
*/
use PDF;
use Auth;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Transaksi;
use Mhpix\App\Model\t_Event_Log;
use Mhpix\App\Model\t_Event_Lot;
use Mhpix\App\Model\t_Event_Register;

class printController
{
    public function nipl(Request $req){
        $Event_Register = t_Event_Register::with('event')->where('id',$req->id)->first();
        
        if( empty($Event_Register) ):
            abort(404);
        endif;

        $rawDate    = ( $Event_Register->event ) ? $Event_Register->event->start_date : '1999-01-01 00:00';
        
        // setlocale (LC_TIME, 'INDONESIA');

        $pdf = PDF::loadView('BackEnd.requires.Print.NIPL', [
            'Event_Register' => $Event_Register,
            'Date' => Carbon::parse($rawDate)
            ]);
        // return view('BackEnd.requires.Print.NIPL', ['Event_Register' => $Event_Register]);
        return $pdf->stream();
    }

    public function bap(Request $req){
        $t_Event_Lot    = t_Event_Lot::with('event','unit','register.customer')->where('id', $req->id)->first();
        
        if( empty($t_Event_Lot) ):
            abort(404);
        endif;

        if( $t_Event_Lot->status == 1 && $t_Event_Lot->konfirmasi == 0 ):
            $Deposit        = ( $t_Event_Lot->register) ? (int) $t_Event_Lot->register->deposit : 0;
            $Amount         = $t_Event_Lot->harga_terbentuk - $Deposit;
            // return $Deposit;

            /* ============ BUAT TRANSAKSI ============ */
            $TrxID          = 'LOT'.str_pad(rand(1,5000), 4, '0', STR_PAD_LEFT);
            $t_Transaksi    = new t_Transaksi;
            $t_Transaksi->id        = $TrxID;
            $t_Transaksi->cust_id   = $t_Event_Lot->register->cust_id;
            $t_Transaksi->amount    = $Amount;
            $t_Transaksi->status    = 0;
            $t_Transaksi->type_transaksi    = 2;
            $t_Transaksi->created_at        = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $t_Transaksi->created_by        = 'system';
            $t_Transaksi->updated_at        = NULL;
            $t_Transaksi->updated_by        = NULL;
            /* ============ BUAT TRANSAKSI ============ */
            
            $Lot_Confirm    = t_Event_Lot::find($req->id);
            $Lot_Confirm->konfirmasi    = 1;
            $Lot_Confirm->transaksi_id  = $TrxID;
            $Lot_Confirm->updated_at    = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $Lot_Confirm->updated_by    = Auth::user()->username;
            
            // return $t_Transaksi;
            if($Lot_Confirm->save()):
                $t_Transaksi->save();
                return redirect()->route('events.read', $t_Event_Lot->event->id)->with('succ',"Success Konfirmasi <b>No.LOT ".$t_Event_Lot->name." </b>".$req->name);
            else:
                return redirect()->route('events.read', $t_Event_Lot->event->id)->with('err','Something Wrong !! Code: 121');  // Jika gagal menyimpan data baru
            endif;
            
        elseif( $t_Event_Lot->status == 1 && $t_Event_Lot->konfirmasi == 1 ):

            $Now    = Carbon::now('Asia/Jakarta');    
            $no_bap = $t_Event_Lot->name.'/BAPPL/BLM/'.$t_Event_Lot->event->id.'/'.Carbon::parse($t_Event_Lot->event->created_at)->format('m/Y');
            $nipl   = ( $t_Event_Lot->register ) ? $t_Event_Lot->register->nipl : '-';
            $no_pol = ( $t_Event_Lot->unit ) ? $t_Event_Lot->unit->plat_kode.' '.$t_Event_Lot->unit->plat_no.' '.$t_Event_Lot->unit->plat_seri : '-';
            $no_rangka  = ( $t_Event_Lot->unit ) ? $t_Event_Lot->unit->no_rangka : '-'; 
            $no_mesin   = ( $t_Event_Lot->unit ) ? $t_Event_Lot->unit->no_mesin : '-';
            $tahun_unit = ( $t_Event_Lot->unit ) ? $t_Event_Lot->unit->tahun : '-';
            $model_unit = ( $t_Event_Lot->unit ) ? $t_Event_Lot->unit->merk.' '.$t_Event_Lot->unit->model : '-';
            $warna_unit = ( $t_Event_Lot->unit ) ? $t_Event_Lot->unit->warna : '-';
            $harga_terbentuk    = $t_Event_Lot->harga_terbentuk;

            $Data   = (object) [
                'no_bap' => $no_bap,
                'nipl' => $nipl,
                'no_pol' => $no_pol,
                'no_rangka' => $no_rangka,
                'no_mesin' => $no_mesin,
                'tahun_unit' => $tahun_unit,
                'model_unit' => $model_unit,
                'warna_unit' => $warna_unit,
                'hari_ini' => $this->hari($Now->format('N')).' tanggal '.$this->terbilang( $Now->format('d') ).' bulan '.$this->bulan( $Now->format('m') ).' tahun '.$this->terbilang( $Now->format('Y') ).' ('.$Now->format('d-m-Y').')'
            ];
            $pdf = PDF::loadView('BackEnd.requires.Print.BAP', [ 'Data' => $Data ]);
            return $pdf->stream();
            // return view('BackEnd.requires.Print.BAP', ['Data' => $Data]);
        else:
            return 'invalid';
        endif;
        
    }
    public function hari($n){
        $hari = [1=> 'Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu','Minggu'];
        return $hari[$n];
    }
    public function penyebut($nilai) {

        // return abs($nilai);
        $nilai  = abs($nilai);
        $huruf  = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        $temp   = "";
        if ($nilai < 12) {
            $temp = " ".$huruf[$nilai];
        } else if ($nilai <20) {
            $temp = $this->penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
        }

        return ucwords($temp);
    }

    public function terbilang($nilai) {
        if($nilai < 0) {
            $hasil = "minus ". trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return $hasil;
    }
    public function tgl_aja($tgl_a){
        return substr($tgl_a,8,2); 
    }
    public function bln_aja($bulan_a){
        return $this->getBulan(substr($bulan_a,5,2));
    }
    public function thn_aja($thn){
        return substr($thn,0,4);
    }
    public function tgl_indo($tgl){
        $tanggal    = substr($tgl,8,2);
        $bulan      = $this->getBulan(substr($tgl,5,2));
        $tahun      = substr($tgl,0,4);
        return $tanggal.' '.$bulan.' '.$tahun;  
    }
    public function bulan($bln){
        switch ($bln){
            case 1:
                return "Januari";
            break;
            case 2:
                return "Februari";
            break;
            case 3:
                return "Maret";
            break;
            case 4:
                return "April";
            break;
            case 5:
                return "Mei";
            break;
            case 6:
                return "Juni";
            break;
            case 7:
                return "Juli";
            break;
            case 8:
                return "Agustus";
            break;
            case 9:
                return "September";
            break;
            case 10:
                return "Oktober";
            break;
            case 11:
                return "November";
            break;
            case 12:
                return "Desember";
            break;
        }
   }
}