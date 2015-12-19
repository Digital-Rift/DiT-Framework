<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */
namespace DiTFramework;
use JsMin\Minify;

/**
 * Class View
 * @package DiTFramework
 */
class View extends Assist{

	protected $ext = '.phtml';
	public $vars;
	public $meta;
	public $scripts = array();
	public $styles = array();

	public function render($view){
		if(!empty($view)){
			$this->compileMeta();
			$this->compileStylesAndScripts();
			$this->vars = $this->registry->getGroup('DiT-View');
			if($this->vars!=false){
				extract((array)$this->vars);
			}
			require DIT_APP_DIR.DIT_VIEWS_FOLDER.DS.$view.$this->ext;
		}else{
			trigger_error(i18n::t('View not found'));
		}
	}

	public function get($key){
		return $this->registry->getInGroup('DiT-View',$key);
	}

	public function getMeta(){
		return $this->meta;
	}

	public function getScriptsVars(){
		$vars = $this->registry->get('DiT-ScriptsVar');
		if(DIT_DEV_MODE){
			$dev_mode = 'true';
		}else{
			$dev_mode = 'false';
		}
		$out = '<script>var DIT_WEB_ROOT = \''.DIT_WEB_ROOT.'\';var DIT_DEV_MODE = '.$dev_mode.';';
		if(is_array($vars)){
			foreach($vars as $k=>$v){
				if(is_array($v)){
					$out .= 'var '.$k.' = '.json_encode($v).';';
				}else{
					$out .= 'var '.$k.' = \''.$v.'\';';
				}
			}
		}
		$out .= '</script>';
		return $out;
	}

	public function getScripts(){
		if(count($this->scripts)>0){
			$out = null;
			if(isset($this->scripts['public'])) $out .= $this->scripts['public'];
			$load = $this->registry->get('DiT-ScriptsLoadPackages');
			if(is_array($load)){
				foreach($load as $package){
					if(isset($this->scripts[$package]) AND $package!='public') $out .= $this->scripts[$package];
				}
			}
			return $out;
		}else{
			return null;
		}
	}

	public function getStyles(){
		if(count($this->styles)>0){
			$out = null;
			if(isset($this->styles['public'])) $out .= $this->styles['public'];
			$load = $this->registry->get('DiT-StylesLoadPackages');
			if(is_array($load)){
				foreach($load as $package){
					if(isset($this->styles[$package]) AND $package!='public') $out .= $this->styles[$package];
				}
			}
			return $out;
		}else{
			return null;
		}
	}

	public function getPackageScripts($package='public'){
		if(isset($this->scripts[$package])){
			return $this->scripts[$package];
		}else{
			return null;
		}
	}

	public function getPackageStyles($package='public'){
		if(isset($this->styles[$package])){
			return $this->styles[$package];
		}else{
			return null;
		}
	}

	public function getTemplate($key){
		$template = $this->registry->getInGroup('DiT-Templates',$key);
		$file = DIT_APP_DIR.DIT_VIEWS_FOLDER.DS.$template.$this->ext;
		if(file_exists($file)){
			$this->vars = $this->registry->getGroup('DiT-View');
			if($this->vars!=false){
				extract((array)$this->vars);
			}
			require $file;
		}
	}

	public function existTemplate($key){
		$template = $this->registry->getInGroup('DiT-Templates',$key);
		if($template!=false){
			$file = DIT_APP_DIR.DIT_VIEWS_FOLDER.DS.$template.$this->ext;
			if(file_exists($file)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	private function compileMeta(){
		$meta = $this->registry->getGroup('DiT-Meta');
		$result = '<meta charset="utf-8" />'."\n";
		$result .= "\t".'<meta name="viewport" content="width=device-width, initial-scale=1.0" />'."\n";
		if(is_array($meta)){
			foreach($meta as $k=>$v){
				//TODO: og:meta tags
				switch($k){
					case 'title':
						$result .= "\t".'<title>'.$v.'</title>'."\n";
						break;
					default:
						$result .= "\t".'<meta name="'.$k.'" content="'.$v.'" />'."\n";
				}
			}
		}
		$this->meta = $result;
	}

	private function compileStylesAndScripts(){
		$scriptsFiles = $this->registry->getGroup('DiT-ScriptsFiles');
		$scriptsCreated = $this->registry->getGroup('DiT-ScriptsCreated');
		if(is_array($scriptsFiles)) {
			foreach ($scriptsFiles as $package => $files) {
				if (isset($scriptsCreated[$package])) {
					$scripts_hash = md5(serialize($scriptsCreated[$package]));
					$name = 'scripts_' . $package . '_' . $scripts_hash . '.js';
					$cache = new Cache($name, true);
					if (!$cache->exist()) {
						$scripts_min = null;
						array_map("unlink", glob(DIT_PUBLIC_DIR . 'cache' . DS . DIT_SITE_NAME . DS . 'scripts_' . $package . '_*'));
						foreach ($files as $file) {
							$scripts_min .= file_get_contents($file) . "\n";
						}
						$scripts_min = Minify::minify($scripts_min);
						$cache->save($scripts_min, true);
					}
					$this->scripts[$package] = '<script src="' . DIT_WEB_ROOT . 'cache/' . DIT_SITE_NAME . '/' . $name . '"></script>' . "\n";
				}
			}
		}

		$stylesFiles = $this->registry->getGroup('DiT-StylesFiles');
		$stylesCreated = $this->registry->getGroup('DiT-StylesCreated');
		if(is_array($stylesFiles)) {
			foreach ($stylesFiles as $package => $files) {
				if (isset($stylesCreated[$package])) {
					$styles_hash = md5(serialize($stylesCreated[$package]));
					$name = 'styles_' . $package . '_' . $styles_hash . '.css';
					$cache = new Cache($name, true);
					if (!$cache->exist()) {
						$styles_min = null;
						array_map("unlink", glob(DIT_PUBLIC_DIR . 'cache' . DS . DIT_SITE_NAME . DS . 'styles_' . $package . '_*'));
						foreach ($files as $file) {
							$styles_min .= file_get_contents($file) . "\n";
						}
						$styles_min = CSSMin::minify($styles_min);
						$cache->save($styles_min, true);
					}
					$this->styles[$package] = '<link rel="stylesheet" href="' . DIT_WEB_ROOT . 'cache/' . DIT_SITE_NAME . '/' . $name . '" />' . "\n";
				}
			}
		}

		$lessFiles = $this->registry->getGroup('DiT-LessFiles');
		$lessCreated = $this->registry->getGroup('DiT-LessCreated');
		if(is_array($lessFiles)) {
			foreach ($lessFiles as $package => $files) {
				if (isset($lessCreated[$package])) {
					$less_hash = md5(serialize($lessCreated[$package]));
					$name = 'less_' . $package . '_' . $less_hash . '.css';
					$cache = new Cache($name, true);
					if (!$cache->exist()) {
						$less_min = null;
						array_map("unlink", glob(DIT_PUBLIC_DIR . 'cache' . DS . DIT_SITE_NAME . DS . 'less_' . $package . '_*'));
						foreach ($files as $file) {
							$less_min .= file_get_contents($file) . "\n";
						}
						$less = new \lessc();
						try {
							$dit_less = '@DIT_WEB_ROOT: "'.DIT_WEB_ROOT.'";'."\n";
							$less_min = $less->compile($dit_less.$less_min);
						} catch (\Exception $e) {
							trigger_error('Less error: '. $e->getMessage());
						}
						$less_min = CSSMin::minify($less_min);
						$cache->save($less_min, true);
					}
					if(isset($this->styles[$package])){
						$this->styles[$package] .= '<link rel="stylesheet" href="' . DIT_WEB_ROOT . 'cache/' . DIT_SITE_NAME . '/' . $name . '" />' . "\n";
					}else{
						$this->styles[$package] = '<link rel="stylesheet" href="' . DIT_WEB_ROOT . 'cache/' . DIT_SITE_NAME . '/' . $name . '" />' . "\n";
					}
				}
			}
		}
	}
}