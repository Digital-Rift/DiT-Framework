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
	private $cfg_default = array(
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

	private $cfg = array(
		'public'=>array(),
		'dev'=>array(),
	);

	function __construct(){
		$this->cfg['public'] = $this->cfg_default;
		$this->cfg['dev'] = $this->cfg_default;
	}

	public function set($key,$value){
		self::$cfg[$key] = $value;
	}

	public function get($key){
		if(DIT_DEV_MODE){
			if(isset(self::$cfg[$key])){
				return self::$cfg[$key];
			}else{
				return false;
			}
		}else{
			if(isset(self::$dev_cfg[$key])){
				return self::$dev_cfg[$key];
			}else{
				return false;
			}
		}
	}
}
