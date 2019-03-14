<?php

namespace Mhpix\App\Model;

use Illuminate\Database\Eloquent\Model;

class t_Pembayaran extends Model
{
	protected $table 		= 't_pembayaran';
    protected $fillable     = ['id','nama','rekening','bank','transfer_rek','transfer_bank','jumlah','bukti','created_by','updated_by'];

}