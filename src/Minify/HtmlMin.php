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
 * Class HtmlMin
 * @package DiTFramework\Minify
 */
class HtmlMin{
	public static function minify($buffer){
		$search = array(
			'/\>[^\S ]+/s',
			'/[^\S ]+\</s',
			'/(\s)+/s'
		);
		$replace = array(
			'>',
			'<',
			'\\1'
		);
		$buffer = preg_replace($search, $replace, $buffer);
		return $buffer;
	}
}
