<?php  
namespace lib; 
use \App\Core as Base; 
/**
* 
* ContentDiff
* Contains, Element style filtering, from text marked by special tags.
* @return Array
*/

class Marker{
	
	public function clean($text){
		global $global_app_config_keys; 
		global $global_app_config; 
		// foreach (Base::config(null,'config-list-item') as $token) : 
		foreach ($global_app_config_keys as $token) : 
			$is_token  = "is_{$token}"; 
			$is_token_next_flag  = "is_{$token}_next_flag"; 
			$is_token_base  = "is_{$token}_base"; 
			$$is_token = $$is_token_next_flag = false; 

			$text = html_entity_decode($text); 
			// $text = preg_replace("@".preg_quote("<".$token.">",'@').'\s*'.preg_quote("</".$token.">", '@')."@i", '', html_entity_decode($text)); 
			$text = preg_replace("@".preg_quote("</".$token.">",'@').'\s*'.preg_quote("<".$token.">", '@')."@i", '', $text);
			$text = str_replace("’", "'", $text); 
			/* replacing variables */
			if(isset($_SESSION['params']["p"])) :
				$text = preg_replace("@#Pr(é|e)nom@i", $_SESSION['params']["p"], $text );
			endif;
			/* variables persos*/
			if(isset($_SESSION['params']["x"])) :
				if($_SESSION['params']['x'] == 1) : 
					$text = preg_replace("@#([^<>\s;\?\.,;!]+)/([^<>\s;\.,:!\?]+)@", "$1", $text); #masculain
				elseif( $_SESSION['params']['x'] > 1 ) : 
					$text = preg_replace("@#([^<>\s;\?\.,;!]+)/([^<>\s;\.,:!\?]+)@", "$2", $text); # feminin
				endif;  
			endif; 

			// $str  = preg_match("@(Fid.+/\w+((LDV)|(PRO)|(BDC)))@i", $str);  # title match this is normal
		endforeach; 

		$peaces = preg_split("/(\s)|(<[^>]*[^\/]>)/", $text, -1 , PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE  ); 
		
		
		foreach ($peaces as $key => $copyright) {
			$flag = array();
			/*$tag_pattern  = "@<[^>]*[^\/]>@";
			preg_match($tag_pattern, $copyright, $tag);
			if(!empty($tag)) : 
				if(!preg_match('@</@', $tag[0])) : 
					$flag[$global_app_config[str_replace(array('<', '>', '/'), array('', '', ''), $tag[0])]] = 1; 
					$copyright  = str_replace($tag[0], '', $copyright); 					
				endif; 
			endif; */

			foreach ($global_app_config_keys as $token) : 
				$is_token  = "is_{$token}"; 
				$is_token_next_flag  = "is_{$token}_next_flag"; 
				$is_token_base  = "is_{$token}_base"; 

				global $$is_token; 
				global $$is_token_next_flag;
				global $$is_token_base;
				
				$$is_token  = $$is_token_next_flag; 

				$start_pattern = "%^".preg_quote( "<".$token.">" ,"%" ).".*%" ; 
				$start_token = "<".$token.">"; 
				$end_token = "</".$token.">"; 

				if( trim($copyright)  == $start_token OR preg_match($start_pattern, trim($copyright)) ): 
					$$is_token = true; 
					$$is_token_next_flag = true; 
					$$is_token_base ++; 
				elseif ( trim($copyright)  == $end_token ) :
					$$is_token = false; 
					$$is_token_next_flag = false;
					$$is_token_base --; 
				endif;

				if($$is_token  && $$is_token_base >= 0) : 					
					$flag[$global_app_config[$token]] = 1; 
				endif; 
				$copyright  = str_replace(array($start_token, $end_token), array('', ''), $copyright); 
			endforeach; 

			$peaces[$key] = array(
				"flag"  => Base::clean($flag),
				// "flag"  => $flag,
				"value" => $copyright
			);

		}
		// echo "<pre>";   print_r($peaces);  echo "</pre>";  
		// return $copyright_side; 
		return $peaces; 
	}
}