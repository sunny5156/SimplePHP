<?php
/**
 +--------------------------------------------------
 * View的Smarty(为兼容smarty而编写的视图类)
 * 具体使用方法请查询smarty的使用方法
 +--------------------------------------------------
 * @package Core
 * @subpackage View
 * @author sunny5156 <blog.cxiangnet.cn>
 * @version 1.0
 +--------------------------------------------------
 */

class View{
	
	private $_smarty = null;
// 	private $_config = null;
	
	public function __construct() {
		global $system;
		$this->_smarty = new Smarty();
		$this->_smarty->caching = $system['smarty']['caching'];
		$this->_smarty->template_dir = $system['smarty']['template_dir'];
		$this->_smarty->compile_dir = $system['smarty']['compile_dir'];
		$this->_smarty->cache_dir = $system['smarty']['cache_dir'];
		$this->_smarty->left_delimiter = $system['smarty']['left_delimiter'];
		$this->_smarty->right_delimiter = $system['smarty']['right_delimiter'];
		$this->_smarty->debugging = $system['smarty']['debug'];
		$this->_smarty->cache_lifetime = $system['smarty']['cache_time'];
		$this->_smarty->allow_php_tag=true;
		$this->_smarty->php_handling = SMARTY_PHP_ALLOW ;
	}
	//设置模板
	public function setTheme($theme){
		$this->_smarty->template_dir = $this->_smarty->template_dir ."/{$theme}/";
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