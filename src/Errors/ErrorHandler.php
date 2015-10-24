<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace DiTFramework\Errors;

/**
 * Class ErrorHandler
 * @package DiTFramework\Errors
 */
class ErrorHandler {

	public static $start_time;
	public static $memory_usage;

	public static $log = array();

	public static function fatalErrors(){
		$error = error_get_last();
		if (isset($error)){
			if($error['type']==E_USER_ERROR || $error['type']==E_ERROR || $error['type']==E_PARSE || $error['type']==E_COMPILE_ERROR || $error['type']==E_CORE_ERROR){
				self::$log['Fatal-Error'] = $error;
				header('Content-type: text/html; charset=utf-8');
				require(FRAMEWORK_DIR.'Errors'.DS.'fatal-error.phtml');
				die();
			}
		}
	}

	public static function showErrors(){
		if(!empty(ErrorHandler::$log['Errors']) AND DEV_MODE==true){
			require(FRAMEWORK_DIR.'Errors'.DS.'errors.phtml');
		}
	}

	public static function logTime(){
		if(SAVE_LOGS){
			$file = APP_DIR.LOGS_FOLDER.DS.'core_time_'.SITE_NAME.'.log';
			$time = round(microtime(true)-ErrorHandler::$start_time, 4);
			$memory = round((memory_get_usage()-ErrorHandler::$memory_usage)/1024/1024,2);
			$memory_peak = round(memory_get_peak_usage()/1024/1024,2);
			file_put_contents($file,date('c').'; Ip: '.$_SERVER['REMOTE_ADDR'].'; Time: '.$time.'; Memory: '.$memory.'Mb; Peak memory: '.$memory_peak.'Mb'."\n",FILE_APPEND);
		}
	}

	public static function jsonErrors(){
		if(!empty(ErrorHandler::$log['Errors']) AND DEV_MODE==true){
			return ErrorHandler::$log['Errors'];
		}else{
			return false;
		}
	}
}