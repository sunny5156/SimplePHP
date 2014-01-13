<?php
/**
 * 核心控制器
 * @copyright   Copyright(c) 2013
 * @author      sunny5156 <blog.cxiangnet.cn>
 * @version     1.0
 */
class Controller{
	
        var $_view = '';
        var $_url_array = '';
        var $_theme = '';
        public function __construct() {
          // header('Content-type:text/html;chartset=utf-8');
          #$this->_view = load('view');
          $this->_view = new View();
          //默认配置
          $siteInfoFile = ROOT_PATH.'/config/siteinfo.php';
          $navInfoFile = ROOT_PATH.'/config/nav.php';
          global $system ;
          $this->_theme = 'default';
          if(file_exists($siteInfoFile)){
            $system = include $siteInfoFile;
            $this->_theme = $system['theme'];
          }
          if(file_exists($navInfoFile)){
          	$nav = include $navInfoFile;
          	$system['NAV'] = $nav;
          }
          //静态文件路径
          $system['THEME_PATH'] = "view/{$system['theme']}/";
          $system['JS_PATH'] = "view/{$system['theme']}/public/js/";
          $system['CSS_PATH'] = "view/{$system['theme']}/public/css/";
          $system['IMAGES_PATH'] = "view/{$system['theme']}/public/images/";
          
          $this->assign('system', $system);
        }

        /**
         * 设置url信息,用于模板路径使用
         * @param array $arr
         */
        final function setUrlArray($arr){
        	global $system ;
        	$this->_url_array = $arr;
        	//当前mod
        	$system['mod'] = $this->_url_array['mod'];
        }
		/**
		 * 模板赋值
		 * @param string $name 模板变量名
		 * @param mix $val 变量值
		 */       
        final protected function assign($name,$val) {
        	$this->_view->assign($name,$val);
        }
        /**
         * 模板替换
         * @param string $tpl 模板文件
         */
		final protected function display($tpl) {
			global $system;
			if($system['URI']['mod'] == 'admin'){
				$tpl = $system['URI']['mod'].'/'.$system['URI']['controller'].'/'.$tpl;
			}else{
				$this->_view->setTheme($this->_theme);//设置当前theme的模板路径
				$tpl = VIEW_PATH.'/'.$this->_theme.'/'.$system['URI']['controller'].'/'.$tpl;
			}
			$this->_view->display($tpl);
		}
}


