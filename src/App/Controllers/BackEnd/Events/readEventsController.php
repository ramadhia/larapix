<?php
namespace Mhpix\App\Controllers\BackEnd\Events;
/*
| Name Controller    : readEventsController
| Controller Created : 2018/11/17 23:28:16
|
*/
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Event_Log;


class readEventsController
{
    public function index($id){
        $t_Event    = t_Event::whereId($id)->with('log.customer', 'register.customer','register.transaksi','lot.unit', 'lot.register.customer')->first();   
        // return $t_Event;
        if( empty($t_Event) ):
            abort(404);
        endif;
        $Ttl_Reg    = count($t_Event->register);
        $tawaran    = collect( $t_Event->log )->max('tawaran');
        return view('BackEnd.requires.Events.readEvents', [
            't_Event' => $t_Event, 
            'Total_reg' => $Ttl_Reg,
            'tawaran' => $tawaran
        ]);
    }

    public function post(){
    /* INSERT YOUR POST METHOD HERE */

    }
    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}