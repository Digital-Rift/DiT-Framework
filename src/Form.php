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
 * Class Form
 *
 * @package DITFramework
 */
class Form{
    public function setSuccessMessage($message){
        Storage::$form = true;
        Storage::$formSuccessMessage = $message;
    }
    public function setErrorMessage($message){
        Storage::$form = true;
        Storage::$formErrorMessage = $message;
    }
    public function setSuccessCallback($callback_function){
        Storage::$form = true;
        Storage::$formSuccessCallback = $callback_function;
    }
}