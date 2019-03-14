<?php

namespace LYTO\App\Controllers\FrontEnd;

use Illuminate\Http\Request;
use LYTO\App\Model\t_News;

class newsController
{

    public function index(Request $req){
        $get    = $req->get('q');
        if(isset($get)):
            if($get == null):
                return redirect()->route('lytonet.news');
            else:
                $q  = str_replace(' ','+',$req->q);
                return redirect()->route('lytonet.news.search',$q);
            endif;
        else:
            $t_News = t_News::where('active',1)->whereRaw('publish_at < NOW()')->paginate(8);
            return view('FrontEnd.requires.news', ['t_News' => $t_News] );
        endif;

    }

    public function indexSearch(){
         return redirect()->route('lytonet.news');
    }
    public function search($query){
        if(isset($query)):
            $string = str_replace('+','%',$query);
            $par_q  = str_replace('+',' ',$query);
            $t_News = t_News::where('active',1)->where('name','like','%'.$string.'%')->whereRaw('publish_at < NOW()')->orderBy('created_at','desc')->paginate(10);
            // return $t_News;
            return view('FrontEnd.requires.news', ['t_News' => $t_News, 'query' => $par_q]);
        else:
            $t_News = t_News::where('active',1)->whereRaw('publish_at < NOW()')->paginate(8);
            return view('FrontEnd.requires.news', ['t_News' => $t_News, 'query' => NULL] );
        endif;
    }

    public function tahun($tahun){
        $t_News = t_News::where('active',1)->whereRaw('publish_at < NOW()')->whereYear('created_at', $tahun)
                ->paginate(8);
        return view('FrontEnd.requires.news', ['t_News' => $t_News ]);
    }

    public function bulan($tahun, $bulan){
        $t_News = t_News::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->where('active',1)
                ->whereRaw('publish_at < NOW()')
                ->paginate(8);
        return view('FrontEnd.requires.news', ['t_News' => $t_News ]);
    }
    public function detail($tahun, $bulan, $alias){
        $t_News = t_News::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->where('active',1)
                ->whereRaw('publish_at < NOW()')
                ->where('alias', $alias)
                ->first();
        return view('FrontEnd.requires.news-detail', ['t_News' => $t_News ]);
    }
}
