<?php 

namespace App; 

Class Loader {

	static function View ( $view ){
		ob_start(); 
		include_once(BASEPATH."views/{$view}.php") ; 
		echo ob_get_clean(); 
	}
	static function Library( $lib ){
		require_once(BASEPATH."lib/{$lib}.php"); 
	}

	static function LibFactory($instance){
		self::Library($instance); 
		$instance = "\lib\\".$instance; 
		return new $instance; 
	}
}