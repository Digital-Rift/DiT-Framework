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
 * Class Model
 * Вспомагательный класс для моделей
 *
 * @package DITFramework
 * @property Session $session
 * @property Popup $popup
 * @property Form $form
 * @property Query $query
 * @property Template $template
 * @property Files $files
 */
class Model{
    public $session;
    public $popup;
    public $form;
    public $query;
    public $template;
    public $files;

    public function __construct(){
        $this->session = Instance::getSessionInstance();
        $this->popup = Instance::getPopupInstance();
        $this->form = Instance::getFormInstance();
        $this->query = Instance::getQueryInstance();
        $this->template = Instance::getTemplateInstance();
        $this->files = Instance::getFilesInstance();
    }

	public function table($table){
		$db = new Db();
		$db->connect(
			Storage::$dbName,
			Storage::$dbHost,
			Storage::$dbUser,
			Storage::$dbPassword,
			$table,
            Storage::$dbTablePrefix,
            Storage::$dbCharset,
            Storage::$dbDriver
		);
		return $db;
	}
	
    function loadModel($modelName){
        if(!isset($this->$modelName)) {
            $this->$modelName = Instance::getModelInstance($modelName);
        }
    }

    public function getWebRoot(){
        return Storage::$webRoot;
    }
}