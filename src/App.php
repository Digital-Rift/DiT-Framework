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
 * Class App
 * @package DiTFramework
 */
class App {
	private $rules = array();
	private $devMode = false;
	private $devTriggerHost = null;

	public static $start_time;
	public static $memory_usage;

	function __construct(){
		self::$memory_usage = memory_get_usage();
		self::$start_time = microtime(true);
		Dispatcher::configInstance();
	}

	public function start($namespace){
		if(!defined('DIT_DS')) define('DIT_DS', DIRECTORY_SEPARATOR);
		if(strlen($this->devTriggerHost)>0) {
			if($_SERVER['HTTP_HOST']==$this->devTriggerHost OR $_SERVER['HTTP_HOST']=='www.'.$this->devTriggerHost){
				$this->devMode = true;
			}
		}
		define('DIT_DEV_MODE', $this->devMode);



		/*define('DIT_APP_NAMESPACE', $namespace);
		define('DIT_WEB_ROOT', Config::get('webRoot'));

		if(empty($this->frameworkDir)) $this->frameworkDir = __DIR__.DS;
		if(empty($this->appDir)) trigger_error(i18n::t('Variable "appDir" is not assigned. Use method setAppDir($dir)'),E_USER_WARNING);
		if(empty($this->publicDir)) $this->publicDir = $this->appDir.'public'.DS;


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

		define('DIT_DB_DRIVER', $this->dbDriver);
		define('DIT_DB_NAME', $this->dbName);
		define('DIT_DB_HOST', $this->dbHost);
		define('DIT_DB_USER', $this->dbUser);
		define('DIT_DB_PASSWORD', $this->dbPassword);
		define('DIT_DB_TABLE_PREFIX', $this->dbTablePrefix);
		define('DIT_DB_CHARSET', $this->dbCharset);

		if(DIT_DEV_MODE==true){
			$cache = new Cache('System');
			$cache->clear(false);
		}

		new Dispatcher($this->rules);
		Log::save();*/
	}

	public function isDevMode(){
		$this->devMode = true;
	}

	public function devTriggerHost($host){
		$this->devTriggerHost = $host;
	}

	public function rule($rule,$options=array()){
		if($rule=='/') $rule = '';
		$this->rules[DIT_WEB_ROOT.$rule] = $options;
	}

	public function rules($rules=array()){
		$outRules = array();
		foreach($rules as $rule=>$options){
			if($rule=='/') $rule = '';
			$outRules[DIT_WEB_ROOT.$rule] = $options;
		}
		$this->rules = array_merge($this->rules,$outRules);
	}

	public function setWebRoot($value){

	}

	public function setDbConfig($driver,$db_name,$host,$user,$password,$table_prefix=null,$charset='utf8'){
		$this->dbDriver = $driver;
		$this->dbName = $db_name;
		$this->dbHost = $host;
		$this->dbUser = $user;
		$this->dbPassword = $password;
		$this->dbTablePrefix = $table_prefix;
		$this->dbCharset = $charset;
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

	public function saveLogs(){
		$this->saveLogs = true;
	}
}