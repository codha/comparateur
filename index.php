<?php  
require_once('vendor/autoload.php'); 

define ("ENV", "production"); 

if(ENV == "development"){
	// error_reporting(E_ALL); 
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1); 
} elseif (ENV == "production" ) {
	error_reporting(0);
}

if(isset($_SERVER['HTTPS'])){
	$prefix =  "https://";
} else {
	$prefix = "http://"; 
}

$filename  = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']); 
include_once("core/config.php"); 
define("BASE_URL", $prefix.$_SERVER['HTTP_HOST'].$filename); 
define("BASEPATH", __DIR__.'/'); 
define('RESOURCE', BASE_URL."resource/") ; 

new App\Router; 