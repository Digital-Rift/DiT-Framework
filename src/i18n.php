<?php
/**
 * @project DiT Framework
 * @link http://www.dit-cms.org
 * @author Юрий Сергеевич Селезнев
 * @author Алексей Рубенович Калантарян
 */
namespace Core\Library;

/**
 * Class i18n
 * @package Core\Library
 */
class i18n {
	public static $i18n = array();

	public static function t($str, $options = null)	{
		if(!empty($options)){
			foreach($options as $k=>$v){
				if(preg_match('/::'.$k.'/is', $str)) {
					$str = preg_replace('/::'.$k.'/is', $v, $str);
				}
			}
		}
		if(isset(self::$i18n[$str])){
			$str = self::$i18n[$str];
		}
		return $str;
	}
}
