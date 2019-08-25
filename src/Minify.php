<?php
/**
 * @project DIT Framework
 * @link http://digitalrift.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DITFramework;

/**
 * Class Minify
 * Минификация стилей и скриптов
 *
 * @package DITFramework
 */
class Minify{
	public function css($source){
		return $this->stripWhitespaces($this->stripLinebreaks($this->stripComments($source)));
	}

    public function html($buffer){
        $search = array(
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
            '/\>\s\</s',
        );
        $replace = array(
            '>',
            '<',
            '\\1',
            '><',
        );
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }

	private function stripLinebreaks($string){
		return preg_replace('/(\\\?[\n\r\t]+|\s{2,})/', '', $string);
	}

	private function stripComments($string){
		$protected = '(?<![\\\/\'":])';
		$multiline = '\/\*[^*]*\*+([^\/][^\*]*\*+)*\/';
		$pattern = $protected;
		$pattern .= $multiline;
		return preg_replace('#'.$pattern.'#', '', $string);
	}

	private function stripWhitespaces($string){
		$pattern = ';|:|,|\{|\}';
		return preg_replace('/\s*('.$pattern.')\s*/', '$1', preg_replace('/\(\s*(.*)\s*\)/', '($1)', $string));
	}
}