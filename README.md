CXPHP
=====
自己动手写PHP MVC框架

来自：blog.cxiangnet.cn / 137898350@qq.com

PHP的框架众多，对于哪个框架最好，哪个框架最烂，是否应该用框架，对于这些争论在论坛里面都有人争论，这里不做评价，
个人觉得根据自己需求，选中最佳最适合自己MVC框架，并在开发中能够体现出敏捷开发的效果就OK了，作为一个PHPer要提高自己的对PHP和MVC的框架的认识，所以自己写一个MVC框架是很有必要的，
即使不是很完善，但是自己动手写一个轻量简洁的PHP MVC框架起码对MVC的思想有一定的了解，而且经过自己后期的完善会渐渐形成一个自己熟悉的一个PHP框架。

来写一个PHP MVC框架开发的简明教程，首先声明，教程里面的框架不是一个完善的框架，只是一种思路，当然每个人对MVC框架实现的方法肯定是有差异的，希望高手多提意见多指正，和我一样的菜鸟多讨论多交流，刚接触MVC的PHPer多学习。


app
|-controller	存放控制器文件
|-model		存放模型文件
|-view		存放视图文件	
|-lib		存放自定义类库
|-config	存放配置文件
|--config.php   系统配置文件
|-system	系统核心目录
|-index.php	入口文件

修改:
1.使用smarty模板引擎
2.添加M() , D()方法(仿照tp函数)
3.修改mysql操作类
4.修改了路由分发机制
5.增加static文件夹 存放上传文件(存放在 uploads文件夹)

2013-9-17 15:44:47
命名规范:

controller:
	adminController
action:
	admin();adminManage();
model:
	M('admin');没有model类,只有通用的方法,admin为表名(不带前缀)
	D('admin');有admin model类,可以自定义方法,admin为表名(不带前缀)

函数变量命名: 
	多词:驼峰命名,首字母小写
	单词:全小写
	
	
系统配置:
	siteinfo.php中 网站相关信息
	在system中core Controller.php中,加载默认配置<!--{$SITE.**}-->
