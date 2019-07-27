<?php
namespace Larapix\Functions;

use Route;
use Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

use Models\Routes;

trait LarapixRoute{

    public static $routes = NULL;
    /**
     * Begin querying a Routes from the database.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function routes(){
        self::$routes   = Routes::with('children')->active()->where('parent_id', 0);
        return new self;
    }


    public function all(){
        $routes = self::$routes;
        if($routes instanceof Builder ){
            $this->generator($routes->get());
        }
    }

    public function get(){
        return $this->data->get();
    }

    public function generator($routes){

        /* ========= LOOPING ==================*/
        foreach($routes as $Route){
            /* ===== ROUTING ===================*/
            if($Route->children->count() > 0){
                self::BREAD(
                    $Route->alias_route,
                    Str::studly($Route->nm_route),
                    Str::studly($Route->nm_route),
                    $Route->bread,
                    $Route->parent_id
                );
                /* === PARENT ROUTING ============*/
                foreach($Route->children as $subRoute){
                    self::BREAD(
                        $subRoute->alias_route,
                        Str::studly($Route->nm_route).'\\'.Str::studly($subRoute->nm_route),
                        Str::studly($subRoute->nm_route),
                        $subRoute->bread,
                        $subRoute->parent_id
                    );
                }
                /* === PARENT ROUTING ============*/
            }else{
                self::BREAD(
                    $Route->alias_route,
                    Str::studly($Route->nm_route),
                    Str::studly($Route->nm_route),
                    $Route->bread,
                    $Route->parent_id
                );
            }
            /* ===== ROUTING ===================*/
        }
        /* ========= LOOPING ==================*/
    }

    /**
     * BREAD For BackEnd routes
     */
    public static function BREAD($nm_alias, $dirController ,$nm_Route, $arr, $parent){
        $array  = explode(',', $arr);
        $ROUTE = $nm_alias;
        /*========== CHECK IF ROUTE HAVE A CHILD ===================*/
        $t_Routes  = Routes::where('id', $parent)->first();
        if($t_Routes){
            $ROUTE    = $t_Routes['alias_route'].'/'.$nm_alias;
        }else{
            $ROUTE    = '/'.$nm_alias;
        }
        /*========== CHECK IF ROUTE HAVE A CHILD ===================*/
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
            $nameController = $route.$nm_Route;
            $nameAlias      = $route.'-'.$nm_alias;
            if($BREAD == 3){
                /* ========== BREAD ADD =====================*/
                Route::get($ROUTE.'/'.$nameAlias, ''.$dirController.'\\'.$nameController.'Controller@'.$index.'')->name($nm_alias.'.'.$route);
                Route::post($ROUTE.'/'.$nameAlias, ''.$dirController.'\\'.$nameController.'Controller@post')->name($nm_alias.'.'.$route.'.post');
                /* ========== BREAD ADD =====================*/
            }elseif($BREAD == 4){
                /* ========== BREAD EDIT =====================*/
                Route::get($ROUTE.'/'.$nameAlias.'/{id}', ''.$dirController.'\\'.$nameController.'Controller@'.$index.'')->name($nm_alias.'.'.$route);
                Route::post($ROUTE.'/'.$nameAlias.'', ''.$dirController.'\\'.$nameController.'Controller@post')->name($nm_alias.'.'.$route.'.post');
                /* ========== BREAD EDIT =====================*/
            }elseif($BREAD == 5){
                /* ========== BREAD DESTROY / DELETE =====================*/
                Route::get($ROUTE.'/'.$index.'-'.$nm_alias, ''.$dirController.'\\'.$nameController.'Controller@'.$index.'')->name($nm_alias.'.'.$index);
                /* ========== BREAD DESTROY / DELETE =====================*/
            }elseif($BREAD == 1){
                /* ========== BREAD BROWSE =====================*/
                Route::get($ROUTE, ''.$dirController.'\\'.$nameController.'Controller@'.$index.'')->name($nm_alias);
                /* ========== BREAD BROWSE =====================*/
            }else{
                /* ========== BREAD READ =====================*/
                Route::get($ROUTE.'/'.$nameAlias.'/{id}', ''.$dirController.'\\'.$nameController.'Controller@'.$index.'')->name($nm_alias.'.'.$route);
                /* ========== BREAD READ =====================*/
          }

        }
        /* ====== CHECKING B.R.E.A.D =========*/
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

}
