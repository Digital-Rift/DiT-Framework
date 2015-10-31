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

	public function set($key,$value,$mergeArray=false){
		if($mergeArray==true){
			if(isset($this->vars[$key]) AND is_array($this->vars[$key]) AND is_array($value)){
				$this->vars[$key] = array_merge($this->vars[$key],$value);
			}else{
				$this->vars[$key] = $value;
			}
		}else{
			$this->vars[$key] = $value;
		}
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

	public function setInGroup($group,$key,$value,$mergeArray=false){
		if(!isset($this->vars[$group])) $this->vars[$group] = array();
		if($mergeArray==true){
			if(isset($this->vars[$group][$key]) AND is_array($this->vars[$group][$key]) AND is_array($value)){
				$this->vars[$group][$key] = array_merge($this->vars[$group][$key],$value);
			}else{
				$this->vars[$group][$key] = $value;
			}
		}else{
			$this->vars[$group][$key] = $value;
		}
	}
}