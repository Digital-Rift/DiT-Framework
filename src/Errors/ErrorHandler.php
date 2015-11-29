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

	public static $log = array();

	public static function init(){
		ini_set('display_errors', 0);
		error_reporting(E_ALL | E_STRICT);
		set_error_handler(array('DiTFramework\Errors\ErrorHandler', 'errorsLog'));
		register_shutdown_function(array('DiTFramework\Errors\ErrorHandler', 'fatalErrors'));
	}

	public static function errorsLog($type, $message, $file, $line){
		self::$log['Errors'][] = array(
			'type'=>$type,
			'message'=>$message,
			'file'=>$file,
			'line'=>$line
		);
		return false;
	}


	public static function fatalErrors(){
		$error = error_get_last();
		if (isset($error)){
			if($error['type']==E_USER_ERROR || $error['type']==E_ERROR || $error['type']==E_PARSE || $error['type']==E_COMPILE_ERROR || $error['type']==E_CORE_ERROR){
				self::$log['Fatal-Error'] = $error;
				header('Content-type: text/html; charset=utf-8');
				require(DIT_FRAMEWORK_DIR.'Errors'.DS.'fatal-error.phtml');
				die();
			}
		}
	}

	public static function showErrors(){
		if(!empty(ErrorHandler::$log['Errors']) AND DIT_DEV_MODE==true){
			require(DIT_FRAMEWORK_DIR.'Errors'.DS.'errors.phtml');
		}
	}

	public static function jsonErrors(){
		if(!empty(ErrorHandler::$log['Errors']) AND DIT_DEV_MODE==true){
			return ErrorHandler::$log['Errors'];
		}else{
			return false;
		}
	}
}