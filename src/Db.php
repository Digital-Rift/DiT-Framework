<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace DiTFramework;

use PDO;
use PDOException;

/**
 * Class PDOext
 * @package DiTFramework
 */
class PDOext extends PDO{
	public $table = null;
	public $driver = null;
	public $executeResult;
	public $executeData;

	public function query($sql,array $data = array()){

		$sql = parent::prepare($sql);

		$start = microtime(true);
		$this->executeResult = $sql->execute($data);
		$this->executeData = $data;
		$time = round(microtime(true)-$start,4);

		if($this->executeResult!=true){
			$errorInfo = $sql->errorInfo();
			trigger_error(i18n::t("::driver error: '::error'", array('error'=>$errorInfo[2],'driver'=>$this->driver)));
		}else{
			$str = date('c').'; Ip: '.$_SERVER['REMOTE_ADDR'].'; Time: '.$time.'; Query: '.var_export($sql->queryString,1);
			Log::setLog('table_'.$this->table);
			Log::addString('table_'.$this->table, $str);
		}

		return $sql;
	}
}

/**
 * Class Db
 * @package DiTFramework
 * @property PDOext $sql
 */
class Db{
	protected $sql;
	protected $query = null;
	protected $first = null;
	protected $where = null;
	protected $limit = null;
	protected $order = null;
	protected $group = null;
	protected $data = array();
	protected static $whereKeyIndex = 0;
	protected static $_sqlInstance;

	public $table;
	public $queryString;
	public $executeResult;
	public $executeData;

	public static $lastQueryString;
	public static $lastExecuteResult;
	public static $lastExecuteData;

	public function __construct($newInstance=false){
		if($newInstance === true) self::$_sqlInstance = null;
	}

	public function connect($driver,$db_name,$host,$user,$password,$table,$table_prefix=null,$charset='utf8'){
		$this->table = $table_prefix.$table;
		$dsn = $driver.':dbname='.$db_name.';host='.$host;
		try {
			if (null === self::$_sqlInstance){
				self::$_sqlInstance = new PDOext($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$charset));
			}
			$this->sql = self::$_sqlInstance;
			$this->sql->table = $this->table;
			$this->sql->driver = $driver;
		} catch (PDOException $e) {
			trigger_error(i18n::t("Error connecting to database: '::error'", array('error'=>$e->getMessage())),E_USER_ERROR);
		}
	}

	protected function setData($data=array()){
		$this->data = array_merge($this->data,$data);
	}

	protected function fastQuery(){
		if($this->first!=null) $this->query = $this->first;
		if($this->where!=null) $this->query .= ' '.$this->where;
		if($this->group!=null) $this->query .= ' '.$this->group;
		if($this->order!=null) $this->query .= ' '.$this->order;
		if($this->limit!=null) $this->query .= ' '.$this->limit;
		$this->first = null;
		$this->where = null;
		$this->group = null;
		$this->order = null;
		$this->limit = null;
		$result = $this->sql->query($this->query,$this->data);
		$this->queryString = $result->queryString;
		self::$lastQueryString = $result->queryString;
		$this->executeResult = $this->sql->executeResult;
		self::$lastExecuteResult = $this->sql->executeResult;
		$this->executeData = $this->sql->executeData;
		self::$lastExecuteData = $this->sql->executeData;
		$this->data = array();
		return $result;
	}

	protected function genWhere($cond,$key,$value){
		$where = null;
		if(strtoupper($cond)=='IN') {
			if(is_array($value)){
				$aKeys = array_keys($value);
				$newValues = array_combine(preg_replace('/^/',':where_in_'.self::$whereKeyIndex.'_',$aKeys,1),$value);
				$where = $key.' IN ('.implode(',',array_keys($newValues)).')';
				$this->setData($newValues);
				self::$whereKeyIndex = self::$whereKeyIndex+1;
			}
		}elseif(strtoupper($cond)=='CQUERY'){
			$where = $key;
		}elseif(strtoupper($cond)=='EXISTS'){
			$where = $cond.' '.$key;
		}elseif(strtoupper($cond)=='NOT EXISTS'){
			$where = $cond.' '.$key;
		}elseif(strtoupper($cond)=='NULL' OR strtoupper($cond)=='IS NULL'){
			$where = $key.' IS NULL';
		}elseif(strtoupper($cond)=='NOT NULL' OR strtoupper($cond)=='IS NOT NULL'){
			$where = $key.' IS NOT NULL';
		}elseif(strtoupper($cond)=='MATCH'){
			if(is_array($key) AND strlen($value)>0){
				$search = explode(' ',htmlspecialchars(trim($value),ENT_QUOTES));
				$against = implode('* +',$search);
				$where = $cond.' ('.implode(',',$key).') AGAINST (\'+'.$against.'*\' IN BOOLEAN MODE)';
			}
		}else{
			$newKey = preg_replace('/^/',':where_'.self::$whereKeyIndex.'_',$key,1);
			$where = $key.' '.$cond.' '.$newKey;
			$this->setData(array($newKey=>$value));
			self::$whereKeyIndex = self::$whereKeyIndex+1;
		}
		return $where;
	}

	public function multiWhere($cond=array()){
		$where = null;
		foreach($cond as $val){
			$where .= $this->genWhere($val[0],$val[1],$val[2]).' ';
			if(isset($val[3])){
				$where .= $val[3].' ';
			}
		}
		if($this->where!=null){
			$this->where .= ' '.$where;
		}else{
			$this->where = 'WHERE '.$where;
		}
		return $this;
	}

	public function where($cond,$key,$value){
		$where = $this->genWhere($cond,$key,$value);
		if($this->where!=null){
			$this->where .= ' '.$where;
		}else{
			$this->where = 'WHERE '.$where;
		}
		return $this;
	}

	public function _and(){
		if($this->where!=null){
			$this->where .= ' AND';
		}
		return $this;
	}

	public function _or(){
		if($this->where!=null){
			$this->where .= ' OR';
		}
		return $this;
	}

	public function _startGroup(){
		if($this->where!=null){
			$this->where .= ' (';
		}
		return $this;
	}

	public function _endGroup(){
		if($this->where!=null){
			$this->where .= ')';
		}
		return $this;
	}

	public function fullTextSearch($columns=array(),$search){
		if(count($columns)>0){
			if($this->where!=null){
				$this->where .= ' AND '.$this->genWhere('MATCH',$columns,$search);
			}else{
				$this->where = 'WHERE '.$this->genWhere('MATCH',$columns,$search);
			}
		}
		return $this;
	}

	public function getAll(){
		$this->first = 'SELECT * FROM '.$this->table;
		$result = $this->fastQuery();
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get(){
		$this->first = 'SELECT * FROM '.$this->table;
		$result = $this->fastQuery();
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	public function insert($values=array()){
		if(count($values)>0){
			$aKeys = array_keys($values);
			$newValues = array_combine(preg_replace('/^/',':',$aKeys,1),$values);
			$this->first = "INSERT INTO ".$this->table." (".implode(',',$aKeys).") VALUE (".implode(',',array_keys($newValues)).")";
			$this->setData($newValues);
			$this->fastQuery();
			if($this->executeResult!=false){
				return $this->sql->lastInsertId();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function update($values=array()){
		if(count($values)>0){
			$aKeys = array_keys($values);
			$newValues = array_combine(preg_replace('/^/',':',$aKeys,1),$values);
			$newKeys = array_combine($aKeys,preg_replace('/^/',':',$aKeys,1));
			$set = null;
			$i=1;
			$count = count($newKeys);
			foreach($newKeys as $k => $v){
				$set .= "{$k} = {$v}";
				if($i<$count){
					$set .= ", ";
				}
				$i++;
			}
			$this->first = "UPDATE ".$this->table." SET ".$set;
			$this->setData($newValues);
			return $this->fastQuery();
		}else{
			return false;
		}
	}

	public function delete(){
		$this->first = "DELETE FROM ".$this->table;
		return $this->fastQuery();
	}

	public function truncate(){
		$this->first = "TRUNCATE TABLE ".$this->table;
		return $this->fastQuery();
	}

	public function exist(){
		$this->first = "SELECT * FROM ".$this->table;
		$result = $this->fastQuery();
		if($this->executeResult!=false){
			if($result->rowCount()>0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function count(){
		$this->first = "SELECT * FROM ".$this->table;
		$result = $this->fastQuery();
		return $result->rowCount();
	}

	public function group($column){
		$this->group = "GROUP BY ".$column;
		return $this;
	}

	public function limit($offset=0,$rows=null){
		if($rows!=null)$rows = ','.$rows;
		$this->limit = "LIMIT ".$offset.$rows;
		return $this;
	}

	public function order($column,$order=null){
		if($order!=null)$order = ' '.$order;
		$this->order = "ORDER BY ".$column.$order;
		return $this;
	}

	public function rand(){
		$this->order = "ORDER BY RAND()";
		return $this;
	}

/*	protected function tableSchema(){
		$cache = new Cache($this->table.'_schema');
		if($cache->exist()){
			$this->schema = $cache->get();
		}else{
			$schemas = array();
			$result = $this->sql->query('SHOW FULL COLUMNS FROM '.$this->table.'');
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach($result as $field) {
				$schemas[$field['Field']]=array(
					'type' => $this->schemaColumn($field['Type']),
					'null' => ($field['Null']=='YES' ? true : false),
					'default' => $field['Default'],
					'length' => $this->schemaLength($field['Type']),
					'primaryKey' => ($field['Key']=='PRI' ? true : false)
				);
			}
			$this->schema = $schemas;
			$cache->save($this->schema);
		}
	}

	protected function schemaColumn($type){
		$type = str_replace(')','',$type);
		$type = explode('(',$type);
		return $type[0];
	}

	protected function schemaLength($type){
		$type = str_replace(')','',$type);
		$type = explode('(',$type);
		return (count($type)>1)?$type[1]:null;
	}*/

	/*protected function prepareQuery(){
		if($this->select!=null){
			$this->query = $this->select;
		}
		if($this->where!=null){
			$this->query .= ' '.$this->where;
		}
		if($this->order!=null){
			$this->query .= ' '.$this->order;
		}
		if($this->limit!=null){
			$this->query .= ' '.$this->limit;
		}
		$this->select = null;
		$this->where = null;
		$this->order = null;
		$this->limit = null;
	}

	public function findAll($colums=array()){
		$select = '*';
		if(count($colums)>0){
			$select = null;
			$count = count($colums);
			$i=1;
			foreach($colums as $c){
				$sep = null;
				if($i!=$count){
					$sep = ', ';
				}
				$select .= '`'.$c.'`'.$sep;
				$i++;
			}
		}
		$this->select = "SELECT ".$select." FROM `".$this->table."`";
		$this->prepareQuery();
		$result = $this->sql->query($this->query);
		if($result!=false){
			return $result->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$this->error();
			return false;
		}
	}

	public function find($colums=array()){
		$select = '*';
		if(count($colums)>0){
			$select = null;
			$count = count($colums);
			$i=1;
			foreach($colums as $c){
				$sep = null;
				if($i!=$count){
					$sep = ', ';
				}
				$select .= '`'.$c.'`'.$sep;
				$i++;
			}
		}
		$this->select = "SELECT ".$select." FROM `".$this->table."`";
		$this->prepareQuery();
		$result = $this->sql->query($this->query);
		if($result!=false){
			return $result->fetch(PDO::FETCH_ASSOC);
		}else{
			$this->error();
			return false;
		}
	}

	public function exist(){
		$this->select = "SELECT * FROM `".$this->table."`";
		$this->prepareQuery();
		$result = $this->sql->query($this->query, PDO::FETCH_ASSOC);
		if($result!=false){
			if($result->rowCount()>0){
				return true;
			}else{
				return false;
			}
		}else{
			$this->error();
			return false;
		}
	}

	public function where($cond,$key,$val){
		$where = null;
		if($cond=='IN' OR $cond=='in') {
			if($val==array()){
				$in = array();
				foreach($val as $k=>$v){
					$in[] = $this->value($v,$key);
				}
				$where = '`'.$key.'` IN ('.implode(',',$in).')';
			}
		}else{
			$where = '`'.$key.'` '.$cond.' '.$this->value($val,$key);
		}
		if($this->where!=null){
			$this->where .= $this->where.' '.$where;
		}else{
			$this->where = 'WHERE '.$where;
		}
		return $this;
	}

	public function _and(){
		if($this->where!=null){
			$this->where .= $this->where.' AND';
		}
		return $this;
	}

	public function _or(){
		if($this->where!=null){
			$this->where .= $this->where.' OR';
		}
		return $this;
	}

	public function limit($offset=0,$rows=null){
		if($rows!=null)$rows = ','.$rows;
		$this->limit = "LIMIT ".$offset.$rows;
		return $this;
	}

	public function order($column,$order=null){
		$this->order = "ORDER BY `".$column."` ".$order;
		return $this;
	}

	public function rand(){
		$this->order = "ORDER BY RAND()";
		return $this;
	}

	public function update($array){
		$set = null;
		foreach($array as $k => $v){
			$v = $this->value($v,$k);
			$set .= "`{$k}` = {$v}";
			end($array);
			if ($k == key($array)){
				$d=" ";
			}else{
				$d=", ";
			}
			$set .= $d;
		}
		$this->select = "UPDATE `".$this->table."` SET ".$set;
		$this->prepareQuery();
		$result = $this->sql->query($this->query);
		if($result!=false){
			return $result;
		}else{
			$this->error();
			return false;
		}
	}

	public function insert($array){
		$query = '(';
		foreach($array as $k => $v){
			$query .= "`{$k}`";
			end($array);
			if ($k != key($array)){
				$d=",";
			}else{
				$d="";
			}
			$query .= $d;
		}
		$query .= ') VALUE (';
		foreach($array as $k => $v){
			$v = $this->value($v,$k);
			$query .= $v;
			end($array);
			if ($k != key($array)){
				$d=",";
			}else{
				$d="";
			}
			$query .= $d;
		}
		$query .= ')';
		$this->select = "INSERT INTO `".$this->table."` ".$query;
		$this->prepareQuery();
		$result = $this->sql->query($this->query);
		if($result!=false){
			return $this->sql->lastInsertId();
		}else{
			$this->error();
			return false;
		}
	}

	public function delete(){
		$this->select = "DELETE FROM `".$this->table."`";
		$this->prepareQuery();
		$result = $this->sql->query($this->query);
		if($result!=false){
			return $result;
		}else{
			$this->error();
			return false;
		}
	}

	private function value($value,$key){
		if(isset($this->schema[$key])){
			$type = $this->schema[$key]['type'];
			switch($type){
				case 'tinyint':case 'smallint':case 'mediumint':case 'int':case 'integer': case'bigint':
					return $this->sql->quote($value,PDO::PARAM_INT);
					break;
				case 'float':case 'double':case 'decimal':
					return floatval($value);
					break;
				case 'varchar':case 'text':case 'longtext':case 'mediumtext':case 'tinytext':case 'char':case 'string':
					return $this->sql->quote($value,PDO::PARAM_STR);
					break;
				default:
					return $this->sql->quote($value);
			}
		}else{
			trigger_error(i18n::t("MySQL ошибка: ключ '::key' не существует", array('key'=>$key)));
			return false;
		}
	}*/
}
