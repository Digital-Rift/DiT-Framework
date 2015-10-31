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
 * Class Session
 * @package DiTFramework
 */
class Session {
	public $id = null;

	public function __construct(){
		if(!session_id()) session_start();
		$this->id = session_id();
	}

	public function get($key,$value=null){
		if(!empty($value)){
			if(isset($_SESSION[$key][$value])){
				return $_SESSION[$key][$value];
			}else{
				return null;
			}
		}else{
			if(isset($_SESSION[$key])){
				return $_SESSION[$key];
			}else{
				return null;
			}
		}
	}

	public function exist($key,$value=null){
		if(!empty($value)){
			if(isset($_SESSION[$key][$value])){
				return true;
			}else{
				return false;
			}
		}else{
			if(isset($_SESSION[$key])){
				return true;
			}else{
				return false;
			}
		}
	}

	public function regenerate(){
		if(session_id()) session_regenerate_id();
		$this->id = session_id();
	}

	public function destroy(){
		if(session_id()) session_destroy();
		$this->id = null;
	}
}