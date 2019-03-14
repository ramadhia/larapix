<?php
namespace Mhpix\Functions;

use Route;
use Request;
use Auth;
use DB;
use Mhpix\App\Model\t_Routes;
use Mhpix\App\Model\t_Routes_Role;
use Mhpix\Functions\Mhpix;

class MhpixFE extends Mhpix{
  
  public static function MainNav(){

    $Prefix     = Request::route()->getPrefix().'/';
    $Active     = ( ('/'.parent::getURI().'/') == $Prefix ) ? 'class="active"' : '';
    $nav        = '<li><a href="'.url($Prefix).'" '.$Active.'><i class="icon-home3"></i><span>Dashboard</span></a></li>';
    $Prefix   = Request::route()->getPrefix().'/';
    $role_id  = Auth::user()->role_id;
    $role     = t_Routes_Role::where('role_id',$role_id)->first();
    $role_id  = explode(',',$role->routes_id);
    $menu     = t_Routes::with('children')
                ->where('id_parent',0)
                ->where('type',1)
                ->where('tampil',1)
                ->whereIn('id', $role_id )
                ->orderBy('sort', 'asc')
                ->get();
      foreach($menu as $Routes){
          $urlMenu  = $Prefix.''.$Routes->alias_route;
          $Active   = ( Mhpix::Routes() == $Routes->alias_route ) ? 'class="active"' : '';
          if($Routes->children->count() > 0):
            $nav .= "\r".'<li>';
            $nav .= '<a href="#'.$Routes->nm_route.'" data-toggle="collapse" class="collapsed" '.$Active.'>';
            $nav .= '<i class="'.$Routes->icon.'"></i><span>'.$Routes->nm_route.'</span><i class="icon-submenu lnr lnr-chevron-left"></i>';
            // $nav .= '<span>'.$Routes->nm_route.'</span>';
            // $nav .= '<i class="icon-submenu lnr lnr-chevron-left"></i>';
            $nav .= '</a>';
            $nav .= '<div id="'.$Routes->nm_route.'" class="collapse">';
            $nav .= '<ul class="nav">';
            $nav .= '<li><a href="'.url($urlMenu).'" '.$Active.'>';
            $nav .= '<i class="'.$Routes->icon.'"></i>List '.$Routes->nm_route.'</a></li>';
            foreach($Routes->children as $subRoutes){
              // Check jika sub menu tidak terdaftar di Role ID
              if(in_array($subRoutes->id,$role_id) && ($subRoutes->tampil) == 1){ 
                $urlMenuParent = $Prefix.$Routes->alias_route.'/'.$subRoutes->alias_route;
                $subActive     = ( Mhpix::Routes() == $subRoutes->alias_route ) ? 'class="active"' : '';
                $nav .='<li><a href="'.url($urlMenuParent).'" '.$subActive.'><i class="'.$subRoutes->icon.'"></i>'.$subRoutes->nm_route.'</a></li>';
              }
            }
            
            $nav .= '</ul>';
            $nav .= '</div>';
            $nav .= '</li>';
           else :
              $nav .= "\r\t\t\t".'<li>
                <a '.$Active.' href="'.url($urlMenu).'">
                  <i class="'.$Routes->icon.'"></i>
                  <span>'.$Routes->nm_route.'</span>
                </a>
              </li>';
          endif;
        }
      $html = str_replace("><", ">\r\n<", $nav);
      return ($nav);
  }

}
