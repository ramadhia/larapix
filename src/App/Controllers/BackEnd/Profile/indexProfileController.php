<?php
namespace Mhpix\App\Controllers\BackEnd\Profile;
/*
| Name Controller    : indexProfileController
| Controller Created : 2019/01/16 15:26:15
|
*/
use Auth;
use Hash;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Mhpix\App\Model\t_Admin;
use Mhpix\App\Model\t_Customer;
use Mhpix\App\Model\t_Event_Log;
use Mhpix\App\Model\t_Event_Register;

class indexProfileController extends Controller
{
    protected $AuthID;
    protected $Register;
    public function __construct(t_Event_Register $t_Register, Request $req, Auth $Auth){
        $this->middleware(function ($request, $next) {
            $this->AuthID   = Auth::user()->id;
            $t_Cust         = t_Customer::with('user')->where('user_id', $this->AuthID )->first();
            $id             = $t_Cust->id;
            $this->Register = t_Event_Register::where('cust_id', $id);
            return $next($request);
        });
        
    }
    public function index(){
        $userID         = Auth::user()->id;
        $t_Cust         = t_Customer::with('user')->where('user_id',$userID)->first();
        $id             = $t_Cust->id;
        
        if( empty($t_Cust) ):
            abort(404);
        endif;

        $E_Tawaran      = t_Event_Log::where('cust_id',$id)->count();
        $Total_Register = $this->Register->count();
        $Total_Menang   = $this->Register->with('lot')->has('lot')->count();
        
        return view('BackEnd.requires.Profile.indexProfile', [
            't_Cust' => $t_Cust,
            'E_Tawaran' => $E_Tawaran,
            'Total_Register' => $Total_Register,
            'Total_Menang' => $Total_Menang,
        ]);
    }

    public function edit(Request $req){
        $Tgl_lahir  = Carbon::parse($req->tgl_lahir)->format('Y-m-d');

        $t_Cust         = t_Customer::where('user_id', $this->AuthID )->first();

        // return $t_Cust;
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
            $t_Admin->email     = $req->email;
            $t_Admin->updated_at= Carbon::now('Asia/Jakarta')->toDateTimeString();
            $t_Admin->save();
            return redirect()->route('profile')->with('succ', 'Success update Profile');
        else:
            return redirect()->route('profile')->with('err','Something Wrong !! Code: 121')->withInput();  // Jika gagal menyimpan data baru
        endif;
        // return $req->all();
    }
    public function password(Request $req){

        if(strlen($req->password) >= 1):
            $rules_pass = array(
                'password'   => 'required|min:5',
                'repassword' => 'required|min:5|same:password'
            );
            $messages   = [
                'min' => 'Password harus lebih dari 5 huruf',
                'same' => '<b>Konfirmasi Password</b> dan <b>Password</b> harus sama'
            ];
            $valid_pass = Validator::make($req->all(), $rules_pass, $messages);
            if ($valid_pass->fails()) :
                return redirect()->route('profile', $req->id)->withErrors($valid_pass);
            endif;
            $id     = Auth::user()->id;
            $t_Users            = t_Admin::find($id);
            $t_Users->password  = Hash::make($req->password);
            
            // return $t_Users;
            if($t_Users->save()):
                return redirect()->route('profile')->with('succ',"Success change Password".$req->name);
            else:
                return redirect()->route('profile')->with('err','Something Wrong !! Code: 121');  // Jika gagal menyimpan data baru
            endif;
        else:
            return redirect()->route('profile');
        endif;

        return $req->all();
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}