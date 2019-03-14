<?php
namespace Mhpix\App\Controllers\BackEnd\Events;
/*
| Name Controller    : indexEventsController
| Controller Created : 2018/11/17 23:28:16
|
*/
use Auth;
use Illuminate\Http\Request;

use Mhpix\App\Model\t_Event;

class indexEventsController
{
    public function index(){
        
        if( Auth::user()->role_id == 100 ):
            $t_Event    = t_Event::where('active', 1)->orderBy('created_at', 'DESC')->get();
            return view('BackEnd.requires.Events.CustEvents', [ 't_Event' => $t_Event]);
        else:
            $t_Event    = t_Event::orderBy('created_at', 'DESC')->get();
            return view('BackEnd.requires.Events.indexEvents', [ 't_Event' => $t_Event]);
        endif;
        // return $t_Event;
    }

    public function destroy(Request $req){
        $t_Event    = t_Event::find($req->id);
        if($req->id){
            if( !$t_Event ){
                return redirect()->route('events')->with('err','Event not Found');
            }else{
                if( t_Event::whereId($req->id)->delete() ){
                    return redirect()->route('events')->with('succ','Success Delete Event');
                }else{
                    return redirect()->route('events')->with('err','Something Wrong');
                }
            }
        }else{
            return redirect()->route('events')->with('err','Parameter ID not Found');
        }
    }
    
}