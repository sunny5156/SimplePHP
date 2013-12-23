<?php
/*
* mysql 单例
*/
class mysql{
    private $host    ='localhost'; //数据库主机
    private $user     = 'root'; //数据库用户名
    private $pwd     = ''; //数据库用户名密码
    private $database = 'imoro_imoro'; //数据库名
    private $charset = 'utf8'; //数据库编码，GBK,UTF8,gb2312
    private $link;             //数据库连接标识;
    private $rows;             //查询获取的多行数组
    private $pconnect = false;
    
    private $bulletin = true; //是否开启错误记录
    private $show_error = true; //测试阶段，显示所有错误,具有安全隐患,默认关闭
    private $is_error = false; //发现错误是否立即终止,默认true,建议不启用，因为当有问题时用户什么也看不到是很苦恼的
    
    private $handle;
    private $is_log = false;
    private $time;
    static $_instance; //存储对象
    /**
     * 构造函数
     * 私有
     */
    public function __construct($config) {
    	$this->host = $config['db_host'];
    	$this->user = $config['db_user'];
    	$this->pwd = $config['db_pwd'];
    	$this->database = $config['db_database'];
    	$this->charset = $config['db_charset'];
    	$this->pconnect = $config['db_pconnect'];
    	
        if (!$this->pconnect) {
            $this->link = @mysql_connect($this->host, $this->user, $this->pwd) or $this->err();
        } else {
            $this->link = @mysql_pconnect($this->host, $this->user, $this->pwd) or $this->err();
        }
        mysql_select_db($this->database) or $this->err();
        $this->query("SET NAMES '{$this->charset}'", $this->link);
        return $this->link;
    }
    /**
     * 防止被克隆
     *
     */
    private function __clone(){}
    public static function getInstance($config){
        if(FALSE == (self::$_instance instanceof self)){
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }
	
	// 查询
	public function query($sql) {
		$this->write_log ( "查询 " . $sql );
		$query = mysql_query ( $sql, $this->link );
		if (! $query)
			$this->halt ( 'Query Error: ' . $sql );
		return $query;
	}
	
	// 获取记录录（MYSQL_ASSOC，MYSQL_NUM，MYSQL_BOTH）
	public function get_count($table, $where = 1, $result_type = MYSQL_ASSOC) {
		$sql = "select count(*) as count from `{$table}` where {$where}";
		$query = $this->query ( $sql );
		$rt = & mysql_fetch_array ( $query, $result_type );
		$this->write_log ( "获取一条记录 " . $sql );
		return $rt ['count'];
	}
	
	// 获取一条记录（MYSQL_ASSOC，MYSQL_NUM，MYSQL_BOTH）
	public function get_one($sql, $result_type = MYSQL_ASSOC) {
		try{
			$query = $this->query ( $sql );
			$rt = & mysql_fetch_array ( $query, $result_type );
			$this->write_log ( "获取一条记录 " . $sql );
			return $rt;
		}catch(Exception $e){
			debug($e);
			return null;
		}
	}
	
	// 获取全部记录
	public function get_all($sql, $result_type = MYSQL_ASSOC) {
		$query = $this->query ( $sql );
		$i = 0;
		$rt = array ();
		while ( $row = & mysql_fetch_array ( $query, $result_type ) ) {
			$rt [$i] = $row;
			$i ++;
		}
		$this->write_log ( "获取全部记录 " . $sql );
		return $rt;
	}
	
	// 插入
	public function insert($table, $dataArray) {
		$field = "";
		$value = "";
		if (! is_array ( $dataArray ) || count ( $dataArray ) <= 0) {
			$this->halt ( '没有要插入的数据' );
			return false;
		}
		while ( list ( $key, $val ) = each ( $dataArray ) ) {
			$field .= "`$key`,";//添加``
			$value .= "'$val',";
		}
		$field = substr ( $field, 0, - 1 );
		$value = substr ( $value, 0, - 1 );
		$sql = "insert into $table($field) values($value)";
		$this->write_log ( "插入 " . $sql );
		if (! $this->query ( $sql ))
			return false;
		return $this->insert_id ();
	}
	
	// 更新
	public function update($table, $dataArray, $condition = "") {
		if (! is_array ( $dataArray ) || count ( $dataArray ) <= 0) {
			$this->halt ( '没有要更新的数据' );
			return false;
		}
		$value = "";
		while ( list ( $key, $val ) = each ( $dataArray ) ){
			$value .= "`$key` = '$val',";//添加``
		}
		$value = substr ( $value, 0, - 1 );//修改bug 错误使用 .=
		$sql = "update $table set $value where 1=1 and $condition";
		$this->write_log ( "更新 " . $sql );
		if (! $this->query ( $sql ))
			return false;
		return true;
	}
	
	// 删除
	public function delete($table, $condition = "") {
		if (empty ( $condition )) {
			$this->halt ( '没有设置删除的条件' );
			return false;
		}
		$sql = "delete from $table where $condition";
		$this->write_log ( "删除 " . $sql );
		if (! $this->query ( $sql ))
			return false;
		return true;
	}
	
	// 返回结果集
	public function fetch_array($query, $result_type = MYSQL_ASSOC) {
		$this->write_log ( "返回结果集" );
		return mysql_fetch_array ( $query, $result_type );
	}
	
	// 获取记录条数
	public function num_rows($results) {
		if (! is_bool ( $results )) {
			$num = mysql_num_rows ( $results );
			$this->write_log ( "获取的记录条数为" . $num );
			return $num;
		} else {
			return 0;
		}
	}
	
	// 释放结果集
	public function free_result() {
		$void = func_get_args ();
		foreach ( $void as $query ) {
			if (is_resource ( $query ) && get_resource_type ( $query ) === 'mysql result') {
				return mysql_free_result ( $query );
			}
		}
		$this->write_log ( "释放结果集" );
	}
	
	// 获取最后插入的id
	public function insert_id() {
		$id = mysql_insert_id ( $this->link );
		$this->write_log ( "最后插入的id为" . $id );
		return $id;
	}
	
	// 关闭数据库连接
	protected function close() {
		$this->write_log ( "已关闭数据库连接" );
		return @mysql_close ( $this->link );
	}
	
	// 错误提示
	private function halt($msg = '') {
		$msg .= "\r\n" . mysql_error ();
		$this->write_log ( $msg );
		die ( $msg );
	}
	
	// 析构函数
	public function __destruct() {
		$this->free_result ();
		$use_time = ($this->microtime_float ()) - ($this->time);
		$this->write_log ( "完成整个查询任务,所用时间为" . $use_time );
		if ($this->is_log) {
			fclose ( $this->handle );
		}
	}
	
	// 写入日志文件
	public function write_log($msg = '') {
		if ($this->is_log) {
			$text = date ( "Y-m-d H:i:s" ) . " " . $msg . "\r\n";
			fwrite ( $this->handle, $text );
		}
	}
	
	// 获取毫秒数
	public function microtime_float() {
		list ( $usec, $sec ) = explode ( " ", microtime () );
		return (( float ) $usec + ( float ) $sec);
	}
	
	/**
	 * 错误信息输出
	 */
	protected function err($sql = null) {
		//这里输出错误信息
		exit('mysql error');
	}
	
}