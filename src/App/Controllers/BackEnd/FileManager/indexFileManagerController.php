<?php
namespace Mhpix\App\Controllers\BackEnd\FileManager;
/*
| Name Controller    : indexFileManagerController
| Controller Created : 2017/10/24 15:57:36
|
*/
use Illuminate\Http\Request;
use Route;

class indexFileManagerController
{
    public function index(){
    //$str = include(public_path('plugin/filemanager/dialog.php'));
        return view('BackEnd.requires.FileManager.indexFileManager');
    }
    public function download($file){
        try {
            $dec_URL    = base64_decode($file);
            $URL        = rawurldecode($dec_URL);
            return response()->download(public_path($URL));
        }
        catch (\Exception $e) {
            $data = ['status' => 'error', 'message' => 'File Not Found'];
            return response()->json($data, 404);
        }
    }
      /* Please DONT DELETE THIS COMMENT */
      /* INSERT HERE */
}
