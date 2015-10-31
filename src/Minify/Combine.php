<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace DiTFramework\Minify;

/**
 * Class Combine
 * @package DiTFramework\Minify
 */
class Combine{

	public static $scripts = array();
	public static $styles = array();

	public static function setScripts($src=array(),$template=null,$dir=SITE_DIR,$priority='normal'){
		if($template==null){
			if(is_array($src)){
				foreach($src as $v){
					$v = $dir.$v;
					if (file_exists($v)){
						if($priority=='high'){
							array_unshift(self::$scripts['public']['files'],$v);
							array_unshift(self::$scripts['public']['created'],filemtime($v));
						}else{
							self::$scripts['public']['files'][] = $v;
							self::$scripts['public']['created'][] = filemtime($v);
						}
					}
				}
			}
		}else{
			if(is_array($src)){
				foreach($src as $v){
					$v = $dir.$v;
					if (file_exists($v)){
						if($priority=='high'){
							array_unshift(self::$scripts[$template]['files'],$v);
							array_unshift(self::$scripts[$template]['created'],filemtime($v));
						}else{
							self::$scripts[$template]['files'][] = $v;
							self::$scripts[$template]['created'][] = filemtime($v);
						}
					}
				}
			}
		}
	}

	public static function setStyles($src=array(),$template=null,$dir=SITE_DIR,$priority='normal'){
		if($template==null){
			if(is_array($src)){
				foreach($src as $v){
					$v = $dir.$v;
					if (file_exists($v)){
						if($priority=='high'){
							array_unshift(self::$styles['public']['files'],$v);
							array_unshift(self::$styles['public']['created'],filemtime($v));
						}else{
							self::$styles['public']['files'][] = $v;
							self::$styles['public']['created'][] = filemtime($v);
						}
					}
				}
			}
		}else{
			if(is_array($src)){
				foreach($src as $v){
					$v = $dir.$v;
					if (file_exists($v)){
						if($priority=='high'){
							array_unshift(self::$styles[$template]['files'],$v);
							array_unshift(self::$styles[$template]['created'],filemtime($v));
						}else{
							self::$styles[$template]['files'][] = $v;
							self::$styles[$template]['created'][] = filemtime($v);
						}
					}
				}
			}
		}
	}

	public static function start(){
		$registry = Registry::viewInstance();

		foreach(self::$scripts as $key=>$scripts){
			if(isset($scripts['files']) AND count($scripts['files'])>0){
				$name = 'scripts_'.$key;
				$scripts_hash = md5(serialize($scripts['created']));
				$cache = new Cache($name.'_'.$scripts_hash.'.js',true);
				if(!$cache->exist()){
					$scripts_min = null;
					array_map("unlink", glob(SITE_DIR.'cache'.DS.$name.'_*'));
					foreach($scripts['files'] as $script){
						$scripts_min .= file_get_contents($script)."\n";
					}
					$scripts_min = JSMin::minify($scripts_min);
					$cache->save($scripts_min,true);
				}
				$registry->scripts[$key] = '/cache/'.$name.'_'.$scripts_hash.'.js';
			}
		}
		foreach(self::$styles as $key=>$styles){
			if(isset($styles['files']) AND count($styles['files'])>0){
				$name = 'styles_'.$key;
				$styles_hash = md5(serialize($styles['created']));
				$cache = new Cache($name.'_'.$styles_hash.'.css',true);
				if(!$cache->exist()){
					$styles_min = null;
					array_map("unlink", glob(SITE_DIR.'cache'.DS.$name.'_*'));
					foreach($styles['files'] as $style){
						$styles_min .= file_get_contents($style)."\n";
					}
					$styles_min = CSSMin::minify($styles_min);
					$cache->save($styles_min,true);
				}
				$registry->styles[$key] = '/cache/'.$name.'_'.$styles_hash.'.css';
			}
		}
	}
}
