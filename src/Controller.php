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
 * Class Controller
 * @package DiTFramework
 */
class Controller extends Assist{
	public $view;
	public $query;
	public $json;
	public $status=200;
	public $type='html';
}