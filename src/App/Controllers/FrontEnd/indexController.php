<?php

namespace Mhpix\App\Controllers\FrontEnd;

use Hash;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Unit;
use Mhpix\App\Model\t_Admin;
use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Customer;
use Mhpix\App\Model\t_Event_Register;

class indexController
{
    protected $t_Event;

    public function __contruct(t_Event $Event){
        $this->middleware('web',['except' => ['pma']]);
        $this->t_Event    = $Event;
    }

    public function index(){
        $t_Event    = t_Event::count();
        $t_Unit     = t_Unit::where('status', 1)->count();
        $t_Event_Register   = t_Event_Register::all();
        $Event_Active       = t_Event::with([
                                    'lot' => function($query){
                                        return $query->with('unit')->limit(5);
                                    }
                                ])
                                ->where('active', 1)
                                ->orderBy('created_at', 'DESC')->first();
        // return $Event_Active;
        return view('FrontEnd.requires.index', [
            'Event_Register' => $t_Event_Register->count(),
            'Event' => $t_Event,
            'Unit' => $t_Unit,
            'Event_Active' => $Event_Active
        ]);
    }
    public function register(){
        return view('FrontEnd.requires.register');
    }
    public function registerPost(Request $req){
        $rules      = [
            'nama' => 'required',
            'username' => 'required|unique:t_admin',
            'no_identitas' => 'required|unique:t_customer',
            'email' => 'required|unique:t_admin',
            'password' => 'required',
            'repassword' => 'required|min:5|same:password',

        ];
        $messages   = [
            'email.unique' => 'Email sudah terdaftar',
            'username.unique' => 'Username sudah terdaftar',
            'no_identitas.unique' => 'No. KTP sudah terdaftar',
            'repassword.same' => '<b>Konfirmasi Password</b> harus sama dengan <b>Password</b>'
        ];
        $validator  = Validator::make($req->all(), $rules, $messages);

        if ( $validator->fails() ):
            return redirect()->route('register')->withErrors($validator)->withInput();
        else:
            $find       = ['/,/', '/Rp./'];
            $replace    = ['', ''];
            $no_mobile  = preg_replace( '/[^0-9]/', '', $req->mobile );
            $no_telepon = preg_replace( '/[^0-9]/', '', $req->no_tlp );

            $t_Cust = new t_Customer;
            $t_Cust->nama   = $req->nama;
            $t_Cust->agama  = $req->agama;
            $t_Cust->email  = $req->email;
            $t_Cust->alamat = $req->alamat;
            $t_Cust->no_telepon = $no_telepon;
            $t_Cust->no_mobile  = $no_mobile;
            $t_Cust->provinsi   = $req->provinsi;
            $t_Cust->kota       = $req->kota;
            $t_Cust->kecamatan  = $req->kecamatan;
            $t_Cust->kelurahan  = $req->kelurahan;
            $t_Cust->created_at = Carbon::now('Asia/Jakarta');
            $t_Cust->updated_at = NULL;
            $t_Cust->created_by = 'register';
            $t_Cust->tanggal_lahir  = Carbon::parse($req->tgl_lahir)->format('Y-m-d');
            $t_Cust->no_identitas   = $req->no_identitas;
            // return $t_Cust;
            /* ====== INSERT NEW USER ====== */
            $t_User = new t_Admin;
            $t_User->name   = $req->nama;
            $t_User->email  = $req->email;
            $t_User->username   = $req->username;
            $t_User->password   = Hash::make($req->password);
            $t_User->role_id    = 100;
            $t_User->active     = 1;
            $t_User->created_at = Carbon::now('Asia/Jakarta');
            $t_User->updated_at = NULL;
            $t_User->remember_token = NULL;

            // return $req->all();
            /* ====== INSERT NEW USER ====== */
            if( $t_User->save() ):
                $newUser    = t_Admin::where('username', $req->username)->first();

                $t_Cust->user_id    = $newUser->id; 
                if( $t_Cust->save() ):
                    return redirect()->route('register')->with('succ', '<p>Hallo <strong>'.$req->nama.'</strong></p><label>Terima kasih sudah mendaftar</label>');
                else:
                    t_Admin::where('username', $req->id)->delete();
                    return redirect()->route('register')->with('err', 'Gagal mendaftar')->withInput();
                endif;

            else:
                return redirect()->route('register')->with('err','Gagal mendaftar')->withInput();  // Jika gagal menyimpan data baru
            endif;
            
        endif;
    }
}
