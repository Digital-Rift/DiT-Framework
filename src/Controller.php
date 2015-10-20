<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Юрий Сергеевич Селезнев
 * @author Алексей Рубенович Калантарян
 */
namespace Core\Library;

/**
 * Class Controller
 * @package Core\Library
 */
class Controller extends Assist{
	public $view;
	public $query;
	public $json;
	public $status=200;
	public $type='html';
}