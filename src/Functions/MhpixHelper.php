<?php
namespace Mhpix\Functions;

use Mhpix\App\Model\t_Routes;
use Request;
use SoapClient;

trait MhpixHelper{

		public static function route($route){
			return route($route);
		}
		public static function path(){
			return base_path().'/vendor/mhpix';
		}

		public static function getURI(){
			return Request::route()->uri();
		}

		public static function Routes(){
			$arr		= explode('/',self::getURI());
			$URI		= NULL;
			if ( isset($arr[1]) ) :
				$ROOT		= $arr[1];
				$route	= t_Routes::where('alias_route', ''.$ROOT.'')->first();
				$parentR= t_Routes::where('id_parent', $route->id)->count();
				if ($parentR >= 1):
					if ( isset($arr[2]) ):
						$PARENT = $arr[2];
						$check	= t_Routes::where('alias_route', ''.$PARENT.'')->first();
						$URI		= ($check) ? $PARENT : $ROOT;
					else:
						$URI = $ROOT;
					endif;
				else:
					$URI = $ROOT;
				endif;
			endif;
			return $URI;
		}

		public static function currentRoute(){
			$route =  explode('/',self::getURI());
			return end($route);
		}

		public static function JSON(){
			$data	= json_encode(["username" => "asdadsa", "_token" => csrf_token()]);
			$url	= route('json');
			$ch		= curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($data)
			));
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			//execute post
			$result = curl_exec($ch);
			//close connection
			curl_close($ch);
			return json_decode($result);
		}

		public static function WSDL(){
			return new SoapClient("http://10.10.104.106:140/Gravindofuncacc.asmx?wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
		}
		public static function WSDL_SHOP(){
			return new SoapClient("http://192.168.254.21:2480/funcLytoIDShop.asmx?wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
		}
		public static function Get_GP($parUsn){
			// $WSDL	= new SoapClient("http://192.168.254.21:2480/funcLytoIDShop.asmx?wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
			$result	= self::WSDL_SHOP()->Get_GP(array('parUserid'=>$parUsn));
     		return  $result->Get_GPResult;
		}
		public static function Use_GP($parUsn,$parAmount,$parEventtype){
			// ' IDOL = 5
			// ' RF Online = 6
			// ' PW = 7
			// ' Rohan = 8
			// ' RFC = 9
			// ' RO Classic = 10
			// ' ROR Valhalla = 11
			// ' CF = 12
			// ' ROR Asgard = 13
			// ' ROR Midgrad = 14
			$result	= self::WSDL_SHOP()->Use_GP(array('parUserid' =>$parUsn,'parAmount' => (int) $parAmount,'parEventtype' => (int)$parEventtype ));
     		return $result->Use_GPResult;
			// return $parUsn.'-'.$parAmount.'-'.'-'.$parEventtype;
		}
		public static function Routes2(){
			$r = session(['username' => Auth::user()->role_id]);
			return $r;
		}

		public static function remSpace($val){
			$str = preg_replace('/\s+/', '', $val);
			$str = str_replace('-', '', $str);
			return $str;
		}

		public static function set($a, $b, $c, $d) {
			if (strcasecmp($a, $b)==0):
				return $c;
			else :
				return $d;
			endif;
		}

		public static function isOurServer(){
		    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
		    	$clientIpAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    } else {
		    	$clientIpAddress = $_SERVER['REMOTE_ADDR'];
		    }
			$ourIP	= array('202.43.161.114','202.93.26.134', '36.88.58.123','::1','192.168.2.81','127.0.0.1','192.168.2.89');
			if (in_array($clientIpAddress, $ourIP)) :
			    return TRUE;
			else:
				return true;
			endif;
		}

		public static function fileManager($field_id){
			$akey	= '2444282ef4344e3dacdedc7a78f8877d';
			return url('plugin/filemanager/dialog.php?type=1&amp;akey='.$akey.'&amp;field_id='.$field_id);
		}
}
