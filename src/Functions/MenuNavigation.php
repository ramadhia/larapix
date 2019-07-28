<?php
namespace Larapix\Functions;


use Auth;
use Request;

use Models\Routes;
use Models\RouteRole;
use Larapix\Functions\Larapix;

trait MenuNavigation{

    public static function mainMenu(){

        $prefix     = Request::route()->getPrefix().'/';
        $Active     = ( ('/'.Larapix::getURI().'/') == $prefix ) ? 'class="active"' : '';
        $userRole   = Auth::user()->role_id;
        $role       = RouteRole::where('role_id', $userRole)->first();
        $roleId     = explode(',',$role->routes_id);
        $menu       = Routes::with('children')->active()
                    ->where('parent_id',0)
                    ->where('type',1)
                    ->whereIn('id', $roleId )
                    ->orderBy('sort', 'asc')
                    ->get();

        $nav        = '<li><a href="'.url($prefix).'" '.$Active.'><i class="icon-home2"></i><span>Dashboard</span></a></li>';
        foreach($menu as $Routes){

            $urlMenu  = $prefix.''.$Routes->alias_route;
            $Active   = ( Larapix::routeIsExists() == $Routes->alias_route ) ? 'class="active"' : '';
            if($Routes->children->count() > 0):
                $nav .= "\r".'<li '.$Active.'>';
                $nav .= '<a href="#'.$Routes->nm_route.'">';
                $nav .= '<i class="'.$Routes->icon.'"></i><span>'.$Routes->nm_route.'</span>';
                // $nav .= '<span>'.$Routes->nm_route.'</span>';
                // $nav .= '<i class="icon-submenu lnr lnr-chevron-left"></i>';
                $nav .= '</a>';
                $nav .= '<ul>';
                // $nav .= '<li><a href="'.url($urlMenu).'" '.$Active.'>';
                // $nav .= '<i class="'.$Routes->icon.'"></i>List '.$Routes->nm_route.'</a></li>';
                foreach($Routes->children as $subRoutes){
                    // Check jika sub menu tidak terdaftar di Role ID
                    if(in_array($subRoutes->id, $roleId) && ($subRoutes->active) == 1):
                        $urlMenuParent = $prefix.$Routes->alias_route.'/'.$subRoutes->alias_route;
                        $subActive     = ( Larapix::routeIsExists() == $subRoutes->alias_route ) ? 'class="active"' : '';
                        $nav .='<li '.$subActive.'><a href="'.url($urlMenuParent).'"><i class="'.$subRoutes->icon.'"></i>'.$subRoutes->nm_route.'</a></li>';
                    endif;

                }

                $nav .= '</ul>';
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

        $prefix     = Request::route()->getPrefix().'/';
        $userRole   = Auth::user()->role_id;
        $role       = RouteRole::where('role_id', $userRole)->first();
        $roleId     = explode(',',$role->routes_id);
        $menu       = Routes::with('children')->active()
                      ->where('parent_id',0)
                      ->where('type',0)
                      ->whereIn('id', $roleId )
                      ->get();

        $nav  = '';
        foreach($menu as $Routes){
            $urlMenu  = $prefix.''.$Routes->alias_route;
            $Active   = ( Larapix::routeIsExists() == $Routes->alias_route ) ? 'class="active"' : '';
            if($Routes->children->count() > 0):
                $nav .= "\r\t\t\t".'<li>
                <a href="'.url($urlMenu).'"><i class="'.$Routes->icon.'"></i>
                <span>'.$Routes->nm_route.'</span></a>
                <ul>';
                $nav .='<li '.$Active.'><a href="'.url($urlMenu).'">'.$Routes->nm_route.'</a></li>';
                foreach($Routes->children as $subRoutes){

                    if(in_array($subRoutes->id, $roleId) && ($subRoutes->active) == 1):
                        $urlMenuParent = $prefix.$Routes->alias_route.'/'.$subRoutes->alias_route;
                        $subActive     = ( Larapix::routeIsExists() == $subRoutes->alias_route ) ? 'class="active"' : '';
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
