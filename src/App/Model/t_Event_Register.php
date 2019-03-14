<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Event_Register extends Model
{
	protected $table 		= 't_event_register';
	protected $fillable     = ['id', 'event_id', 'cust_id', 'nipl', 'deposit', 'tarikan', 'sisa_deposit', 'created_at'];
	public $timestamps   = false;
	
	public function customer()
    {
        return $this->hasOne('Mhpix\App\Model\t_Customer','id','cust_id');
	}
	
	public function event()
    {
        return $this->hasOne('Mhpix\App\Model\t_Event','id','event_id');
	}

	public function lot()
	{
		return $this->hasOne('Mhpix\App\Model\t_Event_Lot', 'register_id','id')->where('status', 1);
	}
	public function transaksi(){
		return $this->hasOne('Mhpix\App\Model\t_Transaksi', 'id','transaksi_id');
	}
}