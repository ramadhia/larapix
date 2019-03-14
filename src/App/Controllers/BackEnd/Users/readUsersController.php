<?php
namespace LYTO\App\Controllers\BackEnd\Users;
/*
| Name Controller    : readUsersController
| Controller Created : 2017/11/14 16:37:44
|
*/
use Illuminate\Http\Request;

class readUsersController
{
      public function index(){

          return view('BackEnd.requires.Users.readUsers');

      }
      public function post(){
        /* INSERT YOUR POST METHOD HERE */

      }

      /* Please DON'T DELETE THIS COMMENT */
      /* INSERT HERE */
      }