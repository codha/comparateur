<?php  

namespace App; 

class FrontController{

  public static function __callStatic($controller, $data){
  	$action = $data[0]; 
  	$params = $data[1];
  	try{ 
  		$controller  = __namespace__.'\\'.$controller; 
  		if(class_exists ($controller) && method_exists(new $controller, $action)){
  			$Manager = new $controller;
  			$Manager->$action($params); 
        call_user_func(array($Manager, $action));
  			exit; 
  		} else {
  			echo "Oops, Something Goes Wrong..."; 
  		}
  		
  	} catch (Exception $e) {
  		echo "Oops, Something Goes Wrong..."; 
  	}
  }

}