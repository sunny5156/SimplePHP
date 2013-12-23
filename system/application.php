<?php
/**
 * 应用驱动类
 * @copyright   Copyright(c) 2013
 * @author      sunny5156 <blog.cxiangnet.cn>
 * @version     1.0
 */



define ( 'SYSTEM_PATH', dirname ( __FILE__ ) );
define ( 'ROOT_PATH', substr ( SYSTEM_PATH, 0, - 7 ) );
define ( 'SYS_LIB_PATH', SYSTEM_PATH . '/lib' );
define ( 'APP_LIB_PATH', ROOT_PATH . '/lib' );
define ( 'SYS_CORE_PATH', SYSTEM_PATH . '/core' );
define ( 'CONTROLLER_PATH', ROOT_PATH . '/controller' );
define ( 'MODEL_PATH', ROOT_PATH . '/model' );
define ( 'LOG_PATH', ROOT_PATH . '/error/' );
define ( 'VIEW_PATH', ROOT_PATH . '/view');
define ( 'CACHE_PATH', ROOT_PATH . '/cache' );

include_once ROOT_PATH . '/system/include/common.include.php';


final class Application {
	public static $_lib = null;
	public static $_config = null;
	public static function init() {
		self::setAutoLibs ();
		// debug(self::$_lib,0);
		require SYS_CORE_PATH . '/model.php';
		require SYS_CORE_PATH . '/controller.php';
	}
	/**
	 * 创建应用
	 * 
	 * @access public
	 * @param array $config        	
	 */
	public static function run($config) {
		self::$_config = $config ['system'];
		self::init ();
		self::autoload ();
		self::$_lib ['route']->setUrlType ( self::$_config ['route'] ['url_type'] ); // 设置url的类型
		$url_array = self::$_lib ['route']->getUrlArray (); // 将url转发成数组
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
		
		// 初始化cache
		// if(is_object(self::$_lib['cache'])){
		// self::$_lib['cache']->init(
		// ROOT_PATH.'/'.self::$_config['cache']['cache_dir'],
		// self::$_config['cache']['cache_prefix'],
		// self::$_config['cache']['cache_time'],
		// self::$_config['cache']['cache_mode']
		// );
		// }
		
		// 初始化smarty
		if (is_object ( self::$_lib ['view'] )) {
			self::$_lib ['view']->setConfig ( self::$_config ['smarty'] );
		}
// 		if (is_object ( self::$_lib ['cmongo'] )) {
// 			self::$_lib ['cmongo']->setConfig ( self::$_config ['cmongo'] );
// 		}
		if (is_object ( self::$_lib ['captcha'] )) {
			self::$_lib ['captcha']->setConfig ( self::$_config ['captcha'] );
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
				'view' => SYS_LIB_PATH . '/view.class.php' ,
// 				'cmongo'=> SYS_LIB_PATH .'/cmongo.class.php',
				'captcha'=> SYS_LIB_PATH .'/captcha.class.php'
		);
	}
	
	/**
	 * 根据URL分发到Controller和Model
	 * 
	 * @access public
	 * @param array $url_array        	
	 */
	public static function routeToCm($url_array = array()) {
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
// 				$model_file = MODEL_PATH . '/' . $app . '/' . $model . 'Model.php';
			} else {
				$controller_file = CONTROLLER_PATH . '/' . $controller . 'Controller.php';
// 				$model_file = MODEL_PATH . '/' . $model . 'Model.php';
			}
		} else {
			$controller = $model = self::$_config ['route'] ['default_controller'];
			if ($app) {
				$controller_file = CONTROLLER_PATH . '/' . $app . '/' . self::$_config ['route'] ['default_controller'] . 'Controller.php';
// 				$model_file = MODEL_PATH . '/' . $app . '/' . self::$_config ['route'] ['default_controller'] . 'Model.php';
			} else {
				$controller_file = CONTROLLER_PATH . '/' . self::$_config ['route'] ['default_mod'] . '/' . self::$_config ['route'] ['default_controller'] . 'Controller.php';
// 				$model_file = MODEL_PATH . '/' . self::$_config ['route'] ['default_controller'] . 'Model.php';
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
// 			if (file_exists ( $model_file )) {
// 				require $model_file;
// 			}
			$extendClass = getExtendsClassName($controller_file);
			if($extendClass != 'Controller'){
				$extendControllerFile = CONTROLLER_PATH . '/' . $app . '/' . $extendClass. '.php';
				require_once $extendControllerFile;
			}
			
			require $controller_file;
			$controller = $controller . 'Controller';
			$controller = new $controller();
			$controller->setUrlArray ( $url_array ); // 用于模板匹配路径
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
