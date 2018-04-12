<?php

/**
* Reconstruction of The result page after performing an O(np) Sequence Comparison Algorithm
*
*/


namespace lib; 

class Reconstruction{

	public function reproduce_page($sections){
		$str = ""; 
		$errors = ""; 
		$deleted =  0; 
		$inserted =  0; 
		foreach ($sections as $key => $value) {
			if($value->t  == 0){
				$str .= $this -> setStyle($value->elem, $value->style);
			} elseif ($value->t == -1) { 
				if (!preg_match( "@([(<p>\s*)(</p>\s*)(\s*)(\[J(\+|\-)\d\])(\[M(\+|\-)\d\])(#Prénom)(#Pr&eacute;nom)])+@i", trim($value->elem) ) && trim($value->elem) != "" ) :
					$deleted ++; 
					// $errors .="\n \n ".$value->elem."\n number :::> ".$deleted ; 
				endif; 
				$str .= $this -> setStyle("<del>".($value->elem)."</del>", $value->style);
			} elseif ($value->t == 1) {
				if (!preg_match( "@([(<p>\s*)(</p>\s*)(\s*)(\[J(\+|\-)\d\])(\[M(\+|\-)\d\])(#Prénom)(#Pr&eacute;nom)])+@i", trim($value->elem) ) && trim($value->elem) != "" ) :
					$inserted ++; 
				endif;  
				$str .= $this -> setStyle("<ins>".($value->elem)."</ins>", $value->style);
			}
		}

		$str  = preg_replace("@(#[^<>\s]+\s*[^<>\s]+/*\w+)@", "<span class='variable_detected'>$1</span>", $str); 
		$str  = preg_replace("@(\[[JM]\s*[\+\-]\s*\d+\])@", "<span class='date_detected'>$1</span>", $str); 

		return $str; //.'<div style="display: none" id="diagnostics-errors"><div class ="html">'.$inserted.'</div><div class ="copyright">'.$deleted.'</div></div>'; 
	}

	private function setStyle($init, $style){

		$str = $init; 

		if( isset($style[3]) && $style[3] == 1 ){
			$str  = '<a class="link"><b>'.$str."</b></a>";
		} 

		if ( isset($style[4]) && $style[4] == 1 ) {
			$str  = '<u><b>'.$str.'</b></u>';
		}

		if ( isset($style[5]) && $style[5] == 1 ) {
			$str  = '<i>'.$str."</i>";
		} 

		if (isset($style[1]) && $style[1] == 1) {
			$str  = '<b>'.$str."</b>";
		} 
		if (isset($style[2]) && $style[2] == 1) {
			$str  = '<u>'.$str."</u>";
		}

		return $str; 
	}
}