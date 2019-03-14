<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Event extends Model
{
	protected $table 		= 't_event';
	protected $fillable     = ['id', 'name', 'start_date', 'end_date', 'closing_date', 'active', 'created_by', 'updated_by'];
    // // public $timestampe      = false;

    /**
     * Pemenang Event 
     */
    public function winner(){
        return $this->hasOne('Mhpix\App\Model\t_Event_Register', 'id', 'pemenang');
    }

    /**
     * Log Event
     */
    public function log(){
        return $this->hasMany('Mhpix\App\Model\t_Event_Log', 'event_id')->orderBy('created_at','DESC');
    }

    /**
     * Log Event
     */
    public function lot(){
        return $this->hasMany('Mhpix\App\Model\t_Event_Lot', 'event_id')->orderBy('name','asc');
    }
    
    /**
     * Register Event
     */
    public function register(){
        return $this->hasMany('Mhpix\App\Model\t_Event_Register', 'event_id')->orderBy('created_at');
    }

    /**
     * Summary Event
     */
    public function summary(){
        return $this->hasMany('Mhpix\App\Model\t_Event_Summary', 'id');
    }

    /**
     * Summary Event
     */
    public function unit(){
        return $this->hasOne('Mhpix\App\Model\t_Unit', 'id', 'unit_id');
    }
}
