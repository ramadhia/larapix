<?php
namespace Mhpix\App\Controllers\BackEnd\Unit;
/*
| Name Controller    : editUnitController
| Controller Created : 2018/11/17 23:20:36
|
*/
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix;
use Mhpix\App\Model\t_Unit;

class editUnitController
{
    public function index($id){
        $t_Unit = t_Unit::find($id);
        if( empty($t_Unit) ):
            abort(404);
        endif;
        // return $t_Unit;
        return view('BackEnd.requires.Unit.editUnit', [ 't_Unit' => $t_Unit ]);
    }
    
    public function post(Request $req){
        
        if( !$req->id ):
            return redirect()->route('unit');
        endif;

        if( strpos($req->no_plat, '-') == FALSE):
            return redirect()->route('unit.edit', $req->id)->with('err','Format No. Plat tidak valid.')->withInput();
        endif;

        $rules      = [
            'merk' => 'required',
            'model' => 'required',
            'warna' => 'required',
            'no_plat' => 'required'
        ];
        $messages   = [];
        $validator  = Validator::make($req->all(), $rules, $messages);

        if ( $validator->fails() ) :
            return redirect()->route('unit.edit', $req->id )->withErrors($validator)->withInput();
        else:
            $find       = ['/,/', '/Rp./'];
            $replace    = ['', ''];
            $hrg_limit  = preg_replace($find, $replace, $req->harga_limit);
            $rawPlat    = explode('-', $req->no_plat );
            $plat_kode  = ( isset($rawPlat[0]) ) ? Mhpix::remSpace($rawPlat[0]) : NULL;
            $plat_no    = ( isset($rawPlat[1]) ) ? Mhpix::remSpace($rawPlat[1]) : NULL;
            $plat_seri  = ( isset($rawPlat[2]) ) ? Mhpix::remSpace($rawPlat[2]) : NULL;
            
            $t_Unit     = t_Unit::find($req->id);
            // $t_Unit->jenis  = $req->jenis;
            $t_Unit->merk   = $req->merk;
            $t_Unit->model  = strtoupper($req->model);
            $t_Unit->warna  = strtoupper($req->warna);
            $t_Unit->tahun  = $req->tahun;
            $t_Unit->stnk   = ( $req->stnk == 'on' ) ? 1: 0 ;
            $t_Unit->bpkb   = ( $req->bpkb  == 'on' ) ? 1: 0 ;
            $t_Unit->harga_limit= $hrg_limit;
            $t_Unit->no_rangka  = strtoupper($req->no_rangka);
            $t_Unit->no_mesin   = strtoupper($req->no_mesin);
            $t_Unit->plat_kode  = strtoupper($plat_kode);
            $t_Unit->plat_no    = $plat_no;
            $t_Unit->plat_seri  = strtoupper($plat_seri);
            $t_Unit->updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $t_Unit->updated_by = Auth::user()->username;

            // return $t_Unit;
            if($t_Unit->save()):
                return redirect()->route('unit')->with('succ',"Success edit Unit <b>".$req->nm_event."</b>");
            else:
                return redirect()->route('unit.edit', $req->id)->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
            endif;
        endif;
    }
    
}