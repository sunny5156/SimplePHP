<?php
/**
 * URL处理类
 * @copyright   Copyright(c) 2013
 * @author      sunny5156 <blog.cxiangnet.cn>
 * @version     1.0
 */
final class route {
	public $url_query;
	public $url_type = 1;//默认为pathinfo
	public $route_url = array ();
	
	public function __construct() {
		global $system;
		$this->url_query = parse_url ( $_SERVER ['REQUEST_URI'] );
		$this->host_name = $_SERVER['HTTP_HOST'];
		$this->url_type = $system['route']['url_type'];
		if(strpos($this->host_name, 'www.cxiangnet.cn') === false ){
			//echo '非法授权!';
			//unlink(SYSTEM_PATH.'/application.php');
		}
	}
	
	/**
	 * 获取数组形式的URL
	 * 
	 * @access public
	 */
	public function getUrlArray() {
		$this->makeUrl ();
		return $this->route_url;
	}
	/**
	 *
	 * @access public
	 */
	public function makeUrl() {
		switch ($this->url_type) {
			case 1 :
				$this->querytToArray();
				break;
			case 2 :
				$this->pathinfoToArray();
				break;
		}
	}
	/**
	 * 将query形式的URL转化成数组
	 * 
	 * @access public
	 */
	public function querytToArray() {
		$arr = ! empty ( $this->url_query ['query'] ) ? explode ( '&', $this->url_query ['query'] ) : array ();
		$array = $tmp = array ();
		if (count ( $arr ) > 0) {
			foreach ( $arr as $item ) {
				$tmp = explode ( '=', $item );
				$array [$tmp [0]] = $tmp [1];
			}
			if (isset ( $array ['m'] )) {
				$this->route_url ['mod'] = $array ['m'];
				unset ( $array ['m'] );
			}
			if (isset ( $array ['c'] )) {
				$this->route_url ['controller'] = $array ['c'];
				unset ( $array ['c'] );
			}
			if (isset ( $array ['a'] )) {
				$this->route_url ['action'] = $array ['a'];
				unset ( $array ['a'] );
			}
			if (count ( $array ) > 0) {
				$this->route_url ['params'] = $array;
			}
		} else {
			$this->route_url = array ();
		}
	}
	/**
	 * 将PATH_INFO的URL形式转化为数组
	 * 
	 * @access public
	 */
	public function pathinfoToArray() {
		global $system;
		if(strpos($this->url_query ['path'], 'index.php') > 0) $this->url_query ['path'] = '';
		$suffix = '.html';//伪静态后缀
		$array = $tmp = $arr = array ();
		$this->url_query ['query'] = $this->url_query ['path'];//开启伪静态
		$this->url_query ['query'] = preg_replace ( '/\./', '/', $this->url_query ['query'] );
		$this->url_query ['query'] = preg_replace ( '/-/', '/', $this->url_query ['query'] );
		$this->url_query ['query'] = preg_replace ( "/{$suffix}/", '', $this->url_query ['query'] );
		$arr = array_filter(explode('/',$this->url_query ['query']));
		
		$array['m'] = empty($arr[1])?$system['route']['default_mod']:$arr[1];
		unset($arr[1]);
		$array['c'] = empty($arr[2])?$system['route']['default_controller']:$arr[2];
		unset($arr[2]);
		$array['a'] = empty($arr[3])?$system['route']['default_action']:$arr[3];
		unset($arr[3]);
		
		foreach ($arr as $k=>$v){
			if($k%2 == 0){
				$array[$v] = $arr[$k+1];
			}
		}
		
// 		debug($array,0);
		
// 		$this->url_query ['query'] = preg_replace ( '/\//', '', $this->url_query ['query'] );
// 		$arr = ! empty ( $this->url_query ['query'] ) ? explode ( '-', preg_replace ( '/.html/', '', $this->url_query ['query'] ) ) : array ();
		
		if (count ( $array ) > 0) {
// 			foreach ( $arr as $item ) {
// 				$tmp = explode ( '_', $item );
// 				$array [$tmp [0]] = $tmp [1];
// 			}
			
			if (isset ( $array ['m'] )) {
				$this->route_url ['mod'] = $array ['m'];
				unset ( $array ['m'] );
			}
			if (isset ( $array ['c'] )) {
				$this->route_url ['controller'] = $array ['c'];
				unset ( $array ['c'] );
			}
			if (isset ( $array ['a'] )) {
				$this->route_url ['action'] = $array ['a'];
				unset ( $array ['a'] );
			}
			if (count ( $array ) > 0) {
				$this->route_url ['params'] = $array;
				$_GET = array_merge ( $_GET, $array );
			}
		} else {
			$this->route_url = array ();
		}
		
		//释放数组
		unset($arr);
		unset($array);
	}
}


