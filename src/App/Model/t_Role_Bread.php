<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Role_Bread extends Model
{
	protected $primaryKey 	= 'role_id';
	protected $table 		= 't_role_bread';
	protected $fillable = ['id', 'role_id', 'routes_id', 'role_bread'];

}
