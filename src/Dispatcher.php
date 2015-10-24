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
 * Class Dispatcher
 * @package DiTFramework
 */
class Dispatcher {

	protected $controller;
	protected static $_requestInstance;
	protected static $_registryInstance;

	public static function requestInstance() {
		if (null === self::$_requestInstance) {
			self::$_requestInstance = new Request();
		}
		return self::$_requestInstance;
	}

	public static function registryInstance() {
		if (null === self::$_registryInstance) {
			self::$_registryInstance = new Registry();
		}
		return self::$_registryInstance;
	}

	public function __construct($rules=array()){
		$uri = parse_url($_SERVER['REQUEST_URI']);
		$path = null;
		if(isset($uri['path'])) $path = urldecode($uri['path']);
		$request = array(
			'method'=>$_SERVER['REQUEST_METHOD'],
			'request_uri'=>$_SERVER['REQUEST_URI'],
			'url'=>$path,
			'server_ip'=>$_SERVER['SERVER_ADDR'],
			'user_ip'=>$_SERVER['REMOTE_ADDR'],
			'host'=>$_SERVER['SERVER_NAME'],
			'query'=>$_REQUEST
		);

		$requestInstance = Dispatcher::requestInstance();
		$requestInstance->set($request);

		Dispatcher::registryInstance();

		$router = new Router();
		if(count($rules)>0)$router->rules($rules);
		$result = $router->start();

		if($result['status']==200){
			switch($result['type']){
				case 'redirect':
					Header::redirect($result['redirect']);
					break;
				case 'symlink':
					$this->loadSymlink($result);
					break;
				case 'controller':
					$this->loadController($result);
					break;
			}
		}else{
			Header::setStatus($result['status']);
		}
	}

	private function loadController($result){
		$result['controller'] = ucfirst($result['controller']);
		$result['module'] = ucfirst($result['module']);
		if(strlen($result['module'])>0){
			$file = APP_DIR.MODULES_FOLDER.DS.$result['module'].DS.CONTROLLERS_FOLDER.DS.$result['controller'].'Controller.php';
			$controller = APP_NAMESPACE.'\\'.MODULES_FOLDER.'\\'.$result['module'].'\\'.CONTROLLERS_FOLDER.'\\'.$result['controller'].'Controller';
		}else{
			$file = APP_DIR.CONTROLLERS_FOLDER.DS.$result['controller'].'Controller.php';
			$controller = APP_NAMESPACE.'\\'.CONTROLLERS_FOLDER.'\\'.$result['controller'].'Controller';
		}
		if(file_exists($file)){
			$this->controller = new $controller();
			$this->controller->query = $result['options'];
			$action = $result['action'].'Action';
			if(method_exists($this->controller,$action)){
				$this->controller->$action();
				Header::setStatus($this->controller->status);
				switch($this->controller->type){
					case 'json':
						header('Content-type: application/json; charset=utf-8');
						$errors = ErrorHandler::jsonErrors();
						if($errors==false){
							echo json_encode($this->controller->json);
						}else{
							echo json_encode(array(
								'dit_errors'=>$errors
							));
						}
						break;
					case 'html':
						header('Content-type: text/html; charset=utf-8');
						if($this->controller->view!=null){
							$view = new View();
							$view->render($this->controller->view);
						}
						ErrorHandler::showErrors();
						break;
				}
				ErrorHandler::logTime();
			}else{
				Header::setStatus(404);
			}
		}else{
			Header::setStatus(404);
		}
	}

	private function loadSymlink($result){
		$result['symlink'] = str_replace("/", DS, $result['symlink']);
		if(isset($result['options']['query'])){
			$file = $result['symlink'].$result['options']['query'];
			if(file_exists($file)){
				$type = Files::getType($file);
				header('Content-type: '.$type.'; charset=utf-8');
				header('Content-Length: ' . filesize($file));
				readfile($file);
				die;
			}else{
				Header::setStatus(404);
			}
		}
	}
}