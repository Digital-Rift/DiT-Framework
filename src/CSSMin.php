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
 * Class CSSMin
 * @package DiTFramework
 */
class CSSMin{
	public static function minify($source){
		return self::stripWhitespaces(self::stripLinebreaks(self::stripComments($source)));
	}

	private static function stripLinebreaks($string){
		return preg_replace('/(\\\?[\n\r\t]+|\s{2,})/', '', $string);
	}

	private static function stripComments($string){
		$protected = '(?<![\\\/\'":])';
		$multiline = '\/\*[^*]*\*+([^\/][^\*]*\*+)*\/';
		$pattern = $protected;
		$pattern .= $multiline;
		return preg_replace('#'.$pattern.'#', '', $string);
	}

	private static function stripWhitespaces($string){
		$pattern = ';|:|,|\{|\}';
		return preg_replace('/\s*('.$pattern.')\s*/', '$1', preg_replace('/\(\s*(.*)\s*\)/', '($1)', $string));
	}
}