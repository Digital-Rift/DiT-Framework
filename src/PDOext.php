<?php
/**
 * @project DIT Framework
 * @link http://digitalrift.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DITFramework;

use PDO;

/**
 * Class PDOext
 * PDO дополнение для записи логов и измерения скорости запросов
 *
 * @package DITFramework
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
			trigger_error($this->driver." error: '".$errorInfo[2]."'");
		}

		return $sql;
	}
}