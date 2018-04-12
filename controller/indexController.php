<?php 

namespace App; 

class IndexController{
	
	public function indexAction($params = array()){
		
		return  Loader::view('index'); 
	}
}