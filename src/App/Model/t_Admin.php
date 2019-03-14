<?php

namespace Mhpix\App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class t_Admin extends Authenticatable
{
	protected $table	= 't_admin';
	protected $fillable = ['id', 'name', 'username', 'email', 'password', 'role_id','active'];

	public function role()
	{
		return $this->belongsTo(t_Roles::class);
	}

}
