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