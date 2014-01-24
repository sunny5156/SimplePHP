<?php
/**
 * 模板list块
 * @param array $args
 * @param mix $content
 * @param object $smarty
 * @return mix
 */
function smarty_block_list($args, $content, &$smarty){
	$mod = $args["mod"];
	$num = $args["num"];
	
	$where = '1';
	
	if(isset($args['cateid']) && !empty($args['cateid']) && is_numeric($args['cateid'])){
		$where .=' AND cateid ='.$args['cateid'];
	}
	if(isset($args['type']) && !empty($args['type'])){
		$where .=' AND type =\''.$args['type'].'\'';
	}
	if(isset($args['parentid']) && !empty($args['parentid']) && is_numeric($args['parentid'])){
		$where .=' AND parentid ='.$args['parentid'];
	}
	$list = M($mod)->where($where)->order('id DESC')->limit(0, $num)->getAll();
	unset($args);
	$smarty->assign("list",$list);
	return $content;
}


?>