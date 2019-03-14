<?php

namespace Mhpix\App\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Mhpix\App\Model\t_Divisi;
use Mhpix\App\Model\t_Document;
use Mhpix\App\Model\t_Category;
use Mhpix;
use Auth;

class indexController extends Controller
{   
    public function index(){
        return view ('BackEnd.requires.index',['Dashboard' => []]);
    }
    public function create(Request $req){
        $dir = base_path().'/vendor/mhpix/App/Controllers/BackEnd/Events';
        $createFOlder = DIRECTORY_SEPARATOR.'EventSSummary';
        $Folder = $dir.$createFOlder;
        return $Folder;
        mkdir($folder, 755, true);
    }
}
