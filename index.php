<?php
/**
 * 应用入口文件
 * @copyright   Copyright(c) 2011
 * @author      sunny5156 <blog.cxiangnet.cn>
 * @version     1.0
 */
require dirname(__FILE__).'/system/application.php';
require dirname(__FILE__).'/config/config.php';
Application::run($CONFIG);



