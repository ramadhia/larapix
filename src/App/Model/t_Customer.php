<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Customer extends Model
{
	protected $table 		= 't_customer';
	// protected $fillable     = ['id'];
    
    public function user()
    {
        return $this->hasOne('Mhpix\App\Model\t_Admin', 'id', 'user_id');
    }

}