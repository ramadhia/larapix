<?php
namespace Mhpix\App\Controllers\BackEnd\Pelanggan;
/*
| Name Controller    : readPelangganController
| Controller Created : 2018/11/17 22:40:51
|
*/
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Customer;
use Mhpix\App\Model\t_Event_Log;
use Mhpix\App\Model\t_Event_Register;

class readPelangganController
{   
    protected $Register;
    
    public function __construct(t_Event_Register $t_Register, Request $req){
        $this->Register = $t_Register->where('cust_id', $req->id);
        $this->requ = $req->id;
    }
    public function index($id){
        
        $t_Cust         = t_Customer::with('user')->whereId($id)->first();
        
        if( empty($t_Cust) ):
            abort(404);
        endif;

        $E_Tawaran      = t_Event_Log::where('cust_id',$id)->count();
        $Total_Register = $this->Register->count();
        $Total_Menang   = $this->Register->with('lot')->has('lot')->count();
        
        return view('BackEnd.requires.Pelanggan.readPelanggan', [
            't_Cust' => $t_Cust,
            'E_Tawaran' => $E_Tawaran,
            'Total_Register' => $Total_Register,
            'Total_Menang' => $Total_Menang,
        ]);
    }
    public function post(){
        /* INSERT YOUR POST METHOD HERE */

    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}