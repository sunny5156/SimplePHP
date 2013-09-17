<?php
interface imongo {
	static function insert($record);
	static function update($condition,$newdata);
	static function delete($condition, $options=array());
	static function find($query_condition, $result_condition=array(), $fields=array());
	static function findOne($condition,$fields=array());
}