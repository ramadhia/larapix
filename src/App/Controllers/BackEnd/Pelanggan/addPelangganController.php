<?php
namespace Mhpix\App\Controllers\BackEnd\Pelanggan;
/*
| Name Controller    : addPelangganController
| Controller Created : 2018/11/17 22:40:51
|
*/
use Illuminate\Http\Request;

class addPelangganController
{
    public function index(){
        return view('BackEnd.requires.Pelanggan.addPelanggan');
    }
    public function post(Request $req){
        return $req->all();
    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}