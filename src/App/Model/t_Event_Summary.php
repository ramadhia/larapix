<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Event_Summary extends Model
{
	protected $table 		= 't_event_summary';
	protected $fillable     = ['id', 'event_id', 'cust_id', 'nipl', 'id_unit', 'tarikan', 'sisa_deposit', 'created_at'];
    protected $timestamps   = false;
    
}