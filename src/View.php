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
 * Class View
 * @package DiTFramework
 */
class View extends Assist{

	protected $ext = '.phtml';
	public $vars;
	public $meta;

	public function render($view){
		if(!empty($view)){
			$this->compileMeta();
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
}