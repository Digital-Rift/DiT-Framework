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
 * Class Header
 * @package DiTFramework
 */
class Header {
	public static $codes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested range not satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out'
	);

	public static function setStatus($status){
		if(isset(self::$codes[$status])){
			header("Status: ".$status." ".self::$codes[$status]);
			header("HTTP/1.0 ".$status." ".self::$codes[$status]);
			switch ($status){
				case 404:
				case 403:
					$file = DIT_APP_DIR.DIT_CONTROLLERS_FOLDER.DS.'ErrorsController.php';
					$controller = DIT_APP_NAMESPACE.'\\'.DIT_CONTROLLERS_FOLDER.'\\'.'ErrorsController';
					if(file_exists($file)){
						$controller = new $controller();
						$action = 'error'.$status.'Action';
						if(method_exists($controller,$action)) {
							$controller->$action();
							header('Content-type: text/html; charset=utf-8');
							if($controller->view!=null){
								$view = new View();
								$view->render($controller->view);
							}
						}
					}
					break;
			}
			return true;
		}else{
			return false;
		}
	}

	public static function redirect($link){
		if(!empty($link)){
			header('Location: '.$link);
			die();
		}
	}
}
