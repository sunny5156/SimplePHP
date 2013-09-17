<?php

/**
 +--------------------------------------------------
 * View的Smarty(为兼容smarty而编写的视图类)
 * 具体使用方法请查询smarty的使用方法
 +--------------------------------------------------
 * @category Toper
 * @package Core
 * @subpackage View
 * @author mingtingling
 * @version 0.5
 +--------------------------------------------------
 */
 



class view{
	
	private $_smarty = null;
// 	private $_config = null;
	
	public function __construct() {
		$this->_smarty = new Smarty();
// 		debug($this->_smarty,0);
// 		$this->_smarty->caching = $config['caching'];
// 		$this->_smarty->template_dir = $config['template_dir'];
// 		$this->_smarty->compile_dir = $config['compile_dir'];
// 		$this->_smarty->cache_dir = $config['cache_dir'];
// 		$this->_smarty->left_delimiter = $config['left_delimiter'];
// 		$this->_smarty->right_delimiter = $config['right_delimiter'];
// 		$this->_smarty->debugging = $config['debug'];
// 		$this->_smarty->cache_lifetime = $config['cache_time'];
	}
	
	public function setConfig($config){
		$this->_smarty->caching = $config['caching'];
		$this->_smarty->template_dir = $config['template_dir'];
		$this->_smarty->compile_dir = $config['compile_dir'];
		$this->_smarty->cache_dir = $config['cache_dir'];
		$this->_smarty->left_delimiter = $config['left_delimiter'];
		$this->_smarty->right_delimiter = $config['right_delimiter'];
		$this->_smarty->debugging = $config['debug'];
		$this->_smarty->cache_lifetime = $config['cache_time'];
		$this->_smarty->allow_php_tag=true;
		$this->_smarty->php_handling = SMARTY_PHP_ALLOW ;
	}
	
	/**
	 +------------------------------------------------
	 * assign
	 +------------------------------------------------
	 * @access public
	 * @param string $name
	 * @param mixed $val
	 * @return void
	 +------------------------------------------------
	 */
	public function assign($name,$val) {
		$this->_smarty->assign($name,$val);
	}
	
	/**
	 +------------------------------------------------
	 * display
	 +------------------------------------------------
	 * @access public
	 * @param string $tpl
	 * @return void
	 +------------------------------------------------
	 */
	public function display($tpl) {
		$this->_smarty->display($tpl);
	}

}