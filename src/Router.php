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
 * Class Router
 * Роутер
 *
 * @package DITFramework
 */
class Router{

    protected $result = array(
        'status'=>200,
        'controller'=>null,
        'action'=>null,
        'redirect'=>null,
        'symlink'=>null,
        'rule'=>null,
        'type'=>null,
        'accessType'=>null,
        'plugin'=>false,
        'options'=>array(),
    );

	private $rules = array();

	function __construct(){
	    foreach (Storage::$rules as $rule=>$options){
	        $this->rules[Storage::$webRoot.$rule] = $options;
        }
        $this->rules[Storage::$webRoot.'{controller}/{action}/'] = array();
        $this->rules[Storage::$webRoot.'{controller}/{action}.json'] = array('accessType'=>'json');
    }

    public function start(){
		$this->parseRules();
		$this->findRule();
		return $this->result;
	}

	private function parseRules(){
		foreach ($this->rules as $k=>$v) {
			preg_match_all("/{(?<key>[0-9a-z_:]+)}/msiu", $k, $matches);
			$pattern = $k;
			if(!empty($matches['key'])){
				foreach($matches['key'] as $key=>$val){
					$val_array = explode(':',$val);
					$cfg = '([а-яa-z0-9\-\_\.]+)';
					$val_key = $val;
					if(count($val_array)>1){
						switch($val_array[1]){
							case 'all':
								$cfg = '(.*)';
								break;
							case 'optional':
								$cfg = '([а-яa-z0-9\-\_\./]+)?';
								break;
							case 'num':
								$cfg = '([0-9]+)';
								break;
							case 'cyr':
								$cfg = '([а-я\-\_\.]+)';
								break;
							case 'cyrnum':
								$cfg = '([а-я0-9\-\_\.]+)';
								break;
							case 'lat':
								$cfg = '([a-z\-\_\.]+)';
								break;
							case 'latnum':
								$cfg = '([a-z0-9\-\_\.]+)';
								break;
							case 'cyrlat':
								$cfg = '([а-яa-z\-\_\.]+)';
								break;
							case 'cyrlatnum':
								$cfg = '([а-яa-z0-9\-\_\.]+)';
								break;
						}
						$val_key = $val_array[0];
					}
					if(isset($v['symlink'])){
						$cfg = '(.*)';
					}
					$this->rules[$k]['options'][$val_key] = array();
					$pattern = preg_replace("/{(?<key>[".$val."]+)}/",$cfg, $pattern);
				}
			}
			$this->rules[$k]['pattern'] = "@^" . $pattern . "$@Diu";
			if(!isset($v['type'])) $this->rules[$k]['type'] = 'controller';
			if(isset($v['redirect'])){
				$this->rules[$k]['type'] = 'redirect';
			}
			if(isset($v['symlink'])){
				$this->rules[$k]['type'] = 'symlink';
			}
		}
	}

	private function findRule(){
		foreach($this->rules as $key=>$val){
			if(preg_match($val['pattern'], Storage::$url, $matches)) {
				if(count($matches)>1) {
					$i = 1;
					foreach ($val['options'] as $k1=>$v1) {
						if (isset($matches[$i])) {
							switch($k1) {
								case 'controller':
									$val['controller']=$matches[$i];
									break;
								case 'action':
									$val['action']=$matches[$i];
									break;
								default:
									$this->result['options'][$k1] = $matches[$i];
									break;
							}
						}
						$i++;
					}
				}
				if(isset($val['controller'])) $this->result['controller'] = $val['controller'];
				if(isset($val['action'])) $this->result['action'] = $val['action'];
				if(isset($val['redirect'])) $this->result['redirect'] = $val['redirect'];
				if(isset($val['symlink'])) $this->result['symlink'] = $val['symlink'];
				if(isset($val['accessType'])) $this->result['accessType'] = $val['accessType'];
				$this->result['rule'] = $key;
				$this->result['type'] = $val['type'];
				break;
			}
		}
		if(!isset($this->result['rule'])){
			$this->result['status'] = 404;
		}
	}
}