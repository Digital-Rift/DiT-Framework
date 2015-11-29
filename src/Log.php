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
 * Class Log
 * @package DiTFramework
 */
class Log
{
	public static $log = array();

	public static function setLog($name){
		if(!isset(self::$log[$name])){
			self::$log[$name] = '';
		}
	}

	public static function addString($name,$string){
		self::$log[$name] .= $string."\n";
	}

	private static function parseArray($array,$header=null){
		ob_start();
		print_r($array);
		if(!empty($header)){
			return $header.': '.ob_get_clean()."\n";
		}else{
			return ob_get_clean()."\n";
		}
	}

	public static function addArray($name,$array,$header=null){
		self::$log[$name] .= self::parseArray($array,$header);
	}

	public static function save(){
		if(DIT_SAVE_LOGS){
			foreach(self::$log as $name=>$data){
				$file = DIT_APP_DIR.DIT_LOGS_FOLDER.DS.$name.'_'.DIT_SITE_NAME.'.log';
				if(file_exists($file)){
					$data = file_get_contents($file).$data;
				}
				file_put_contents($file,$data);
			}
		}
	}
}