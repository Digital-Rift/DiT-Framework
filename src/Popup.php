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
 * Class Popup
 * Вспомагательный класс для моделей
 *
 * @package DITFramework
 */
class Popup{
    public function setTitle($title){
        Storage::$popup = true;
        Storage::$popupTitle = $title;
    }
    public function setClass($class){
        Storage::$popup = true;
        Storage::$popupClass = $class;
    }
    public function hideClose(){
        Storage::$popup = true;
        Storage::$popupHideClose = true;
    }
    public function setContent($content){
        Storage::$popup = true;
        Storage::$popupContent = $content;
    }
    public function setContentTemplate($template){
        Storage::$popup = true;
        Storage::$mainTemplate = $template;
        $templateClass = new TemplateEngine();
        $templateClass->render();
        Storage::$popupContent = $templateClass->getRenderHtml();
    }
    public function setForm($formAction=null){
        Storage::$popup = true;
        Storage::$popupForm = true;
        Storage::$popupFormAction = Storage::$webRoot.$formAction;
    }
    public function setBtnOneTitle($title){
        Storage::$popup = true;
        Storage::$popupBtnOne = $title;
    }
    public function setBtnTwoTitle($title){
        Storage::$popup = true;
        Storage::$popupBtnTwo = $title;
    }
}