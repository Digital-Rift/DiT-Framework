<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace DiTFramework\Http;

/**
 * Class Request
 * @package DiTFramework\Http
 */
class Request {
	protected $data = array(
		'method'=>null,
		'request_uri'=>null,
		'url'=>null,
		'server_ip'=>null,
		'user_ip'=>null,
		'host'=>null,
		'query'=>null
	);

	public function __construct(){
		if(is_array($_SERVER)){
			$uri = parse_url($_SERVER['REQUEST_URI']);
			$path = null;
			if(isset($uri['path'])) $path = urldecode($uri['path']);
			$this->data = array(
				'method'=>$_SERVER['REQUEST_METHOD'],
				'request_uri'=>$_SERVER['REQUEST_URI'],
				'url'=>$path,
				'server_ip'=>$_SERVER['SERVER_ADDR'],
				'user_ip'=>$_SERVER['REMOTE_ADDR'],
				'host'=>$_SERVER['SERVER_NAME'],
				'query'=>$_REQUEST
			);
		}
	}

	public function getMethod(){
		return $this->data['method'];
	}

	public function getRequestUri(){
		return $this->data['request_uri'];
	}

	public function getUrl(){
		return $this->data['url'];
	}

	public function getServerIp(){
		return $this->data['server_ip'];
	}

	public function getUserIp(){
		return $this->data['user_ip'];
	}

	public function getHost(){
		return $this->data['host'];
	}

	public function getQuery($key){
		if(isset($this->data['query'][$key])){
			return $this->data['query'][$key];
		}else{
			return false;
		}
	}

	public function existQuery($key){
		if(isset($this->data['query'][$key])){
			return true;
		}else{
			return false;
		}
	}

	public function getQueryAll(){
		return $this->data['query'];
	}

	public function getCookie($key){
		if(isset($_COOKIE[$key])){
			return $_COOKIE[$key];
		}else{
			return false;
		}
	}

	public function existCookie($key){
		if(isset($_COOKIE[$key])){
			return true;
		}else{
			return false;
		}
	}

	public function setCookie($key,$value){
		setcookie($key, $value, time()+DIT_COOKIE_LIFE_TIME, "/", $this->data['host']);
	}

	public function deleteCookie($key){
		setcookie($key, '', time()-3600, "/", $this->data['host']);
	}
}