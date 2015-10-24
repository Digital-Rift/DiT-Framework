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

	public function render($view){
		if(!empty($view)){
			$this->vars = $this->registry->getGroup('DiT-View');
			if($this->vars!=false){
				extract((array)$this->vars);
			}
			require APP_DIR.VIEWS_FOLDER.DS.$view.$this->ext;
		}else{
			trigger_error(i18n::t('View not found'));
		}
	}

	public function get($key){
		return $this->registry->getInGroup('DiT-View',$key);
	}
}