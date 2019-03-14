<?php
namespace LYTO\App\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use LYTO\App\Model\t_Document;
use LYTO\App\Model\t_Category;
use LYTO\App\Model\t_Divisi;
use Exception;
use Lyto;


class roleDivisiMiddleware
{
    public function handle($request, Closure $next)
    {
        if(Auth::check()){
            $Cur_Route  = Lyto::Routes();
            try{

                if($Cur_Route == 'document'){
                    $t_Document = t_Document::find($request->id);
                    if( in_array($t_Document->divisi, $this->getDivisi() ) ){
                        
                        if($t_Document->private == 1){
                            $Share_With  = explode(',', $t_Document->share_with);
                            if( in_array( Auth::user()->id , $Share_With ) || ( Auth::user()->username == $t_Document->created_by ) ){
                                return $next($request);
                            }
                            throw new \Exception;
                        }else{
                            return $next($request);
                        }
                    }
                }

                if($Cur_Route == 'category'){
                    $t_Document = t_Category::find($request->id);
                    if( in_array($t_Document->divisi, $this->getDivisi() ) ){

                        if($t_Document->private == 1){
                            $Share_With  = explode(',', $t_Document->share_with);
                            if( in_array( Auth::user()->id , $Share_With ) ){
                                return $next($request);
                            }
                            throw new \Exception;
                        }else{
                            return $next($request);
                        }
                        
                    }
                }
                
                throw new \Exception;
            }catch (\Exception  $e){
                return redirect()->route($Cur_Route)->with('error_role', 'You don\'t have Permission to Access');
            }

        }else{
            return redirect()->route('login')->with('error_role', 'You don\'t have Permission to Access 2');
        }

        // return $next($request);
    }

    private function getDivisi(){
        $t_Divisi   = t_Divisi::get();
        $role_id    = Auth::user()->role_id;
        // $role_id    = 3;
        if($role_id == 99){
            foreach($t_Divisi as $row){
                $getDivisi[] = $row->id;
            }

        }else{
            foreach($t_Divisi as $index => $divisi){
                if($divisi->role_id){
                    $arr_Role[$divisi->id] = explode(',', $divisi->role_id);
                }
            }
            foreach($arr_Role as $divisi => $role){
                if(in_array( $role_id, $role )){
                    $getDivisi[] = $divisi;
                }
            }
            
        }
        return $getDivisi;
    }
}
