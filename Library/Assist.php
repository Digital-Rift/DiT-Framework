<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Юрий Сергеевич Селезнев
 * @author Алексей Рубенович Калантарян
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
