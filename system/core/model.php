<?php
/**
 * 核心控制器类
 * @copyright   Copyright(c) 2011
 * @author      sunny5156 <blog.cxiangnet.cn>
 * @version     1.0
 */
class Model {
        protected $db = null;
        private $table = '';
        private $sql = '';
        private $field = ' * ';
        private $where = ' WHERE  1 ';
        private $orderby = '';
        private $groupby = '';
        private $limit = '';
        
        final public function __construct($table) {
                header('Content-type:text/html;chartset=utf-8');
//                 $this->db = load('mysql'); 
//                 if($this->db instanceof mysql){
//                 	$config_db = config('db');
//                 }
//                 $this->table = $config_db['db_table_prefix'].$table;
//                 $this->db->init(
//                         $config_db['db_host'],
//                         $config_db['db_user'],
//                         $config_db['db_password'],
//                         $config_db['db_database'],
//                         $config_db['db_conn'],
//                         $config_db['db_charset']
//                         );                                            //初始话数据库类
                $config_db = config('db');
                $this->table = $config_db['db_table_prefix'].$table;
                $this->db = mysql::getInstance($config_db);
        }


        /**
         * 插入数据(数组key与表列名一致)
         * @param array $data
         */
        public function insert($data){
//         	echo $this->table;
        	$id = $this->db->insert($this->table, $data);
        	return $id;
        }
        /**
         * 修改数据
         * @param string $condition
         * @param array $data
         */
        public function update( $condition="",$data){
        	return $this->db->update($this->table, $data,$condition);
        }
        /**
         * 条件删除
         * @param string|array $condition
         */
        public function delete($condition=""){
        	if(is_array($condition)){
        		$str = ' 1=1 ';
        		foreach($condition as $k=>$v){
        			$str .= "AND {$k}='{$v}'";
        		}
        		
        		$condition = $str;
        		unset($str);
        	}
        	return $this->db->delete( $this->table,$condition);
        }
        
        public function getOne(){
        	$sql = "SELECT {$this->field} FROM `{$this->table}` {$this->where}{$this->orderby}{$this->groupby}{$this->limit}";
        	return $this->db->get_one($sql,$result_type = MYSQL_ASSOC);
        }
        
        public function getAll(){
        	$sql = "SELECT {$this->field} FROM `{$this->table}` {$this->where}{$this->orderby}{$this->groupby}{$this->limit}";
        	return $this->db->get_all($sql,$result_type = MYSQL_ASSOC);
        }
        
        
        public function getPager($page,$baseUrl){
        	$count = $this->db->get_count($this->table);
        	return showPage($count, $page, $baseUrl);
        }
        
        public function field($field = ' * '){
        	$this->field = $field;
        	return $this;
        }
        
        public function where($where = '1 '){
        	$this->where= ' WHERE '.$where;
        	return $this;
        }
        
        public function order($orderby){
        	$this->orderby= ' ORDER BY '.$orderby;
        	return $this;
        }
        
        public function group($groupby){
        	$this->groupby = ' GROUP BY '.$groupby;
        	return $this;
        }
        
        public function limit($start,$count){
        	$this->limit = ' LIMIT '.$start.','.$count;
        	return $this;
        }
        public function page($pageNum){
        	$perNum = 10;
        	$this->limit = ' LIMIT '.($pageNum-1)*$perNum.','.$perNum;
        	return $this;
        }

}


