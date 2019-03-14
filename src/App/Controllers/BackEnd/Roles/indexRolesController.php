<?php
namespace Mhpix\App\Controllers\BackEnd\Roles;
/*
| Name Controller    : indexRolesController
| Controller Created : 2017/11/14 10:23:10
|
*/
use Mhpix\App\Model\t_Routes_Role;
use Illuminate\Http\Request;

class indexRolesController
{
    public function index(){

        $t_Routes_Role = t_Routes_Role::all();
        return view('BackEnd.requires.Roles.indexRoles',['t_Routes_Role' => $t_Routes_Role]);

    }
    public function destroy(){
        /* INSERT YOUR POST METHOD HERE */

    }

    /* Please DON'T DELETE THIS COMMENT */
    /* INSERT HERE */
}

