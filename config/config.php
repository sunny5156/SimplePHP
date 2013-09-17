<?php
/**
 * 系统配置文件
 * @copyright   Copyright(c) 2011
 * @author      sunny5156 <blog.cxiangnet.cn>
 * @version     1.0
 */

/*数据库配置*/
$CONFIG['system']['db'] = array(
    'db_host'           =>      'localhost',
    'db_user'           =>      'root',
    'db_pwd'       		=>      '',
    'db_database'       =>      'db_cxnet',
    'db_table_prefix'   =>      'cx_',
    'db_charset'        =>      'utf8',
    'db_pconnect'       =>      false,             //数据库连接标识; pconn 为长久链接，默认为即时链接
    
);

/*自定义类库配置*/
$CONFIG['system']['lib'] = array(
    'prefix'            =>      'cx'   //自定义类库的文件前缀
);

/*路由配置*/
$CONFIG['system']['route'] = array(
    'default_mod'             		 =>      'default',  //系统默认模块
    'default_controller'             =>      'index',  //系统默认控制器
    'default_action'                 =>      'index',  //系统默认action
    'url_type'                       =>      1          /*定义URL的形式 1 为普通模式    index.php?c=controller&a=action&id=2
                                                         *              2 为PATHINFO   index.php/controller/action/id/2(暂时不实现)              
                                                         */                                                                           
);

/*缓存配置*/
$CONFIG['system']['cache'] = array(
    'cache_dir'                 =>      'cache', //缓存路径，相对于根目录
    'cache_prefix'              =>      'cache_',//缓存文件名前缀
    'cache_time'                =>      1800,    //缓存时间默认1800秒
    'cache_mode'                =>      2,       //mode 1 为serialize ，model 2为保存为可执行文件    
);


/*模板设置*/
$CONFIG['system']['smarty'] = array(
		'template_dir' => VIEW_PATH.'/',//模板路径
		'compile_dir' => CACHE_PATH.'/template_c',//编译文件路径
		'cache_dir' => CACHE_PATH.'/template',//缓存路径
		'caching' => false,//开启缓存 false关闭
		'cache_time' => 600,//缓存时间
		'debug' => false,//调试模式
		'left_delimiter' => '<!--{',
		'right_delimiter' => '}-->',
);

/*mongo配置*/
$CONFIG['system']['mongo'] = array(
		'db_host'	=>	'127.0.0.1',
		'db_user'	=>	'admin',
		'db_pwd'		=>	'admin',
		'db_name'	=>	'cxnet',
		'db_prefix'	=>	'cx'
);

/*验证码配置*/
$CONFIG['system']['captcha'] = array(
		'width'=>80,
	    'height'=>20,
	    'codenum'=>5,
		'session_flag'=>'verify'
);

/*静态数据*/
$CONFIG['system']['static'] = array(
		'uploads' => ROOT_PATH.'/static/uploads',	
);

/*网站信息*/



