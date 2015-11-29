<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace DiTFramework;

/**
 * Class Model
 * @package DiTFramework
 */
class Model extends Assist{
	public function table($table){
		$db = new Db();
		$db->connect(
			DIT_DB_DRIVER,
			DIT_DB_NAME,
			DIT_DB_HOST,
			DIT_DB_USER,
			DIT_DB_PASSWORD,
			$table,
			DIT_DB_TABLE_PREFIX,
			DIT_DB_CHARSET
		);
		return $db;
	}
}