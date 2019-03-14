<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Event_Log extends Model
{
	protected $table 		= 't_event_log';
	protected $fillable     = ['id', 'event_id', 'cust_id', 'nipl', 'created_at'];
    public $timestamps   = false;

    public function event()
    {
        return $this->belongsTo('Mhpix\App\Model\t_Event', 'event_id');
    }

    public function customer()
    {
        return $this->hasOne('Mhpix\App\Model\t_Customer','id','cust_id');
    }

    public function register()
    {
        return $this->hasOne('Mhpix\App\Model\t_Event_Register','cust_id','cust_id');
    }

}