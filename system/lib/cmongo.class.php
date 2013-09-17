<?php

/**
 *      $Id: class_mongo.php 2012-03-13 15:35 yuwenhui $
 */

class cmongo{

	private $dbhost;
	private $dbuser;
	private $dbpw;
	private $dbname;
	private $dbprefix;
	private $dbport = '';
	private $dbresult;
	private $db = "";
	private $is_close = true;
	private $mongo_connection = "";

	function setConfig($_config) {
		$this->dbhost = $_config['db_host'];
		$this->dbuser = $_config['db_user'];
		$this->dbpw = $_config['db_pwd'];
		$this->dbname = $_config['db_name'];
		$this->dbprefix = $_config['db_prefix'];
	}

	function __construct(){
// 		$this->_init_config();
	}
	
	function open(){
		try{
			$dbhost = $this->dbhost;
			$dbuser = $this->dbuser;
			$dbpw = $this->dbpw;
			$dbname = $this->dbname;
			$this->mongo_connection = new Mongo("mongodb://$dbuser:$dbpw@$dbhost/$dbname", array('timeout' => 50));
		}catch(Exception $e){
			die("Mongodb conn Fail!");
		}
		$this->select_db($this->dbname);
		$this->is_close = false;
		return $this->db;
	}
	
	function select_db($dbname){
		$this->db = $this->mongo_connection->selectDB($dbname);
	}

	function _select_collection($collection_name){
		$collection = $this->db->selectCollection($collection_name);
		return $collection;
	}

	function _close(){
		if(!$this->_close()){
			$this->mongo_connection->close();
			$this->_close();
		}	
	}

	function _auto_connection_mongodb(){
		if($this->is_close){
			$this->open();
		}
	}

	function insert($collection_name, $data_array){
		$this->_auto_connection_mongodb();
		$collection = $this->_select_collection($collection_name);
		return $collection->insert($data_array);
	}

	function remove($collection_name, $query, $options=array("justOne"=>false)){
		$collection = $this->_select_collection($collection_name);
		return $collection->remove($query, $options);
	}

	function update_one($collection_name, $query, $newdata){
		$this->_auto_connection_mongodb();
		$collection = $this->_select_collection($collection_name);
		return $collection->update($query, $newdata);
	}
	
	function update_all($collection_name, $query, $newdata){
		$result = false;
		$collection = $this->_select_collection($collection_name);
		$count = $collection->count($count);
		for($i=0;$i<=$count;$i++){
			$result = $collection->update($query, $newdata);
		}
		return $result;
	}

	function count($collection_name, $query=array()){
		$this->_auto_connection_mongodb();
		$collection = $this->_select_collection($collection_name);
		$result = $collection->count($query);
		return $result;
	}
	
	function find_one($collection_name, $query, $field=array()){
		$this->_auto_connection_mongodb();
		$collection = $this->db->selectCollection($collection_name);
		$result = $collection->findOne($query, $field);
		return $result;
	}

	function find_all($collection_name, $query, $field=array(), $start, $perpage){
		$this->_auto_connection_mongodb();
		$result = array();
		$collection = $this->_select_collection($collection_name);
		$cursor = $collection->find($query, $field)->skip($start)->limit($perpage);
		while($cursor->hasNext()){
			$result[] = $cursor->getNext();
		}
		return $result;
	}		
}
?>