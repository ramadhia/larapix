<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Event_Lot extends Model
{
	protected $table 		= 't_event_lot';
	protected $fillable     = ['id', 'event_id', 'unit_id', 'status','register_id', 'harga_terbentuk', 'created_by', 'updated_by'];
    
    public function event()
    {
        return $this->hasOne('Mhpix\App\Model\t_Event', 'id','event_id');
    }

    public function unit()
    {
        return $this->hasOne('Mhpix\App\Model\t_Unit', 'id', 'unit_id');
    }
    public function register()
    {
        return $this->hasOne('Mhpix\App\Model\t_Event_Register', 'id', 'register_id');
    }
    public function transaksi()
    {
        return $this->hasOne('Mhpix\App\Model\t_Transaksi', 'id', 'transaksi_id');
    }
}