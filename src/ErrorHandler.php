<?php
/**
 * @project DIT Framework
 * @link http://digitalrift.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DITFramework;

/**
 * Class ErrorHandler
 * Обработчик ошибок
 *
 * @package DITFramework
 */
class ErrorHandler{
	public function init(){
		ini_set('display_errors', 0);
		error_reporting(E_ALL | E_STRICT);
		set_error_handler(array($this, 'logErrors'));
		register_shutdown_function(array($this, 'fatalError'));
	}

	public function logErrors($type, $message, $file, $line){
		Storage::$errors[] = array(
			'type'=>$type,
			'message'=>$message,
			'file'=>$file,
			'line'=>$line
		);
		return false;
	}

	public function fatalError(){
		$error = error_get_last();
		if (isset($error)){
			if($error['type']==E_USER_ERROR || $error['type']==E_ERROR || $error['type']==E_PARSE || $error['type']==E_COMPILE_ERROR || $error['type']==E_CORE_ERROR){
                if(ob_get_length()) ob_end_clean();
                $m = explode("\n",$error['message']);
                Storage::$fatalError = array(
                    'message'=>$m,
                    'file'=>$error['file'],
                    'line'=>$error['line'],
                );
                $template = new TemplateEngine();
                $template->render();
                $template->output();
				die;
			}
		}
	}
}