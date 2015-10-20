<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace Core\Library;

/**
 * Class Assist
 * @package Core\Library
 */
class Assist{
	public $request;
	public $registry;

	public function __construct(){
		$this->request = Dispatcher::requestInstance();
		$this->registry = Dispatcher::registryInstance();
	}

	public function assign($key,$value){
		$this->registry->setInGroup('DiT-View',$key,$value);
	}

	public function redirect($link=null){
		Header::redirect($link);
	}

	public function reload(){
		Header::redirect($this->request->getRequestUri());
	}
}
