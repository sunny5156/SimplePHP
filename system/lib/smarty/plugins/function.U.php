<?php
/**
 * 模板U函数
 * @param array $args (str:Mod Controller Action param:条件参数)
 * @param object $smarty
 */
function smarty_function_U($args,&$smarty)
{

	$str = $args['str'];
	unset($args['str']);
	if(isset($args['param'])){
		$param = $args['param'];
	}else{
		$param = $args;
	}
	
	echo U($str,$param);
}