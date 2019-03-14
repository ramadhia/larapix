<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;
use Mhpix\App\Model\t_Admin;

class t_Routes_Role extends Model
{
	protected $primaryKey 	= 'role_id';
	protected $table 		= 't_routes_role';
	protected $fillable = ['id', 'name', 'role_id', 'routes_id'];


}
