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
	public $log_file = null;

	public function query($string){
		$start = microtime(true);
		$query = parent::query($string);
		$time = round(microtime(true)-$start,4);
		if(DIT_SAVE_LOGS){
			file_put_contents($this->log_file,date('c').'; Ip: '.$_SERVER['REMOTE_ADDR'].'; Time: '.$time.'; Query: '.var_export($string,1)."\n",FILE_APPEND);
		}
		return $query;
	}
}