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
 * Class Assist
 * @package DiTFramework
 */
class Assist{
	public $request;
	public $registry;
	public $session;

	public function __construct(){
		$this->request = Dispatcher::requestInstance();
		$this->registry = Dispatcher::registryInstance();
		$this->session = Dispatcher::sessionInstance();
	}

	public function assign($key,$value){
		$this->registry->setInGroup('DiT-View',$key,$value);
	}

	public function template($key,$value=null){
		if(empty($value)) $value = $key;
		$this->registry->setInGroup('DiT-Templates',$key,$value);
	}

	public function redirect($link=null){
		Header::redirect($link);
	}

	public function reload(){
		Header::redirect($this->request->getRequestUri());
	}

	public function setMeta($key, $value){
		$this->registry->setInGroup('DiT-Meta',$key,$value);
	}

	public function setMetaTitle($value){
		$this->registry->setInGroup('DiT-Meta','title',$value);
	}

	public function setMetaDescription($value){
		$this->registry->setInGroup('DiT-Meta','description',$value);
	}

	public function setMetaKeywords($value){
		$this->registry->setInGroup('DiT-Meta','keywords',$value);
	}

	public function setLoadPackageScripts($array){
		if(is_array($array)){
			$this->registry->set('DiT-ScriptsLoadPackages',$array,true);
		}
	}

	public function setLoadPackageStyles($array){
		if(is_array($array)){
			$this->registry->set('DiT-StylesLoadPackages',$array,true);
		}
	}

	public function setScripts($array,$package='public'){
		if(is_array($array)){
			$files = array();
			$created = array();
			foreach($array as $v){
				$file = DIT_APP_DIR.$v;
				if (file_exists($file)){
					$files[] = $file;
					$created[] = filemtime($file);
				}
			}
			$this->registry->setInGroup('DiT-ScriptsFiles',$package,$files,true);
			$this->registry->setInGroup('DiT-ScriptsCreated',$package,$created,true);
		}
	}

	public function setStyles($array,$package='public'){
		if(is_array($array)){
			$files = array();
			$created = array();
			foreach($array as $v){
				$file = DIT_APP_DIR.$v;
				if (file_exists($file)){
					$files[] = $file;
					$created[] = filemtime($file);
				}
			}
			$this->registry->setInGroup('DiT-StylesFiles',$package,$files,true);
			$this->registry->setInGroup('DiT-StylesCreated',$package,$created,true);
		}
	}
}
