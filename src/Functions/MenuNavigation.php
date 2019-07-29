<?php
namespace Larapix\Functions;


use Auth;
use Request;

use Models\Routes;
use Models\RouteRole;
use Larapix\Functions\Larapix;

trait MenuNavigation{

    public static function mainMenu(){
        $userRole   = Auth::user()->role_id;
        $role       = RouteRole::select(['routes_id'])->where('role_id', $userRole)->first();
        $roleId     = explode(',',$role->routes_id);

        // if( session()->exists('mainMenu') ){
        //     $Menu   = session('mainMenu');
        // }else{
            $Menu       = Routes::with('children')->active()
                        ->where('parent_id',0)
                        ->where('type',1)
                        ->whereIn('id', $roleId )
                        ->orderBy('sort', 'asc')
                        ->get();

        //     session([ 'mainMenu' => $Menu ]);
        // }
        $prefix     = Request::route()->getPrefix().'/';
        $Active     = ( ('/'.Larapix::getURI().'/') == $prefix ) ? 'class="active"' : '';
        $currRoute  = Larapix::routeIsExists();

        $nav        = '<li><a href="'.url($prefix).'" '.$Active.'><i class="icon-home2"></i><span>Dashboard</span></a></li>';
        foreach( $Menu  as $Routes){

            $urlMenu  = $prefix.''.$Routes->alias_route;
            $Active   = ( $currRoute == $Routes->alias_route ) ? 'class="active"' : '';
            if($Routes->children->count() > 0):
                $nav .= "\r".'<li '.$Active.'>';
                $nav .= '<a href="#'.$Routes->nm_route.'">';
                $nav .= '<i class="'.$Routes->icon.'"></i><span>'.$Routes->nm_route.'</span>';
                // $nav .= '<span>'.$Routes->nm_route.'</span>';
                // $nav .= '<i class="icon-submenu lnr lnr-chevron-left"></i>';
                $nav .= '</a>';
                // $nav .= '<div id="'.$Routes->nm_route.'" class="collapse">';
                $nav .= '<ul>';
                // $nav .= '<li><a href="'.url($urlMenu).'" '.$Active.'>';
                // $nav .= '<i class="'.$Routes->icon.'"></i>List '.$Routes->nm_route.'</a></li>';
                foreach($Routes->children as $subRoutes){
                    // Check jika sub menu tidak terdaftar di Role ID
                    if(in_array($subRoutes->id, $roleId) && ($subRoutes->active) == 1):
                        $urlMenuParent = $prefix.$Routes->alias_route.'/'.$subRoutes->alias_route;
                        $subActive     = ( $currRoute == $subRoutes->alias_route ) ? 'class="active"' : '';
                        $nav .='<li '.$subActive.'><a href="'.url($urlMenuParent).'"><i class="'.$subRoutes->icon.'"></i>'.$subRoutes->nm_route.'</a></li>';
                    endif;

                }

                $nav .= '</ul>';
                $nav .= '';
                $nav .= '</li>';
            else :
                $nav .= "\r\t\t\t".'<li '.$Active.'>
                <a href="'.url($urlMenu).'">
                <i class="'.$Routes->icon.'"></i>
                <span>'.$Routes->nm_route.'</span>
                </a>
                </li>';
            endif;

        }
        $html   = str_replace("><", ">\r\n<", $nav);
        return $html;
    }

    public static function coreMenu(){

        $userRole   = Auth::user()->role_id;
        $role       = RouteRole::select(['routes_id'])->where('role_id', $userRole)->first();
        $roleId     = explode(',',$role->routes_id);

        // if( session()->exists('coreMenu') ){
        //     $Menu   = session('coreMenu');
        // }else{
            $Menu       = Routes::with('children')->active()
                        ->where('parent_id',0)
                        ->where('type', 0)
                        ->whereIn('id', $roleId )
                        ->orderBy('sort', 'asc')
                        ->get();

            // session([ 'coreMenu' => $Menu ]);
        // }
        $prefix     = Request::route()->getPrefix().'/';
        $Active     = ( ('/'.Larapix::getURI().'/') == $prefix ) ? 'class="active"' : '';
        $currRoute  = Larapix::routeIsExists();

        $nav  = '';
        foreach( $Menu as $Routes ){
            $urlMenu  = $prefix.''.$Routes->alias_route;
            $Active   = ( $currRoute == $Routes->alias_route ) ? 'class="active"' : '';
            if($Routes->children->count() > 0):
                $nav .= "\r\t\t\t".'<li>
                <a href="'.url($urlMenu).'"><i class="'.$Routes->icon.'"></i>
                <span>'.$Routes->nm_route.'</span></a>
                <ul>';
                $nav .='<li '.$Active.'><a href="'.url($urlMenu).'">'.$Routes->nm_route.'</a></li>';
                foreach($Routes->children as $subRoutes){

                    if(in_array($subRoutes->id, $roleId) && ($subRoutes->active) == 1):
                        $urlMenuParent = $prefix.$Routes->alias_route.'/'.$subRoutes->alias_route;
                        $subActive     = ( $currRoute == $subRoutes->alias_route ) ? 'class="active"' : '';
                        $nav          .='<li><a href="'.url($urlMenuParent).'" '.$subActive.'><i class="'.$subRoutes->icon.'"></i>'.$subRoutes->nm_route.'</a></li>';
                    endif;

                }
                $nav .='</ul></li>';
            else :
                $nav .= "\r\t\t\t".'<li '.$Active.'>
                <a href="'.url($urlMenu).'">
                <i class="'.$Routes->icon.'"></i>
                <span>'.$Routes->nm_route.'</span>
                </a>
                </li>';
            endif;
          }

        $html   = str_replace("><", ">\r\n<", $nav);
        return $html;
    }

}
