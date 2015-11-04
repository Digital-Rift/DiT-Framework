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
 * Class Db
 * @package DiTFramework
 */
class Db{
	protected $sql;
	protected $select = null;
	protected $where = null;
	protected $limit = null;
	protected $order = null;

	public $error;
	public $query;
	public $table;
	public $schema = array();

	public function connect($driver,$db_name,$host,$user,$password,$table,$multi_site=false,$charset='utf8'){
		if($multi_site==true){
			$this->table = DB_PREFIX.DIT_SITE_NAME.'_'.$table;
		}else{
			$this->table = DB_PREFIX.$table;
		}
		$dsn = $driver.':dbname='.$db_name.';host='.$host;
		try {
			$this->sql = new PDOext($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$charset));
			$file = DIT_APP_DIR.DIT_LOGS_FOLDER.DS.'table_'.$this->table.'.log';
			$this->sql->log_file = $file;
			$this->tableSchema();
		} catch (PDOException $e) {
			trigger_error(i18n::t("Error connecting to database: '::error'", array('error'=>$e->getMessage())), E_USER_ERROR);
		}
	}

	protected function tableSchema(){
		$cache = new Cache($this->table.'_schema');
		if($cache->exist()){
			$this->schema = $cache->get();
		}else{
			$schemas = array();
			$result = $this->sql->query('SHOW FULL COLUMNS FROM `'.$this->table.'`');
			if($result!=false){
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
			}else{
				$this->error();
			}
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
	}

	protected function prepareQuery(){
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

	protected function error(){
		$this->error = $this->sql->errorInfo();
		if(isset($this->error[2])){
			trigger_error(i18n::t("MySQL ошибка: '::error'", array('error'=>$this->error[2])));
		}
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
	}
}
