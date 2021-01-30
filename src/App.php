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
 * Class App
 * Инициализация и запуск приложения
 *
 * @package DITFramework
 * @property AppConfig $configApp
 * @property ErrorHandler $errorHandler
 * @property Files $Files
 */
class App{
    private $configApp;
    private $errorHandler;
    private $status = 200;
    private $mainTemplate;
    private $outputTemplate;
    private $Files;

	function __construct($namespaceApp,$namespacePlugins='Plugins'){
        if(!session_id()) session_start();
        $this->Files = new Files();
        Storage::$namespaceApp = $namespaceApp;
        Storage::$namespacePlugins = $namespacePlugins;
        Storage::$frameworkTemplatesDir = __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
        Storage::$frameworkAssetsDir = __DIR__.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR;
	    // инициализация обработчика ошибок
	    $this->errorHandler = new ErrorHandler();
        $this->errorHandler->init();

        $this->initRequest();

        // подключение файла настроек
        $configAppClass = '\\'.Storage::$namespaceApp.'\\Config';
        $this->configApp = new $configAppClass();
        if(method_exists($this->configApp,Storage::$release)){
            $release = Storage::$release;
            $this->configApp->$release();
        }
        $reflection = new \ReflectionClass($configAppClass);
        Storage::$appDir = dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR;

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
        if(isset($backtrace[0]['file'])){
            Storage::$publicDir = dirname($backtrace[0]['file']) . DIRECTORY_SEPARATOR;
        }else{
            trigger_error('Unable to determine the path to public directory');
        }
        
        if(count(Storage::$assetsLinks)>0){
            foreach (Storage::$assetsLinks as $item){
                Storage::$rules[$item['name'].'/{file:all}'] = array('symlink'=>$item['dir']);
            }
        }

        // заруск роутера
        $router = new Router();
        $result = $router->start();

        $render = true;
        if($result['status']==200){
            switch($result['type']){
                case 'redirect':
                    $this->redirect($result['redirect']);
                    $render = false;
                    break;
                case 'symlink':
                    $this->loadSymlink($result);
                    break;
                case 'controller':
                    $render = $this->loadController($result);
                    break;
            }
        }else{
            $this->setStatus(404);
        }
        if($render==true){
            $template = new TemplateEngine();
            switch ($this->status){
                case 404:
                    Storage::$metaTitle = '404 - Not found';
                    Storage::$mainTemplate = $this->status;
                    break;
                case 403:
                    Storage::$metaTitle = '403 - Forbidden';
                    Storage::$mainTemplate = $this->status;
                    break;
                default:
                    if(!empty($this->mainTemplate)) Storage::$mainTemplate = $this->mainTemplate;
                    break;
            }
            if(!empty($this->outputTemplate)) Storage::$outputTemplate = $this->outputTemplate;
            $template->render();
            $template->output();
        }
	}

    /**
     * Определение запроса пользователя
     */
    private function initRequest(){
        $uri = parse_url($_SERVER['REQUEST_URI']);
        $path = null;
        if(isset($uri['path'])) $path = urldecode($uri['path']);
        Storage::$method = $_SERVER['REQUEST_METHOD'];
        Storage::$requestUri = $_SERVER['REQUEST_URI'];
        Storage::$serverIp = $_SERVER['SERVER_ADDR'];
        Storage::$userIp = $_SERVER['REMOTE_ADDR'];
        Storage::$host = $_SERVER['SERVER_NAME'];
        Storage::$url = $path;
        if(count($_REQUEST)>0){
            foreach ($_REQUEST as $key=>$request){
                Storage::$query[$key] = $request;
            }
        }
    }

    /**
     * Загрузка контроллера
     * @param $result
     * @return bool
     */
	private function loadController($result){
        $render = true;
        $result['controller'] = ucfirst($result['controller']);
        if($result['plugin']!=false){
            $controllerName = '\\'.Storage::$namespacePlugins.'\\'.$result['plugin'].'\\'.Storage::$controllersFolder.'\\'.$result['controller'].'Controller';
        }else{
            $controllerName = '\\'.Storage::$namespaceApp.'\\'.Storage::$controllersFolder.'\\'.$result['controller'].'Controller';
        }
		if(class_exists($controllerName)){
            $controller = new $controllerName();
            if(is_array($result['options']) and count($result['options'])>0){
                foreach ($result['options'] as $key=>$option){
                    Storage::$query[$key] = $option;
                }
            }
            $action = $result['action'];
            if(method_exists($controller,$action)){
                $controller->$action();
                $error = false;
                if(!empty($result['accessType'])){
                    if($controller->getType() != $result['accessType']){
                        $error = true;
                    }
                }
                if($error==false) {
                    $this->setStatus($controller->getStatus());
                    if($controller->is_redirect){
                        $this->redirect($controller->is_redirect);
                    }elseif($controller->is_reload){
                        $this->redirect(Storage::$requestUri);
                    }else{
                        $this->outputTemplate = $controller->outputTemplate;
                        switch($controller->getType()){
                            case 'json':
                                $this->setContentType('application/json');
                                $render = false;
                                $jsonData = array();
                                if(count(Storage::$errors)>0) $jsonData['dit_errors'] = Storage::$errors;
                                $controllerJsonData = $controller->getJsonData();
                                if(is_array($controllerJsonData)){
                                    foreach ($controllerJsonData as $key=>$item){
                                        $jsonData[$key] = $item;
                                    }
                                }
                                if(Storage::$popup){
                                    $jsonData['popup'] = array();
                                    $jsonData['popup']['title'] = Storage::$popupTitle;
                                    $jsonData['popup']['hideClose'] = Storage::$popupHideClose;
                                    $jsonData['popup']['content'] = Storage::$popupContent;
                                    $jsonData['popup']['btnOne'] = Storage::$popupBtnOne;
                                    $jsonData['popup']['btnTwo'] = Storage::$popupBtnTwo;
                                    $jsonData['popup']['form'] = Storage::$popupForm;
                                    $jsonData['popup']['formAction'] = Storage::$popupFormAction;
                                    $jsonData['popup']['class'] = Storage::$popupClass;
                                }
                                if(Storage::$form){
                                    $jsonData['form'] = array();
                                    $jsonData['form']['success'] = Storage::$formSuccessMessage;
                                    $jsonData['form']['error'] = Storage::$formErrorMessage;
                                    $jsonData['form']['successCallback'] = Storage::$formSuccessCallback;
                                }
                                echo json_encode($jsonData);
                                break;
                            case 'html':
                                $this->mainTemplate = $controller->mainTemplate;
                                $this->setContentType('text/html');
                                break;
                        }
                    }
                }else{
                    $this->setStatus(404);
                }
            }else{
                $this->setStatus(404);
            }
		}else{
		   $this->setStatus(404);
		}
		return $render;
	}

    private function loadSymlink($result){
        if(isset($result['options']['file'])){
            $result['options']['file'] = str_replace("/", DIRECTORY_SEPARATOR, $result['options']['file']);
            $file = $result['symlink'].$result['options']['file'];
            if(file_exists($file)){
                header('Content-type: '.$this->Files->getFileType($file).'; charset=utf-8');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                die;
            }else{
                $this->setStatus(404);
            }
        }else{
            $this->setStatus(404);
        }
    }

    private function setStatus($status){
        $this->http_response_code($status);
        $this->status = $status;
    }

    private function setContentType($type){
        header('Content-type: '.$type.'; charset=utf-8');
    }

    private function redirect($link){
        if(!empty($link)){
            header('Location: '.$link);
            die();
        }
    }

    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}