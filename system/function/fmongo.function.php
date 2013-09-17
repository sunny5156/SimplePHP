<?php


function mongoConnect(){
	$_lib['mongo']->open();
}


function mongoInsert($table,$data){
	$_lib['mongo'] -> insert($table,$data);
}