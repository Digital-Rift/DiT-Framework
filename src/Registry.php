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
 * Class Registry
 * @package DiTFramework
 */
class Registry {
	protected $vars = array();

	public function get($key){
		if(isset($this->vars[$key])){
			return $this->vars[$key];
		}else{
			return false;
		}
	}

	public function set($key,$value){
		$this->vars[$key] = $value;
	}

	public function getInGroup($group,$key){
		if(isset($this->vars[$group][$key])){
			return $this->vars[$group][$key];
		}else{
			return false;
		}
	}

	public function getGroup($group){
		if(isset($this->vars[$group])){
			return $this->vars[$group];
		}else{
			return false;
		}
	}

	public function setInGroup($group,$key,$value){
		if(!isset($this->vars[$group])) $this->vars[$group] = array();
		$this->vars[$group][$key] = $value;
	}
}