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
 * Class TemplateEngine
 * Шаблонизатор
 *
 * @package DITFramework
 * @property \DITFramework\Minify $minify
 * @property Session $session
 */
class TemplateEngine{
	private $minify;
    private $vars = array();
	private $html = '';
    public $session;

	function __construct(){
        $this->session = Instance::getSessionInstance();
        $this->vars = Storage::$templateVars;
        $this->minify = new Minify();
    }

    public function render(){
        $mainTemplate = Storage::$mainTemplate;
        if(count(Storage::$fatalError)>0){
            $mainTemplate = 'dit-fatal-error';
        }
        $appMainFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $mainTemplate .'.phtml';
        $mainFile = Storage::$frameworkTemplatesDir . $mainTemplate .'.phtml';
        if(file_exists($appMainFile)) $mainFile = $appMainFile;
        if(file_exists($mainFile)){
            ob_start();
            require $mainFile;
            $this->html = ob_get_clean();
        }
	}

	public function getRenderHtml(){
	    return $this->html;
    }

	public function output(){
		ob_start();
        $appHtmlFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . Storage::$outputTemplate .'.phtml';
        $ditHtmlFile = Storage::$frameworkTemplatesDir . Storage::$outputTemplate .'.phtml';;
        if(file_exists($appHtmlFile)){
            require $appHtmlFile;
        }elseif(file_exists($ditHtmlFile)){
            require $ditHtmlFile;
        }
		$html = ob_get_clean();
		if(Storage::$minifyHtml) $html = $this->minify->html($html);
		echo $html;
	}

	public function loadTemplate($template,$vars=array()){
	    if(is_array($vars)){
	        foreach ($vars as $key=>$var){
                $this->vars[$key]=$var;
            }
        }
		$file = Storage::$frameworkTemplatesDir . $template . '.phtml';
        $appMainFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $template . '.phtml';
        if(file_exists($appMainFile)) $file = $appMainFile;
		if(file_exists($file)) require $file;
	}

	public function existTemplate($template){
		$file = Storage::$frameworkTemplatesDir . $template . '.phtml';
        $appMainFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $template . '.phtml';
        $result = false;
        if(file_exists($appMainFile)) $result = true;
		if(file_exists($file)) $result = true;
		return $result;
	}

	public function get($key){
		if(array_key_exists($key,$this->vars)) {
			return $this->vars[$key];
		}else{
			return false;
		}
	}

	public function frameworkBody(){
        echo $this->html;
	}

	public function frameworkHeader(){
	    $meta = "\t".'<meta charset="utf-8" />'."\n";
	    $meta .= "\t".'<meta name="viewport" content="width=device-width, initial-scale=1.0" />'."\n";
	    $meta .= "\t".'<title>'.Storage::$metaTitle.'</title>'."\n";
	    if(strlen(Storage::$metaDescription)>0){
            $meta .= "\t".'<meta name="description" content="'.Storage::$metaDescription.'" />'."\n";
        }
	    if(count(Storage::$metaKeywords)>0){
            $meta .= "\t".'<meta name="keywords" content="'.implode(', ',Storage::$metaKeywords).'" />'."\n";
        }
	    echo $meta;
	    $this->compileStyles();
	}

	public function frameworkFooter(){
		$dir = Storage::$frameworkTemplatesDir;
		if(Storage::$debug){
            $this->loadTemplate('dit-debug');
        }
        $this->compileScripts();
	}

    public function getWebRoot(){
        return Storage::$webRoot;
    }

	private function compileStyles(){
	    $result = '';
        $name = 'styles_dit.css';
        $cache = new Cache($name);
        if (!$cache->exist()) {
            $style_min = '';
            $less_content = "@WEB_ROOT: '".Storage::$webRoot."';\n";
            $less_content .= file_get_contents(Storage::$frameworkAssetsDir.'less'.DIRECTORY_SEPARATOR.'framework.less') . "\n";
            if(Storage::$debug) $less_content .= file_get_contents(Storage::$frameworkAssetsDir.'less'.DIRECTORY_SEPARATOR.'debug.less') . "\n";
            $less_content .= file_get_contents(Storage::$frameworkAssetsDir.'less'.DIRECTORY_SEPARATOR.'popup.less') . "\n";
            if(strlen($less_content)>0){
                $less = new \lessc();
                try {
                    $less_content = $less->compile($less_content);
                } catch (\Exception $e) {
                    trigger_error('Error Less gen: '. $e->getMessage());
                }
                $style_min .= $less_content . "\n";
            }
            if(Storage::$minifyStyles and strlen($style_min)>0) $style_min = $this->minify->css($style_min);
            $cache->save($style_min);
        }
        $result .= "\t".'<link rel="stylesheet" href="' . Storage::$webRoot . 'cache/' . $name . '" />'."\n";
	    foreach (Storage::$loadPackages as $package){
            if(isset(Storage::$styles[$package]) and is_array(Storage::$styles[$package])){
                $hash = md5(serialize(Storage::$styles[$package]));
                $name = 'styles_' . $package . '_' . $hash . '.css';
                $cache = new Cache($name);
                if (!$cache->exist()) {
                    $style_min = '';
                    $less_content = "@WEB_ROOT: '".Storage::$webRoot."';\n";
                    foreach (Storage::$styles[$package] as $item){
                        if(isset($item['file']) and isset($item['version'])){
                            $file = $item['file'];
                            $version = $item['version'];
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            $style_content = file_get_contents($file);
                            switch (strtolower($ext)){
                                case 'less':
                                    $less_content .= $style_content . "\n";
                                    break;
                                default:
                                    $style_min .= $style_content . "\n";
                                    break;
                            }
                        }
                    }
                    if(strlen($less_content)>0){
                        $less = new \lessc();
                        try {
                            $less_content = $less->compile($less_content);
                        } catch (\Exception $e) {
                            trigger_error('Error Less gen: '. $e->getMessage());
                        }
                        $style_min .= $less_content . "\n";
                    }
                    array_map("unlink", glob( Storage::$publicDir . 'cache' . DIRECTORY_SEPARATOR . 'styles_' . $package . '_*'));
                    if(Storage::$minifyStyles and strlen($style_min)>0) $style_min = $this->minify->css($style_min);
                    $cache->save($style_min);
                }
                $result .= "\t".'<link rel="stylesheet" href="' . Storage::$webRoot . 'cache/' . $name . '" />'."\n";
            }
        }
        echo $result;
    }

	private function compileScripts(){
	    $result = '';
        $name = 'scripts_dit.js';
        $cache = new Cache($name);
        if (!$cache->exist()) {
            $script_min = "var WEB_ROOT = '".Storage::$webRoot."';\n";
            $script_min .= file_get_contents(Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'jquery-3.3.1.min.js') . "\n";
            $script_min .= file_get_contents(Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'jquery.validate.min.js') . "\n";
            $script_min .= file_get_contents(Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'validate_ru.js') . "\n";
            $script_min .= file_get_contents(Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'jquery.maskedinput.min.js') . "\n";
            $script_min .= file_get_contents(Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'framework.js') . "\n";
            if(Storage::$minifyScripts and strlen($script_min)>0) $script_min = \JsMin\Minify::minify($script_min);
            $cache->save($script_min);
        }
        $result .= "\t".'<script src="' . Storage::$webRoot . 'cache/' . $name . '"></script>'."\n";
        foreach (Storage::$loadPackages as $package){
            if(isset(Storage::$scripts[$package]) and is_array(Storage::$scripts[$package])){
                foreach (Storage::$scripts[$package] as $key=>$item){
                    if(isset($item['type']) and $item['type']=='cdn'){
                        $result .= "\t".'<script src="' . $item['file'] . '"></script>'."\n";
                        unset(Storage::$scripts[$package][$key]);
                    }
                }
                $hash = md5(serialize(Storage::$scripts[$package]));
                $name = 'scripts_' . $package . '_' . $hash . '.js';
                $cache = new Cache($name);
                if (!$cache->exist()) {
                    $script_min = '';
                    foreach (Storage::$scripts[$package] as $item){
                        $file = $item['file'];
                        $version = $item['version'];
                        $script_content = file_get_contents($file);
                        $script_min .= $script_content . "\n";
                    }
                    array_map("unlink", glob( Storage::$publicDir . 'cache' . DIRECTORY_SEPARATOR . 'scripts_' . $package . '_*'));
                    if(Storage::$minifyScripts and strlen($script_min)>0) $script_min = \JsMin\Minify::minify($script_min);
                    $cache->save($script_min);
                }
                $result .= "\t".'<script src="' . Storage::$webRoot . 'cache/' . $name . '"></script>'."\n";
            }
        }
        echo $result;
    }
}