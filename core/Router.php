<?php 
namespace App; 

class Router{
	function __construct(){

		if(!defined('BASEPATH')){
			die('403 your are allowed to access this file !!'); 
		}
		// die (RESOURCE); 
		if(isset($_GET['c'])) : 
			$uri = strip_tags($_GET['c']);
			$uri = explode('/', $uri) ; 
			$controller = $uri[0]."Controller";
			if(isset($uri[1])) : 
				$action = $uri[1]."Action";
				unset($uri[0]); 
				unset($uri[1]); 
				$params = array_values($uri); 
			else :
				$action = "indexAction"; 
				unset($uri[0]); 
				$params = array_values($uri); 
			endif; 
		else:  
			$action = "indexAction";  
			$controller = "indexController"; 
			$params = array(); 
		endif; 
		
		FrontController::$controller($action, $params); 

	}
}