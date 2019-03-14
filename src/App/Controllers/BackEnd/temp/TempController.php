<?php

namespace LYTO\App\Controllers\BackEnd\temp;
use Route;
use Lyto;
use LYTO\App\Model\t_Routes;
class TempController
{

    public function index()
    {
        return view('BackEnd.requires.temp')->with('tmp_routes',''.Lyto::getController().'');
    }

    public function edit($id)
    {
        return $id;
    }

    public function BREAD($array,$routes){
      /* ====== CHECKING B.R.E.A.D =========*/
      foreach($array as $BREAD){
        if($BREAD == 0)continue;
        $index = 'index';
        switch($BREAD){
          case 1: $route = 'index'; break;
          case 2: $route = 'read';  break;
          case 3: $route = 'add';   break;
          case 4: $route = 'edit';  break;
          case 5:
            $route  = 'index';
            $index  = 'destroy';
          break;
        }

        if($BREAD == 3 || $BREAD == 4){

          echo '<br /> GET '.$routes.' '.$route.'&'.$index.'';
          echo '<br /> POST '.$routes.' '.$route.'&'.$index.'';

        }elseif($BREAD == 5){

          echo '<br /> DESTROY '.$routes.' '.$route.'&'.$index.'';

        }else{

          echo '<br /> GET '.$routes.' '.$route.'&'.$index.'';

        }

      }
      /* ====== CHECKING B.R.E.A.D =========*/
    }

    public function test()
    {
      echo '<p>'.Lyto::currentRoute().'</p>';
      $Menu = t_Routes::with('children')
              ->where('tampil',1)
              ->where('id_parent',0)
              ->get();

      foreach($Menu as $Route){
        $arr = explode(',',$Route->bread);
          /* ===== ROUTING ===================*/
          if($Route->children->count() > 0){
            /* ====== CHECKING B.R.E.A.D =========*/
            $this->BREAD($arr,$Route->nm_route);
            // foreach($arr as $BREAD){
            //   if($BREAD == 0)continue;
            //   $index = 'index';
            //   switch($BREAD){
            //     case 1: $route = 'index'; break;
            //     case 2: $route = 'read';  break;
            //     case 3: $route = 'add';   break;
            //     case 4: $route = 'edit';  break;
            //     case 5:
            //       $route  = 'index';
            //       $index  = 'destroy';
            //     break;
            //   }
            //   echo '<br />'.Lyto::remSpace($Route->nm_route).' '.$route.'&'.$index.'';
            // }
            /* ====== CHECKING B.R.E.A.D =========*/

            /* === PARENT ROUTING ============*/
            foreach($Route->children as $subRoute){
              $arr = explode(',',$subRoute->bread);
              $this->BREAD($arr,$subRoute->nm_route);
              // foreach($arr as $BREAD){
              //   if($BREAD == 0)continue;
              //   $index = 'index';
              //   switch($BREAD){
              //     case 1: $route = 'index'; break;
              //     case 2: $route = 'read';  break;
              //     case 3: $route = 'add';   break;
              //     case 4: $route = 'edit';  break;
              //     case 5:
              //       $route = 'index';
              //       $index= 'destroy';
              //     break;
              //   }
              //   echo '<br />'.Lyto::remSpace($subRoute->nm_route).' '.$route.'&'.$index.'';
              // }
             }
            /* === PARENT ROUTING ============*/

            echo '<br />';
            //Route::get($Route->alias_route,''.Lyto::remSpace($Route->nm_route).'\\'.$route.Lyto::remSpace($Route->nm_route).'Controller@'.$index.'')->name($Route->alias_route);
          }else{
            $arr = explode(',',$Route->bread);
            $this->BREAD($arr,$Route->nm_route);
            // foreach($arr as $BREAD){
            //   if($BREAD == 0)continue;
            //   $index = 'index';
            //   switch($BREAD){
            //     case 1: $route = 'index'; break;
            //     case 2: $route = 'read';  break;
            //     case 3: $route = 'add';   break;
            //     case 4: $route = 'edit';  break;
            //     case 5:
            //       $route = 'index';
            //       $index= 'destroy';
            //     break;
            //   }
            //   echo '<br />'.Lyto::remSpace($Route->nm_route).' '.$route.'&'.$index.'<br />';
            // }
              //Route::get($Route->alias_route.'',''.Lyto::remSpace($Route->nm_route).'\\'.$route.Lyto::remSpace($Route->nm_route).'Controller@'.$index.'')->name($Route->alias_route);
          }
          //echo '<Br  />Else '.$route.'';
          //Route::get($Route->alias_route.'',''.Lyto::remSpace($Route->nm_route).'\\'.$route.Lyto::remSpace($Route->nm_route).'Controller@'.$index.'')->name($arr2);
          /* ===== ROUTING ===================*/
      }

    }
}
