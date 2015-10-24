<?php

define('APP_PATH', realpath(__DIR__.'/../'));
define('INC_PATH', APP_PATH.'/includes');
define('CLASSES_PATH', INC_PATH.'/classes');
error_reporting(E_ALL);

function autoloader($class_name){
	$class_to_load = CLASSES_PATH.'/'.$class_name.'.php';
	if(file_exists($class_to_load) && is_file($class_to_load)){
		include_once($class_to_load);
	}
}
spl_autoload_register('autoloader');