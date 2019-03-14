<?php
namespace Mhpix\App\Controllers\BackEnd\LOT;
/*
| Name Controller    : indexListLOTController
| Controller Created : 2018/12/17 23:43:40
|
*/
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;
use Mhpix\App\Model\t_Event_Lot;

class indexLOTController
{
    protected $Event_Lot;

    public function __construct(t_Event_Lot $Lot){
        $this->Event_Lot = $Lot;
    }
    public function index(Request $req){
        $t_Event_Lot    = $this->Event_Lot;
        $typeEvent      = ( $req->event != NULL ) ? $req->event : false;
        
        if( $typeEvent ):
            if($typeEvent == 'all'):
                $t_Event_Lot = $t_Event_Lot->with(['event','unit'])->orderBy('created_at', 'DESC')->get();
            else:
                $t_Event_Lot = $t_Event_Lot->where('event_id', $typeEvent)->orderBy('created_at', 'DESC')->get();
            endif;
        else:
            $t_Event_Lot = $t_Event_Lot->with('unit')->whereHas('event', function($query){
                    return $query->where('active', 1);
            })->orderBy('created_at', 'DESC')->get();
        endif;
        
        $t_Event        = t_Event::where('active', 1)->get();
        
        return view('BackEnd.requires.LOT.indexLOT', [
            't_Event_Lot' => $t_Event_Lot,
            't_Event' => $t_Event
            ]);
    }
    public function destroy(Request $req){
        $t_Event_Lot    = $this->Event_Lot->find($req->id);
        if($req->id){
            if( !$t_Event_Lot ){
                return redirect()->route('lot')->with('err','LOT tidak tersedia');
            }else{
                if( $this->Event_Lot->whereId($req->id)->delete() ){
                    return redirect()->route('lot')->with('succ','Success Delete LOT');
                }else{
                    return redirect()->route('lot')->with('err','Something Wrong');
                }
            }
        }else{
            return redirect()->route('lot')->with('err','Parameter ID not Found');
        }
    }
}