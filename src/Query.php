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
 * Class Query
 * Класс для управления запросами
 *
 * @package DITFramework
 */
class Query {
    public function get($key){
        if(isset(Storage::$query[$key])){
            return Storage::$query[$key];
        }else{
            return null;
        }
    }

    public function exist($key){
        if(isset(Storage::$query[$key])){
            return true;
        }else{
            return false;
        }
    }
}