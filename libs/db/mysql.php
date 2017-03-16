<?php


if (defined('SCAKE_MYSQL_PHP'))
{
	return;
}
else
{
	define('SCAKE_MYSQL_PHP', 1);
}

include_once 'dbo.php';

  /**
   * DB_MySQL class
   * 
   */

class DB_MySQL extends DBO
{
	protected $con;


	public function __construct()
	{
		parent::__construct();
	}

	public function __destruct()
	{
		$this->close();
	}


/** 
 * connect to database
 * 
 * @param host 
 * @param user 
 * @param pwd 
 * @param database 
 * 
 * @return 
 */
	public function connect()
	{
		$host = $this->dsn['host'];
		$user = $this->dsn['user'];
		$psw = $this->dsn['psw'];
		$database = $this->dsn['database'];
		
		$this->con = @mysql_connect($host, $user, $psw, TRUE);
		if (!$this->con)
		{
			//连接DB出错信息放入LOG，返回错误信息
			$msg = "connect to mysql server failed: " . "{$this->dsn['user']}@{$this->dsn['host']}: {$this->dsn['database']}";
			ethrow("数据库连接出错，请联系管理员");
		}
		mysql_select_db($database, $this->con);
		$this->setCharset(DB_CHARSET);	
	}
	
	public function setCharset($charset)
	{
		if($this->con)
		{
			@mysql_set_charset($charset, $this->con);
		}
	}
	
/** 
 * check connection
 * 
 * 
 * @return 
 */
	protected function check()
	{
		if (!@mysql_ping($this->con))
		{
			$this->close();
			$this->connect();
		}
	}



/** 
 * query sql
 * 
 * @param sql 
 * @param limit such as 10  or  '10,10'
 * 
 * @return 
 */
	public function query($sql, $limit = null)
	{
		$this->check();
		if($limit)
		{
			$sql .= ' LIMIT ' . $limit;
		}
		
		$rs = @mysql_query($sql, $this->con);
		if($rs)
		{
			return $rs;
		}
		else
		{
			//将sql的出错信息记录LOG，屏幕上不显示具体的sql信息
			$msg = "Invalid SQL:\n{$sql} \n" . mysql_error($this->con);
			ethrow("数据库访问出错，请联系管理员");
		}
		return false;
	}


/** 
 * get last insert id
 * 
 * 
 * @return 
 */
	public function lastId()
	{
		return mysql_insert_id($this->con);
	}


/** 
 * do fetch
 * 
 * @param rs 
 * @param fetchModel 
 * 
 * @return 
 */
	public function fetch($rs, $fetchModel = self::FETCH_DEFAULT)
	{
		return mysql_fetch_array($rs, $fetchModel);
	}
	


	public function select($fields, $table, $where, $limit = '')
	{
		$sql = $this->buildSelectSql($fields, $table, $where, $limit);
		return $this->getAll($sql, $limit, self::FETCH_ASSOC);
	}
	
	public function selectOne($fields, $table, $where, $limit = '')
	{
		$sql = $this->buildSelectSql($fields, $table, $where, $limit);
		return $this->getOne($sql);
	}
	
	public function selectCol($fields, $table, $where, $limit = '')
	{
		$sql = $this->buildSelectSql($fields, $table, $where, $limit);
		return $this->getCol($sql, $limit);
	}
	
    public function selectRow($fields, $table, $where, $limit = '')
	{
		$sql = $this->buildSelectSql($fields, $table, $where, $limit);
		return $this->getRow($sql, self::FETCH_ASSOC);
	}

	public function selectAll($fields, $table, $where, $limit = '', $key)
	{
		$sql = $this->buildSelectSql($fields, $table, $where, $limit);
		return $this->getAll($sql, $limit, self::FETCH_ASSOC, $key);
	}
/** 
 * update data
 * 
 * @param data 
 * @param table 
 * @param where 
 * 
 * @return 
 */
	public function update($data, $table, $where)
	{
		$sql = " select * from {$table} where {$where}";
		if(! $this->getRow($sql)){
			return $this->insert($data, $table);
		}
			
		$sql = $this->buildUpdateSql($data, $table, $where);
		return $this->query($sql);
	}

	
	
/** 
 * update multiple data
 * 
 * @param multidimensional array
 * @param table 
 * 
 * @return 
 */
	public function updateAll($data, $table, $primaryFieldName, $where = '')
	{
		//拼装批量更新sql语句
		$set	=	array();
		foreach( current($data) as $field=>$val ) {
			$set[$field]	=	" {$field} = case {$primaryFieldName} ";
		}
		
		$caseData	=	array();
		foreach( $data as $row ) {
			foreach( $row as $field=>$val ){
				$caseData[$field][$row[$primaryFieldName]] = " when {$row[$primaryFieldName]} then '".$this->escape($val)."' ";
			}
		}
		
		$sql	=	"update {$table} SET ";
		$setField	=	"";
		foreach( $caseData as $field	=>	$row )
		{
			$setField	=	$set[$field];
			$caseStr	=	"";
			foreach( $row as $primaryField	=>	$case )
			{
				$caseStr	.=	$case; 
				$primaryFieldArr[]	=	$primaryField;
			}
			$sql .= $setField. $caseStr ." end, ";
		}
		$sql = trim( $sql, ', ' );
		
		$primaryFieldStr	=	implode(',', array_unique($primaryFieldArr));
		
		if(!$where){
			$where	=	" where {$primaryFieldName} in ({$primaryFieldStr})";
		}
		$sql	.=	$where;
		
		return $this->query($sql);
	}
	

/** 
 * insert a data
 * 
 * @param data 
 * @param table 
 * 
 * @return 
 */
	public function insert($data, $table)
	{
		$sql = $this->buildInsertSql($data, $table);
		//echo 'sql:'.$sql.'<br />';
		return $this->query($sql);
	}
	
	
/** 
 * insert  multiple data
 * 
 * @param multidimensional array
 * @param table 
 * 
 * @return 
 */
	public function insertAll($data, $table)
	{
		$names = '';
		$values = '';
		
		foreach ( $data[0] as $key => $val)
		{
			$names .= "`" . $key . '`,';
		}
		
		foreach ($data as $v )
		{
			$values	.=	" ( ";
			foreach ( $v as $key => $val)
			{
				$values .= "'" . $this->escape($val) . "',";
			}
			$values	=	trim( $values, "," );
			$values	.=	" ), ";
		}
		
		$names = preg_replace('/,$/', '', $names);
		$values = preg_replace('/,$/', '', $values);

		$sql = ' INSERT INTO ' . $table;
		$sql .= ' (' . $names . ') ';
		$sql .= ' VALUES ' ;
		$sql .=  $values;
		$sql  =	trim( $sql, ", " );
		
		return $this->query($sql);
	}

	
/** 
 * get first column of first row
 * 
 * @param sql 
 * 
 * @return 
 */
	public function getOne($sql)
	{
		$rs = $this->query($sql, 1);
		if (mysql_num_rows($rs) == 0)
		{
			return false;
		}
		
		$row = $this->fetch($rs, self::FETCH_NUM);
		$this->free($rs);
		return $row[0];
	}

	

/** 
 * get first column array
 * 
 * @param sql 
 * @param limit 
 * 
 * @return 
 */
	public function getCol($sql, $limit = null)
	{
		if(!$rs = $this->query($sql, $limit))
		{
			return false;
		}
		
		$result = array();
        while ($row = $this->fetch($rs, self::FETCH_NUM))
		{
            $result[] = $row[0];
        }
        $this->free($rs);
        return $result;
	}
	


/** 
 * get first row
 * 
 * @param sql 
 * @param fetchModel 
 * 
 * @return 
 */
	public function getRow($sql, $fetchModel = self::FETCH_DEFAULT)
	{
		if(!$rs = $this->query($sql, 1))
		{
			return false;
		}
		
		$row = $this->fetch($rs, $fetchModel);
        $this->free($rs);
        return $row;
	}
	


/** 
 * get all data
 * 
 * @param sql 
 * @param limit 
 * @param fetchModel 
 * 
 * @return 
 */
	public function getAll($sql, $limit = null, $fetchModel = self::FETCH_DEFAULT, $key = null)
	{
		
	    if($key && $fetchModel == self::FETCH_NUM)
	    {
	        ethrow('使用某个字段作为key时，不能用FETCH_NUM模式'); 
	    }
		if(!$rs = $this->query($sql, $limit))
		{
			return false;
		}
		
		$all = array();
		while($row = $this->fetch($rs, $fetchModel))
		{
		    if(!$key)
                $all[] = $row;
            else 
                $all[$row[$key]] = $row;
		}
		$this->free($rs);
		return $all;
	}



/** 
 * free result
 * 
 * @param result 
 * 
 * @return 
 */
	public function free($rs = NULL)
	{
		if($rs)
		{
			@mysql_free_result($rs);
		}
	}



/** 
 * set database autocommit
 * 
 * @param mode 
 * 
 * @return 
 */
	public function autoCommit($mode = false)
	{
        $this->check();
        $sql = "set autocommit = ".($mode ? 1 : 0);
	    return mysql_query($sql, $this->con);
    }



/** 
 * commit a transaction
 * 
 * 
 * @return 
 */
	public function commit()
	{
		$flag = mysql_query("commit", $this->con);
		$this->autoCommit(true);
		return $flag;
	}


/** 
 * rollback a transaction
 * 
 * 
 * @return 
 */
	public function rollback()
	{
		$flag = mysql_query('rollback', $this->con);
		$this->autoCommit(true);
		return $flag;
	}



/** 
 * ping
 * 
 * 
 * @return 
 */
	public function ping()
	{
		return mysql_ping($this->con);
	}



/** 
 * close connection
 * 
 * 
 * @return 
 */
    public function close()
	{
		if($this->con)
		{
			return @mysql_close($this->con);
		}

		return false;
    }



/** 
 * build update sql
 * 
 * @param data 
 * @param table 
 * @param where 
 * 
 * @return 
 */
	protected function buildUpdateSql($data, $table, $where)
	{
		$sql = '';
		$sql = ' UPDATE ' . $table . ' SET ';
		foreach ($data as $key => $val)
		{
			$sql .= "`" . $key . "`='" . $this->escape($val) . "',";
		}
		$sql = preg_replace( '/,$/' , '' , $sql );
		$sql .= " where {$where} ";
		
		return $sql;
	}



/** 
 * build insert sql
 * 
 * @param data 
 * @param table 
 * 
 * @return 
 */
	protected function buildInsertSql($data, $table)
	{
		$names = '';
		$values = '';

		foreach ($data as $key => $val)
		{
			$names .= "`" . $key . '`,';
			$values .= "'" . $this->escape($val) . "',";
			//$values .= "'" .$this->escape($val);
		}
		$names = preg_replace('/,$/', '', $names);
		$values = preg_replace('/,$/', '', $values);

		$sql = ' INSERT INTO ' . $table;
		$sql .= ' (' . $names . ') ';
		$sql .= ' VALUES ' ;
		$sql .= ' (' . $values . ') ';
		
		return $sql;
	}

    protected function buildSelectSql($fields, $table, $where, $limit = '')
    {
        $__fields = "";
		if(is_array($fields))
		{
			$__fields = implode(',', $fields);
		}
		else
		{
			$__fields = $fields;
		}

		$sql = " select {$__fields} from {$table} ";
		if($where)
		{
			$sql .= " where {$where} ";
		}
		return $sql;
    }

/** 
 * escape sql
 * 
 * @param str 
 * 
 * @return 
 */
	public function escape($str)
	{
		if (get_magic_quotes_gpc())
		{
			$str = stripslashes_deep($str);
		}


		if (!is_numeric($str))
		{
		    if(!$this->con)
                $this->check();
		    $str = mysql_real_escape_string($str, $this->con);
		}

		return $str;
	}
}



// end of script
