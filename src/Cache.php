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
 * Class Cache
 * @package DiTFramework
 */
class Cache{
	private $cache = null;

	function __construct($file,$site_mode=false,$date_mode=false){
		if($site_mode!=false){
			$cache_dir = DIT_PUBLIC_DIR.'cache'.DS;
			$cache_site_dir = $cache_dir.DIT_SITE_NAME.DS;
			Files::makeDir($cache_dir);
			Files::makeDir($cache_site_dir);
			$this->cache = $cache_site_dir.$file;
		}else{
			if($date_mode){
				Files::makeDir(DIT_APP_DIR.DIT_CACHE_FOLDER.DS);
				$this->cache = DIT_APP_DIR.DIT_CACHE_FOLDER.DS.$file.'_'.DIT_SITE_NAME;
				$file_time = filemtime($this->cache);
				$next_time = strtotime('+2 day',$file_time);
				if($file_time>time()){
					unlink($this->cache);
				}
			}else{
				Files::makeDir(DIT_APP_DIR.DIT_CACHE_FOLDER.DS);
				$this->cache = DIT_APP_DIR.DIT_CACHE_FOLDER.DS.$file.'_'.DIT_SITE_NAME;
			}
		}
	}

	function exist(){
		if(file_exists($this->cache)){
			return true;
		}else{
			return false;
		}
	}

	function get(){
		$result = unserialize(file_get_contents($this->cache));
		return $result;
	}

	function save($data,$site_mode=false){
		if($site_mode!=false){
			if(!file_exists($this->cache)){
				file_put_contents($this->cache, $data);
			}
		}else{
			if(DIT_DEV_MODE==false){
				file_put_contents($this->cache, serialize($data));
			}
		}
	}

	function clear($clearSiteCache=true){
		Files::deleteDir(DIT_APP_DIR.DIT_CACHE_FOLDER.DS, true, array('readme'));
		if($clearSiteCache==true){
			Files::deleteDir(DIT_PUBLIC_DIR.'cache'.DS, true, array('readme'));
		}
	}
}
