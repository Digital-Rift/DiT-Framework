<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Юрий Сергеевич Селезнев
 * @author Алексей Рубенович Калантарян
 */
namespace Core\Library;

/**
 * Class View
 * @package Core\Library
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
			require BASE_DIR.VIEWS_FOLDER.DS.$view.$this->ext;
		}else{
			trigger_error(i18n::t('View not found'));
		}
	}

	public function get($key){
		return $this->registry->getInGroup('DiT-View',$key);
	}
}