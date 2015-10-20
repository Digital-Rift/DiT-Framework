<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Юрий Сергеевич Селезнев
 * @author Алексей Рубенович Калантарян
 */
namespace Core\Library;

/**
 * Class Cache
 * @package Core\Library
 */
class Cache{
	private $cache = null;

	function __construct($file,$site_mode=false){
		if($site_mode!=false){
			$cache_dir = BASE_DIR.PUBLIC_FOLDER.DS.'cache'.DS;
			$cache_site_dir = $cache_dir.SITE_NAME.DS;
			Files::makeDir($cache_dir);
			Files::makeDir($cache_site_dir);
			$this->cache = $cache_site_dir.$file;
		}else{
			Files::makeDir(BASE_DIR.CACHE_FOLDER.DS);
			$this->cache = BASE_DIR.CACHE_FOLDER.DS.$file.'_'.SITE_NAME;
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
			if(DEV_MODE==false){
				file_put_contents($this->cache, serialize($data));
			}
		}
	}

	function clear($clearSiteCache=true){
		Files::deleteDir(BASE_DIR.CACHE_FOLDER.DS, true, array('readme'));
		if($clearSiteCache==true){
			Files::deleteDir(BASE_DIR.PUBLIC_FOLDER.DS.'cache'.DS, true, array('readme'));
		}
	}
}
