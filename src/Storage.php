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
 * Class Storage
 * Хранилище параметров
 *
 * @package DITFramework
 */
class Storage{
    public static $appDir;
    public static $publicDir;
    public static $namespaceApp;
    public static $namespacePlugins;
    public static $frameworkTemplatesDir;
    public static $frameworkAssetsDir;
    public static $cacheLiveTime = '+2 day';
    public static $webRoot = '/';
    public static $release = 'publicRelease';
    public static $controllersFolder = 'controllers';
    public static $modelsFolder = 'models';
    public static $pluginsFolder = 'plugins';
    public static $templatesFolder = 'templates';
    public static $errors = array();
    public static $fatalError = array();
    public static $rules = array();
    public static $plugins = array();
    public static $styles = array('main'=>array());
    public static $scripts = array('main'=>array());
    public static $assetsLinks = array();
    public static $minifyStyles = true;
    public static $minifyScripts = true;
    public static $minifyHtml = true;
    public static $debug = false;
    /**
     * Запросы
     */
    public static $method;
    public static $requestUri;
    public static $serverIp;
    public static $userIp;
    public static $host;
    public static $url;
    public static $query = array();
    /**
     * Настройки базы данных
     */
    public static $dbDriver = 'mysql';
    public static $dbName = 'dit';
    public static $dbHost = 'localhost';
    public static $dbUser = 'root';
    public static $dbPassword = 'password';
    public static $dbTablePrefix = '';
    public static $dbCharset = 'utf8';
    /**
     * Настройки popup окна
     */
    public static $popup = false;
    public static $popupTitle = false;
    public static $popupHideClose = false;
    public static $popupContent = false;
    public static $popupBtnOne = false;
    public static $popupBtnTwo = false;
    public static $popupForm = false;
    public static $popupFormAction = false;
    public static $popupClass = false;
    /**
     * Настройки формы
     */
    public static $form = false;
    public static $formSuccessMessage = false;
    public static $formErrorMessage = false;
    public static $formSuccessCallback = false;
    /**
     * Настройки шаблона
     */
    public static $mainTemplate = 'index';
    public static $outputTemplate = 'html';
    public static $templateVars = array();
    public static $metaTitle = '';
    public static $metaDescription = '';
    public static $metaKeywords = array();
    public static $loadPackages = array('main');
}