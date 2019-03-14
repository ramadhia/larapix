<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Transaksi extends Model
{
	protected $table 		= 't_transaksi';
    protected $fillable     = ['id','cust_id','amount','jns_pembayaran','no_rek','type_transaksi','pembayaran_id','status','created_by','updated_by'];
    public $incrementing = false;

    public function customer()
    {
        return $this->hasOne('Mhpix\App\Model\t_Customer','id','cust_id');
    }
    public function pembayaran(){
        return $this->hasOne('Mhpix\App\Model\t_Pembayaran','id','pembayaran_id');
    }
}