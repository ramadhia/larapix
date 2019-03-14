<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Unit extends Model
{
	protected $table 		= 't_unit';
	protected $fillable     = [
        'type',
        'merk',
        'model',
        'no_rangka',
        'no_mesin',
        'warna',
        'plat_kode',
        'plat_no',
        'plat_seri',
        'tahun',
        'tnk',
        'bpkb',
        'surat_lain',
        'status',
        'created_by',
        'updated_by'
    ];
    public $timestamps      = false;
    


}