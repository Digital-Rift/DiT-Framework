<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace DiTFramework;
use DiTFramework\Errors\ErrorHandler;

/**
 * Class App
 * @package DiTFramework
 */
class App {
	private $rules = array();
	private $devMode = false;
	private $webRoot = '/';
	private $siteName = 'default';
	private $saveLogs = false;
	private $cookieLifeTime = 3600;
	private $appDir = null;
	private $frameworkDir = null;
	private $publicDir = null;
	private $cacheFolder = 'cache';
	private $modulesFolder = 'modules';
	private $controllersFolder = 'controllers';
	private $modelsFolder = 'models';
	private $viewsFolder = 'views';
	private $logsFolder = 'logs';

	public function isDevMode(){
		$this->devMode = true;
	}

	public function setWebRoot($value){
		$this->webRoot = $value;
	}

	public function setSiteName($value){
		$this->siteName = $value;
	}

	public function setCookieLifeTime($time){
		$this->cookieLifeTime = $time;
	}

	public function setAppDir($dir){
		$this->appDir = $dir;
	}

	public function setFrameworkDir($dir){
		$this->frameworkDir = $dir;
	}

	public function setPublicDir($dir){
		$this->publicDir = $dir;
	}

	public function setCacheFolder($folder){
		$this->cacheFolder = $folder;
	}

	public function setModulesFolder($folder){
		$this->modulesFolder = $folder;
	}

	public function setControllersFolder($folder){
		$this->controllersFolder = $folder;
	}

	public function setModelsFolder($folder){
		$this->modelsFolder = $folder;
	}

	public function setViewsFolder($folder){
		$this->viewsFolder = $folder;
	}

	public function setLogsFolder($folder){
		$this->logsFolder = $folder;
	}

	public function includeSaveLogs(){
		$this->saveLogs = true;
	}

	public function init($namespace){
		ErrorHandler::$memory_usage = memory_get_usage();
		ErrorHandler::$start_time = microtime(true);

		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

		if(empty($this->frameworkDir)) $this->frameworkDir = __DIR__.DS;
		if(empty($this->appDir)) trigger_error(i18n::t('Variable "appDir" is not assigned. Use method setAppDir($dir)'),E_USER_WARNING);
		if(empty($this->publicDir)) $this->publicDir = $this->appDir.'public'.DS;

		define('DIT_APP_NAMESPACE', $namespace);
		define('DIT_DEV_MODE', $this->devMode);
		define('DIT_WEB_ROOT', $this->webRoot);
		define('DIT_SITE_NAME', $this->siteName);
		define('DIT_SAVE_LOGS', $this->saveLogs);
		define('DIT_COOKIE_LIFE_TIME', $this->cookieLifeTime);
		define('DIT_FRAMEWORK_DIR', $this->frameworkDir);
		define('DIT_APP_DIR', $this->appDir);
		define('DIT_PUBLIC_DIR', $this->publicDir);
		define('DIT_CACHE_FOLDER', $this->cacheFolder);
		define('DIT_MODULES_FOLDER', $this->modulesFolder);
		define('DIT_CONTROLLERS_FOLDER', $this->controllersFolder);
		define('DIT_MODELS_FOLDER', $this->modelsFolder);
		define('DIT_VIEWS_FOLDER', $this->viewsFolder);
		define('DIT_LOGS_FOLDER', $this->logsFolder);

		if(DIT_DEV_MODE==true){
			$cache = new Cache('System');
			$cache->clear(false);
		}
	}

	public function start(){
		new Dispatcher($this->rules);
	}

	public function rule($rule,$options=array()){
		$this->rules[DIT_WEB_ROOT.$rule] = $options;
	}

	public function rules($rules=array()){
		$outRules = array();
		foreach($rules as $k=>$rule){
			$outRules[DIT_WEB_ROOT.$k] = $rule;
		}
		$this->rules = array_merge($this->rules,$outRules);
	}
}