<?php
/**
 * 调试函数
 * @param array $arr
 * @param int $doexit
 */
function debug($arr, $doexit = 1) {
	echo '<pre>';
	//	var_dump($arr);
	print_r ( $arr );
	if ($doexit) {
		exit ( '<br/>Debug_ok</pre>' );
	}
	echo '<br/>Debug_ok</pre>';
}

/**
 * 实例化模型
 * @access      final   protected
 * @param       string  $model  模型名称
 */
function M($table) {
	if (empty($table)) {
		trigger_error('不能实例化空模型');
	}
// 	include_once ROOT_PATH .'/system/core/model.php';
	$model = new Model($table);
	if($model instanceof Model){
		return $model;
	}else{
		return null;
	}
	
}
/**
 * 实例化模型
 * @access      final   protected
 * @param       string  $model  模型名称
 */
function D($model) {
	if (empty($model)) {
		trigger_error('不能实例化空模型');
	}
	$model_name = $model . 'Model';
	include_once ROOT_PATH .'/model/'.$model_name.'.php';
	$model = new $model_name;
	if($model instanceof Model){
		return $model;
	}else{
		return null;
	}
	
}


/**
 * 加载类库
 * @param string $lib   类库名称
 * @param Bool  $my     如果FALSE默认加载系统自动加载的类库，如果为TRUE则加载自定义类库
 * @return type
 */
function load($lib,$my = FALSE){
	if(empty($lib)){
		trigger_error('加载类库名不能为空');
	}elseif($my === FALSE){
		return Application::$_lib[$lib];
	}elseif($my === TRUE){
		return  Application::newLib($lib);
	}
}

/**
 * 加载系统配置,默认为系统配置 $CONFIG['system'][$config]
 * @access      final   protected
 * @param       string  $config 配置名
 */
function config($config=''){
	return Application::$_config[$config];
}

/**
 * URL函数
 * @param array $arr
 * @return string 
 * ($str = "default/index/index",$params=array())
 */
function U($str = "default/index/index",$params=array()){
	$url = '';
	$conf = config('route');
	$arr['type'] = $conf['url_type'];
	if(!empty($str)){
		$urlArr = explode('/', $str);
		
		$arr['m'] = $urlArr[0];
		$arr['c'] = $urlArr[1];
		$arr['a'] = $urlArr[2];
		if(!empty($params)){
			$arr['params']= $params;
		}
		unset($urlArr);
		unset($params);
		
		switch ($arr['type']){
		
			case 1:
				$url .="./index.php?";
				if(isset($arr['m']) && !empty($arr['m'])){
					$url .="m={$arr['m']}";
				}
				if(isset($arr['c']) && !empty($arr['c'])){
					$url .="&c={$arr['c']}";
				}
				if(isset($arr['a']) && !empty($arr['a'])){
					$url .="&a={$arr['a']}";
				}
// 				$url .="./index.php?a={$arr['a']}&c={$arr['c']}&m={$arr['m']}";
				if(!empty($arr['params']) && is_array($arr['params'])){
					foreach ($arr['params'] as $k=>$v){
						$url .="&{$k}={$v}";
					}
				}
				break;
			case 2:
				$url .="/";
				if(isset($arr['m']) && !empty($arr['m'])){
					$url .="{$arr['m']}";
				}
				if(isset($arr['c']) && !empty($arr['c'])){
					$url .=".{$arr['c']}";
				}
				if(isset($arr['a']) && !empty($arr['a'])){
					$url .=".{$arr['a']}";
				}
					
				if(!empty($arr['params']) && is_array($arr['params'])){
					foreach ($arr['params'] as $k=>$v){
						$url .="-{$k}-{$v}";
					}
				}
				$url .= '.html';
				break;
		}
	}else{
		$url = './index.php';
	}
	
	return $url;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function getClientIp($type = 0) {
	$type       =  $type ? 1 : 0;
	static $ip  =   NULL;
	if ($ip !== NULL) return $ip[$type];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$pos    =   array_search('unknown',$arr);
		if(false !== $pos) unset($arr[$pos]);
		$ip     =   trim($arr[0]);
	}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip     =   $_SERVER['HTTP_CLIENT_IP'];
	}elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip     =   $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = ip2long($ip);
	$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}

/**
 * SESSION
 * @param string $name
 * @param $value
 */
function session($name,$value='') {
    $prefix   =  '';
    if(is_array($name)) { 
        ini_set('session.auto_start', 0);
        if(isset($name['name']))            session_name($name['name']);
        if(isset($name['path']))            session_save_path($name['path']);
        if(isset($name['domain']))          ini_set('session.cookie_domain', $name['domain']);
        if(isset($name['expire']))          ini_set('session.gc_maxlifetime', $name['expire']);
        if(isset($name['use_trans_sid']))   ini_set('session.use_trans_sid', $name['use_trans_sid']?1:0);
        if(isset($name['use_cookies']))     ini_set('session.use_cookies', $name['use_cookies']?1:0);
        if(isset($name['cache_limiter']))   session_cache_limiter($name['cache_limiter']);
        if(isset($name['cache_expire']))    session_cache_expire($name['cache_expire']);
    }elseif('' === $value){ 
        if(0===strpos($name,'[')) { // session 操作
            if('[pause]'==$name){ // 暂停session
                session_write_close();
            }elseif('[start]'==$name){ // 启动session
                session_start();
            }elseif('[destroy]'==$name){ // 销毁session
                $_SESSION =  array();
                session_unset();
                session_destroy();
            }elseif('[regenerate]'==$name){ // 重新生成id
                session_regenerate_id();
            }
        }elseif(0===strpos($name,'?')){ // 检查session
            $name   =  substr($name,1);
            if($prefix) {
                return isset($_SESSION[$prefix][$name]);
            }else{
                return isset($_SESSION[$name]);
            }
        }elseif(is_null($name)){ // 清空session
            if($prefix) {
                unset($_SESSION[$prefix]);
            }else{
                $_SESSION = array();
            }
        }elseif($prefix){ // 获取session
            return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;
        }else{
            return isset($_SESSION[$name])?$_SESSION[$name]:null;
        }
    }elseif(is_null($value)){ // 删除session
        if($prefix){
            unset($_SESSION[$prefix][$name]);
        }else{
            unset($_SESSION[$name]);
        }
    }else{ // 设置session
        if($prefix){
            if (!is_array($_SESSION[$prefix])) {
                $_SESSION[$prefix] = array();
            }
            $_SESSION[$prefix][$name]   =  $value;
        }else{
            $_SESSION[$name]  =  $value;
        }
    }
}


/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
	//多行URL地址支持
	$url        = str_replace(array("\n", "\r"), '', $url);
	if (empty($msg))
		$msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent()) {
		// redirect
		if (0 === $time) {
			header('Location: ' . $url);
		} else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	} else {
		$str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0)
			$str .= $msg;
		exit($str);
	}
}

/**
 * 根据类文件获取继承的类名
 * @param string $filePath 类文件路径
 * @return string
 */
function getExtendsClassName($filePath){
	$content = file_get_contents($filePath);
	preg_match("/extends(.*?){/", $content,$matchs);
	unset($content);
	return trim($matchs[1]);
}

/**
 * 判断POST提交
 * @return boolean
 */
function isPost(){
	if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
		return true;
	}else
		return false;
}

/**
 * 判断GET提交
 * @return boolean
 */
function isGet(){
	if(strtolower($_SERVER['REQUEST_METHOD']) == 'get'){
		return true;
	}else
		return false;
}

/**
 * 分页函数
 * @param int $count 总数
 * @param int $page 页数
 * @param string $baseUrl 基础URL
 * @param array $pageParam 分页参数
 * @return string
 */
function showPage($count,$pageNum,$baseUrl,$pageParam = array()) {
	$pagelimit = 10;
	global $system ;
	if($system['URI']['mod'] == 'admin'){
		$page = '<div class="pagination-i">';
		// 	$page .= '<div>'.(($page-1)*$pagelimit +1).'-'.($page*$pagelimit ).'条记录 / 共';
		// 	$page .= $count.'条记录</div>';
		if ($pageNum > 1)
			$page .= '<a href='.U($baseUrl,array_merge($pageParam,array('page'=>$pageNum-1))).'><span>上一页</span></a>';
		if ($pageNum < ceil($count/$pagelimit) )
			$page .= '<a href='.U($baseUrl,array_merge($pageParam,array('page'=>$pageNum+1))).'><span>下一页</span></a>';
		$page .= '</div>';
	}else{
		$file = $system['THEME_PATH'].'public/pager.html';
		$pageContent = '';
		$pageContent = file_get_contents($file);
		if ($pageNum > 1){
			$prevUrl = U($baseUrl,array_merge($pageParam,array('page'=>$pageNum-1)));
		}else{
			$prevUrl = '';
		}
		if ($pageNum < ceil($count/$pagelimit) ){
			$nextUrl = U($baseUrl,array_merge($pageParam,array('page'=>$pageNum+1)));
		}else{
			$nextUrl = '';
		}
		
		$pageContent = preg_replace('/\{prev\}/', $prevUrl, $pageContent);
		$pageContent = preg_replace('/\{next\}/', $nextUrl, $pageContent);
		$pageContent = preg_replace('/\{pageNum\}/', $pageNum, $pageContent);
		
		$page = $pageContent;
	}

	return $page;
}

/**
 * 获取文件目录列表,该方法返回数组
 * @param string $dir
 * @return Ambigous <NULL, string>
 */
function getDir($dir) {
	$dirArray[]=NULL;
	if (false != ($handle = opendir ( $dir ))) {
		$i=0;
		while ( false !== ($file = readdir ( $handle )) ) {
			//去掉"“.”、“..”以及带“.xxx”后缀的文件
			if ($file != "." && $file != ".."&&!strpos($file,".")) {
				$dirArray[$i]=$file;
				$i++;
			}
		}
		//关闭句柄
		closedir ( $handle );
	}
	return $dirArray;
}

/**
 * 获取文件列表
 */
function getFile($dir) {
	$fileArray[]=NULL;
	if (false != ($handle = opendir ( $dir ))) {
		$i=0;
		while ( false !== ($file = readdir ( $handle )) ) {
			//去掉"“.”、“..”以及带“.xxx”后缀的文件
			if ($file != "." && $file != ".."&&strpos($file,".")) {
				$fileArray[$i]="./imageroot/current/".$file;
				if($i==100){
					break;
				}
				$i++;
			}
		}
		//关闭句柄
		closedir ( $handle );
	}
	return $fileArray;
}

/**
 * json输出
 * @param mix $content
 * @param string $callback
 */
function jsonOUT($content = '', $callback = ''){
	
	$content = empty($content)?ob_get_contents():$content;
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
	@header("Content-type: application/json; charset=utf-8");
	if (!is_array($content))
	{
		$content=array('html'=>$content);
	}
	echo empty($callback)?json_encode($content):$callback.'('.json_encode($content).');';
	exit();
}
/**
 * 写文件
 * @param string $file
 * @param string $str
 * @param string $mode
 * @return boolean
 */
function wfile($file, $str, $mode = 'w') {
	$oldmask = @umask ( 002 );
	//@chmod ( $file, 777 );
	//@exec("chmod 777 $file");
	$fp = @fopen ( $file, $mode );
	@flock ( $fp, 3 );
	if (! $fp) {
		return false;
	} else {
		@fwrite ( $fp, $str );
		@fclose ( $fp );
		@umask ( $oldmask );
		return true;
	}
}
/**
 * 清空文件夹
 * @param string $dir 文件夹路径
 */
function deldir($dir) {
	//先删除目录下的文件：
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath)) {
				@unlink($fullpath);
			} else {
				deldir($fullpath);
			}
		}
	}
	closedir($dh);
}
/**
 * html代码解码
 * @param string $str
 * @return string
 */
function html_decode($str){
	return stripslashes(htmlspecialchars_decode($str));
}
/**
 * html代码转码
 * @param string $str
 * @return string
 */
function html_encode($str){
	return htmlspecialchars(addslashes($str));
}