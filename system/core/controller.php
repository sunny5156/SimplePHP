<?php
/**
 * 核心控制器
 * @copyright   Copyright(c) 2011
 * @author      sunny5156 <blog.cxiangnet.cn>
 * @version     1.0
 */
class Controller{
	
        var $_view = '';
        var $_url_array = '';
        public function __construct() {
               // header('Content-type:text/html;chartset=utf-8');
               $this->_view = load('view');
        }

        /**
         * 设置url信息,用于模板路径使用
         * @param array $arr
         */
        final function setUrlArray($arr){
        	$this->_url_array = $arr;
        }
        /**
         * 加载模板文件
         * @access      final   protect
         * @param       string  $path   模板路径
         * @return      string  模板字符串
         */
        final protected function showTemplate($path,$data = array()){
                $template =  load('template');
                $template->init($path,$data);
                $template->outPut();
        }
        
        final protected function assign($name,$val) {
        	$this->_view->assign($name,$val);
        }
        
		final protected function display($tpl) {
// 			if(empty($tpl)){
// 				$tpl = $this->_url_array['mod'].'/'.$this->_url_array['controller'].'/'.$this->_url_array['action'];
// 			}
			$tpl = $this->_url_array['mod'].'/'.$this->_url_array['controller'].'/'.$tpl;
			
			//默认配置
			$siteInfoFile = ROOT_PATH.'/config/siteinfo.php';
			if(file_exists($siteInfoFile)){
				$SITE = include $siteInfoFile;
				$this->assign('SITE', $SITE);
			}
			$this->_view->display($tpl);
		}
}


