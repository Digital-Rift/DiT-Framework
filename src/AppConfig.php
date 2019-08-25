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
 * Class AppConfig
 * Настройка приложения
 *
 * @package DITFramework
 */
class AppConfig{
    public function setReleaseInit($releases=array()){
        foreach ($releases as $release=>$host){
            if($_SERVER['SERVER_NAME']==$host or $_SERVER['SERVER_ADDR']==$host or $_SERVER['REMOTE_ADDR']==$releases){
                Storage::$release=$release;
            }
        }
    }

    public function setCahceLiveTime($cacheLiveTime){
        Storage::$cacheLiveTime = $cacheLiveTime;
    }

    public function enableMinifyStyles(){
        Storage::$minifyStyles = true;
    }

    public function enableMinifyScripts(){
        Storage::$minifyScripts = true;
    }

    public function enableMinifyHtml(){
        Storage::$minifyHtml = true;
    }

    public function disableMinifyStyles(){
        Storage::$minifyStyles = false;
    }

    public function disableMinifyScripts(){
        Storage::$minifyScripts = false;
    }

    public function disableMinifyHtml(){
        Storage::$minifyHtml = false;
    }

    public function setRule($rule,$options=array()){
        Storage::$rules[$rule] = $options;
    }

    public function setMainTemplate($mainTemplate='index'){
        Storage::$mainTemplate = $mainTemplate;
    }

    public function setWebRoot($webRoot='/'){
        Storage::$webRoot = $webRoot;
    }

    public function activePlugin($plugin){
        Storage::$plugins[md5($plugin)] = $plugin;
    }

    public function activePlugins($plugins=array()){
        foreach($plugins as $plugin){
            Storage::$plugins[md5($plugin)] = $plugin;
        }
    }

    public function setAssetsLink($name,$dir){
        Storage::$assetsLinks[md5($name)] = array(
            'name'=>$name,
            'dir'=>$dir,
        );
    }

    public function setStyle($file,$version='1.0',$package='main'){
        Storage::$styles[$package][md5($file)] = array(
            'file'=>$file,
            'version'=>$version,
        );
    }

    public function setScript($file,$version='1.0',$package='main'){
        Storage::$scripts[$package][md5($file)] = array(
            'file'=>$file,
            'version'=>$version,
            'type'=>'file',
        );
    }

    public function setScriptCDN($url,$package='main'){
        Storage::$scripts[$package][md5('cdn'.$url)] = array(
            'file'=>$url,
            'version'=>'1.0',
            'type'=>'cdn',
        );
    }

    public function debug(){
        Storage::$debug = true;
    }

    public function setDbDriver($driver){
        Storage::$dbDriver = $driver;
    }

    public function setDbName($name){
        Storage::$dbName = $name;
    }

    public function setDbHost($host){
        Storage::$dbHost = $host;
    }

    public function setDbUser($user){
        Storage::$dbUser = $user;
    }

    public function setDbPassword($password){
        Storage::$dbPassword = $password;
    }

    public function setDbTablePrefix($prefix){
        Storage::$dbTablePrefix = $prefix;
    }

    public function setDbCharset($charset){
        Storage::$dbCharset = $charset;
    }
}