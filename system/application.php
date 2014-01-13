<?php
/**
 * 应用驱动类
 * @copyright   Copyright(c) 2013
 * @author      sunny5156 <blog.cxiangnet.cn>
 * @version     1.0
 */

define ( 'SYSTEM_PATH', dirname ( __FILE__ ) );//SimplePHP路径
define ( 'ROOT_PATH', substr ( SYSTEM_PATH, 0, - 7 ) );//项目路径
define ( 'SYS_LIB_PATH', SYSTEM_PATH . '/lib' );//SimplePHP library路径
define ( 'APP_LIB_PATH', ROOT_PATH . '/lib' );//项目自定义 library路径
define ( 'SYS_CORE_PATH', SYSTEM_PATH . '/core' );//SimplePHP core路径
define ( 'CONTROLLER_PATH', ROOT_PATH . '/controller' );//控制器路径
define ( 'MODEL_PATH', ROOT_PATH . '/model' );//
define ( 'LOG_PATH', ROOT_PATH . '/error/' );
define ( 'VIEW_PATH', ROOT_PATH . '/view');
define ( 'CACHE_PATH', ROOT_PATH . '/cache' );

//公共文件
include_once ROOT_PATH . '/system/include/common.include.php';

//core文件
require_once SYS_CORE_PATH . '/model.php';
require_once SYS_CORE_PATH . '/controller.php';
require_once SYS_CORE_PATH . '/view.php';


final class Application {
	public static $_lib = null;
	public static $_config = null;
	
	public static function init() {
		global $system;
		self::setAutoLibs ();
		//加载默认配置
		$system['route'] = self::$_config ['route'];//路由默认配置
		$system['smarty'] = self::$_config ['smarty'];//smarty默认配置
		$system['smarty'] = self::$_config ['smarty'];//smarty默认配置
		$system['captcha'] = self::$_config ['captcha'];//验证码默认配置
	}
	/**
	 * 创建应用
	 * 
	 * @access public
	 * @param array $config        	
	 */
	public static function run($config) {
		global $system;
		self::$_config = $config ['system'];
		self::init ();
		self::autoload ();
		$url_array = self::$_lib['route']->getUrlArray (); // 将url转发成数组
		self::routeToCm ( $url_array );
	}
	/**
	 * 自动加载类库
	 * 
	 * @access public
	 * @param array $_lib        	
	 */
	public static function autoload() {
		foreach ( self::$_lib as $key => $value ) {
			require (self::$_lib [$key]);
			$lib = $key;
			if($key == 'mysql'){
				self::$_lib [$key] = new $lib(self::$_config ['db']);
			}else{
				self::$_lib [$key] = new $lib;
			}
		}
	}
	/**
	 * 加载类库
	 * 
	 * @access public
	 * @param string $class_name
	 *        	类库名称
	 * @return object
	 */
	public static function newLib($class_name) {
		$app_lib = $sys_lib = '';
		$app_lib = APP_LIB_PATH . '/' . self::$_config ['lib'] ['prefix'] . '_' . $class_name . '.php';
		$sys_lib = SYS_LIB_PATH . '/' . $class_name . '.class.php';
		
		if (file_exists ( $app_lib )) {
			require ($app_lib);
			$class_name = ucfirst ( self::$_config ['lib'] ['prefix'] ) . ucfirst ( $class_name );
			return new $class_name ();
		} else if (file_exists ( $sys_lib )) {
			require ($sys_lib);
			return self::$_lib ['$class_name'] = new $class_name ();
		} else {
			trigger_error ( '加载 ' . $class_name . ' 类库不存在' );
		}
	}
	/**
	 * 自动加载的类库
	 * 
	 * @access public
	 */
	public static function setAutoLibs() {
		self::$_lib = array (
				'route' => SYS_LIB_PATH . '/route.class.php',
				'mysql' => SYS_LIB_PATH . '/mysql.class.php',
				'template' => SYS_LIB_PATH . '/template.class.php',
				'cache' => SYS_LIB_PATH . '/cache.class.php',
				'thumbnail' => SYS_LIB_PATH . '/thumbnail.class.php',
				#'view' => SYS_LIB_PATH . '/view.class.php' ,
				'captcha'=> SYS_LIB_PATH .'/captcha.class.php',
				'file'=> SYS_LIB_PATH .'/file.class.php',
				'image'=> SYS_LIB_PATH .'/image.class.php',
		);
	}
	
	/**
	 * 根据URL分发到Controller和Model
	 * 
	 * @access public
	 * @param array $url_array        	
	 */
	public static function routeToCm($url_array = array()) {
		global $system;
		$app = '';
		$controller = '';
		$action = '';
		$model = '';
		$params = '';

		if (isset ( $url_array ['mod'] )) {
			$app = $url_array ['mod'];
		}else{
			//默认 mod
			$app = self::$_config ['route'] ['default_mod'];
			$url_array ['mod'] = self::$_config ['route'] ['default_mod'];
		}
		
		if (isset ( $url_array ['controller'] )) {
			$controller = $model = $url_array ['controller'];
			if ($app) {
				$controller_file = CONTROLLER_PATH . '/' . $app . '/' . $controller . 'Controller.php';
			} else {
				$controller_file = CONTROLLER_PATH . '/' . $controller . 'Controller.php';
			}
		} else {
			$controller = $model = self::$_config ['route'] ['default_controller'];
			if ($app) {
				$controller_file = CONTROLLER_PATH . '/' . $app . '/' . self::$_config ['route'] ['default_controller'] . 'Controller.php';
			} else {
				$controller_file = CONTROLLER_PATH . '/' . self::$_config ['route'] ['default_mod'] . '/' . self::$_config ['route'] ['default_controller'] . 'Controller.php';
			}
			// 默认 controller
			$url_array ['controller'] = self::$_config ['route'] ['default_controller'];
		}
		
		if (isset ( $url_array ['action'] )) {
			$action = $url_array ['action'];
		} else {
			//默认 action
			$action = self::$_config ['route'] ['default_action'];
		}
		
		if (isset ( $url_array ['params'] )) {
			$params = $url_array ['params'];
		}
		if (file_exists ( $controller_file )) { 
			$extendClass = getExtendsClassName($controller_file);
			if($extendClass != 'Controller'){
				$extendControllerFile = CONTROLLER_PATH . '/' . $app . '/' . $extendClass. '.php';
				require_once $extendControllerFile;
			}
			
			require_once $controller_file;
			$controller = $controller . 'Controller';
			$controller = new $controller();
			//设置URI
			$system['URI'] = $url_array;
			//设置URI
			if ($action) {
				if (method_exists ( $controller, $action )) {
					isset ( $params ) ? $controller->$action ( $params ) : $controller->$action ();
				} else {
					die ( 'action不存在' );
				}
			} else {
				die ( 'action为空' );
			}
		} else {
			die ( 'controller不存在' );
		}
	}
}
