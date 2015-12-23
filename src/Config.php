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
 * Class Config
 * @package DiTFramework
 */
class Config{
	private static $cfg = array(
		'webRoot'=>'/',
		'siteName'=>'default',
		'saveLogs'=>false,
		'cookieLifeTime'=>3600,

		'appDir'=>null,
		'frameworkDir'=>null,
		'publicDir'=>null,

		'cacheFolder'=>'cache',
		'modulesFolder'=>'modules',
		'controllersFolder'=>'controllers',
		'modelsFolder'=>'models',
		'viewsFolder'=>'views',
		'logsFolder'=>'logs',

		'dbDriver'=>'mysql',
		'dbName'=>'dit-framework',
		'dbHost'=>'localhost',
		'dbUser'=>'root',
		'dbPassword'=>'password',
		'dbTablePrefix'=>'dit_',
		'dbCharset'=>'utf8',
	);
	private static $dev_cfg = array(
		'webRoot'=>'/',
		'siteName'=>'default',
		'saveLogs'=>false,
		'cookieLifeTime'=>3600,

		'appDir'=>null,
		'frameworkDir'=>null,
		'publicDir'=>null,

		'cacheFolder'=>'cache',
		'modulesFolder'=>'modules',
		'controllersFolder'=>'controllers',
		'modelsFolder'=>'models',
		'viewsFolder'=>'views',
		'logsFolder'=>'logs',

		'dbDriver'=>'mysql',
		'dbName'=>'dit-framework',
		'dbHost'=>'localhost',
		'dbUser'=>'root',
		'dbPassword'=>'password',
		'dbTablePrefix'=>'dit_',
		'dbCharset'=>'utf8',
	);

	public static function set($key,$value){
		self::$cfg[$key] = $value;
	}

	public static function setDev($key,$value){
		self::$dev_cfg[$key] = $value;
	}

	public static function get($key){

	}
}
