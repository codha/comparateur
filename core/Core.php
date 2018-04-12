<?php   
namespace App; 

Class Core {

	static function config($type = false, $list = false, $tag = false){
		
		/*$config['b']  = 1; #bold

		$config['u']  = 2; #underlined

		$config['link']  = 3; 

		$config['ub']  = 4; # underlined + bold

		$config['i']  = 5; # italic*/

		if(!$list)  : 
			/*if(isset($config[$type])) :  
				return $config[$type]; 
			else  : 
				return false; 
			endif; */
			switch ($type) {
				case 'b':
					return 1; 
					break;

				case 'u':
					return 2; 
					break;
				
				case 'link':
					return 3; 
					break;
				
				case 'ub':
					return 4; 
					break;
				
				case 'i':
					return 5; 
					break;
				
				default:
					return false; 
					break;
			}

			if($tag) :
				// if()
			endif; 

			return  $data; 
		elseif ($list == "config-list-item")  : 
			// return array_keys($config); 
			return array('b', 'u', 'link', 'ub', "i");
		endif; 
	}

	static function clean($flags){
		global $global_app_config; 
		/*$bold  = self::config('b'); 
		$ubold  = self::config('ub'); 
		$underlined  = self::config('u'); 
		$link  = self::config('link'); 
		$italic  = self::config('i'); */

		$bold  = $global_app_config['b']; 
		$ubold  = $global_app_config['ub']; 
		$underlined  = $global_app_config['u']; 
		$link  = $global_app_config['link']; 
		$italic  = $global_app_config['i']; 

		if(array_key_exists($bold, $flags) && array_key_exists($ubold, $flags) ) : 
			unset($flags[$bold]); 
		endif; 

		if(array_key_exists($bold, $flags) && array_key_exists($link, $flags) ) : 
			unset($flags[$bold]); 
		endif; 

		if(array_key_exists($underlined, $flags) && array_key_exists($ubold, $flags) ) : 
			unset($flags[$underlined]); 
		endif; 

		if(array_key_exists($underlined, $flags) && array_key_exists($link, $flags) ) : 
			unset($flags[$underlined]); 
		endif;
		
		if(array_key_exists($ubold, $flags) && array_key_exists($link, $flags) ) : 
			unset($flags[$ubold]); 
		endif;

		return $flags; 
	}
}