<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Routes extends Model
{
	protected $table = 't_routes';
	protected $fillable = ['id', 'id_parent', 'nm_route', 'alias_route', 'shortcut', 'created_by', 'modified_by'];

    public function parent()
    {
        return $this->belongsTo('Mhpix\App\Model\t_Routes', 'id_parent');
    }

    public function children()
    {
        return $this->hasMany('Mhpix\App\Model\t_Routes', 'id_parent');
    }
}
