<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace DiTFramework\Errors;
use DiTFramework\i18n;

/**
 * Class ErrorHandler
 * @package DiTFramework\Errors
 */
class ErrorHandler {

	public static $errors = array();
	public static $fatal_error = array();

	public static function init(){
		ini_set('display_errors', 0);
		error_reporting(E_ALL | E_STRICT);
		set_error_handler(array('DiTFramework\Errors\ErrorHandler', 'errors'));
		set_exception_handler(array('DiTFramework\Errors\ErrorHandler', 'exceptions'));
		register_shutdown_function(array('DiTFramework\Errors\ErrorHandler', 'fatalErrors'));
	}

	public static function errors($type, $message, $file, $line){
		switch ($type) {
			case E_USER_WARNING:

				break;
			case E_USER_NOTICE:

				break;
			default:
				$type = i18n::t('Unknown error');
				break;
		}
		self::$errors[] = array(
			'type'=>$type,
			'message'=>$message,
			'file'=>$file,
			'line'=>$line
		);
		return false;
	}

	public static function exceptions(\Exception $exception){
		self::$errors[] = array(
			'type'=>i18n::t('Exception'),
			'message'=>$exception->getMessage(),
			'file'=>$exception->getFile(),
			'line'=>$exception->getLine()
		);
	}

	public static function fatalErrors(){
		$error = error_get_last();
		if (isset($error)){
			if($error['type']==E_USER_ERROR || $error['type']==E_ERROR || $error['type']==E_PARSE || $error['type']==E_COMPILE_ERROR || $error['type']==E_CORE_ERROR){
				self::$fatal_error = $error;
				header('Content-type: text/html; charset=utf-8');
				require(DIT_FRAMEWORK_DIR.'Errors'.DS.'fatal-error.phtml');
				die();
			}
		}
	}

	public static function showErrors(){
		if(!empty(ErrorHandler::$errors) AND DIT_DEV_MODE==true){
			require(DIT_FRAMEWORK_DIR.'Errors'.DS.'errors.phtml');
		}
	}

	public static function jsonErrors(){
		if(!empty(ErrorHandler::$errors) AND DIT_DEV_MODE==true){
			return ErrorHandler::$errors;
		}else{
			return false;
		}
	}
}