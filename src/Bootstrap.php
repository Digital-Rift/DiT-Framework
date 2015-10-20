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
 * Class DiT
 * @package Core
 */
class Bootstrap {
	protected $rules = array();

	public function init(){
		ErrorHandler::$memory_usage = memory_get_usage();
		ErrorHandler::$start_time = microtime(true);

		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		if(!defined('BASE_DIR')) define('BASE_DIR', __DIR__.DS);
		if(!defined('CACHE_FOLDER')) define('CACHE_FOLDER', 'Cache');
		if(!defined('PUBLIC_FOLDER')) define('PUBLIC_FOLDER', 'Public');
		if(!defined('MODULES_FOLDER')) define('MODULES_FOLDER', 'Modules');
		if(!defined('CONTROLLERS_FOLDER')) define('CONTROLLERS_FOLDER', 'Controllers');
		if(!defined('MODELS_FOLDER')) define('MODELS_FOLDER', 'Models');
		if(!defined('VIEWS_FOLDER')) define('VIEWS_FOLDER', 'Views');
		if(!defined('LOGS_FOLDER')) define('LOGS_FOLDER', 'Logs');
		if(!defined('COOKIE_LIFE_TIME')) define('COOKIE_LIFE_TIME', 3600);
		if(!defined('SITE_NAME')) define('SITE_NAME', 'default');
		if(!defined('DEV_MODE')) define('DEV_MODE', false);
		if(!defined('WEB_ROOT')) define('WEB_ROOT', '/');

		if(DEV_MODE==true){
			$cache = new Cache('System');
			$cache->clear(false);
		}
	}

	public function start(){
		new Dispatcher($this->rules);
	}

	public function rule($rule,$options=array()){
		$this->rules[WEB_ROOT.$rule] = $options;
	}

	public function rules($rules=array()){
		$outRules = array();
		foreach($rules as $k=>$rule){
			$outRules[WEB_ROOT.$k] = $rule;
		}
		$this->rules = array_merge($this->rules,$outRules);
	}
}