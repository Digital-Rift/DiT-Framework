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
 * Class Session
 * Класс для управления сессиями
 *
 * @package DITFramework
 */
class Session {
	function set($key,$value){
		$_SESSION[$key] = $value;
	}

	function get($key,$value=null){
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

	function exist($key,$value=null){
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

	function getId(){
	    return session_id();
    }

    function regenerate(){
        if(session_id()) session_regenerate_id();
    }

    function destroy(){
        if(session_id()) session_destroy();
    }
}