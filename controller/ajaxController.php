<?php
namespace App ; 

class AjaxController{

	public function __construct(){
		if(!isset($_SESSION)):
			session_start(); 
		endif; 
	}

	public function loadAction(){
		
		/* 
	    *  Récuperation du fichier HTML 
	    */
	    $base_manager = new Base(); 
	    if(isset($_POST["htmlLink"]) && ($_POST["htmlLink"]!=""))
	    {
	        $lHtm = addslashes($_POST["htmlLink"]);
	        $_SESSION['params'] = $base_manager->getParams($lHtm); 
	        echo '<div id="loadHtm">'.$base_manager->loadLinkHtm($lHtm).'</div>';
	    } else if(isset($_FILES["htmlFile"]) && ($_FILES["htmlFile"]['name']!="")){
	        $fHtm = array($_FILES['htmlFile']['name'],$_FILES['htmlFile']['tmp_name']);
	        echo '<div id="loadHtm">'.$base_manager->loadLinkHtm($fHtm[1]).'</div>';
	    }
	    
	    /* 
	    *  Récuperation des fichier .Docx 
	    */

	    if(isset($_FILES["docFile"]) && ($_FILES["docFile"]['name']!=""))
	    {
	        $fDoc = array($_FILES['docFile']['name'],$_FILES['docFile']['tmp_name']);
	        $cntFDocPlus = "";
	        if(isset($_FILES["docFilePlus"]) && ($_FILES["docFilePlus"]['name']!=""))
	        {
	            $fDocPlus    = array($_FILES['docFilePlus']['name'],$_FILES['docFilePlus']['tmp_name']);
	            $cntFDocPlus = '^^'.$base_manager->readDoc($fDocPlus);
	        }
	        echo '<div id="loadDoc">'.$base_manager->readDoc($fDoc).$cntFDocPlus.'</div>';
	    }

	    exit;
	}

	public function processAction(){
		
		// error_reporting(0);
		ob_start(); 
		ini_set('memory_limit', '-1'); // Avoid suprises
		set_time_limit(10060);

		if(isset($_POST['html']) && isset($_POST['copyright'])) :
			$copyright = $_POST['copyright'] ;  
			$html = $_POST['html']; 
			// file_put_contents (BASEPATH.'/logs/copyright.php', $copyright);
			$html = array_map(function($str){
				return Loader::libFactory('Marker')->clean($str); 
			}, $html); 
			
			$copyright = array_map(function($str){
				return Loader::libFactory('Marker')->clean($str); 
			}, $copyright);

			$copyright2  = array(); 
			foreach ($copyright as $key => $value) {
				$copyright2[] = array('value'=> "<p>", "flag" => array()); 
					foreach ($value as $k => $e) {
						if($e['value'] !== "") :
							$copyright2[] = $e;
						endif; 
					}
				$copyright2[] = array('value'=> "</p>", "flag" => array()); 
			}

			$html2 = array(); 
			
			foreach ($html as $key => $value) {
				$html2[] = array('value'=> "<p>", "flag" => array());  
				foreach ($value as $k => $e) {
						if($e['value'] !== "") :
							$html2[] = $e;
						endif;  
				}
				$html2[] = array('value'=> "</p>", "flag" => array()); 
			}
			
			if ( function_exists('array_column') ) :  // requires php 5.5

				$copyright_style = array_column($copyright2, 'flag'); 
				$copyright_content = array_column($copyright2, 'value');

				$html_style = array_column($html2, 'flag');  
				$html_content = array_column($html2, 'value');  
            
			else : 
				$copyright_style = array_map(function($row){
					return $row['flag']; 
				}, $copyright2); 
				
				$copyright_content = array_map(function($row){
					return $row['value']; 
				}, $copyright2); 

				$html_style = array_map(function($row){
					return $row['flag']; 
				}, $html2); 
				
				$html_content = array_map(function($row){
					return $row['value']; 
				}, $html2); 

			endif; 
			 
			gc_collect_cycles();   // clean up memory
			$t = microtime(true);
			$onp = Loader::libFactory('onp');
			$onp->initialize($copyright_content, $copyright_style, $html_content, $html_style);
			$onp->run(); 

			$sessions = $onp->getSessions();
			// file_put_contents (BASEPATH.'/logs/copyright.php', json_encode($sessions));
			$reproducer = Loader::libFactory('Reconstruction'); 
			$result = $reproducer->reproduce_page($sessions);
			// echo (microtime(true) -$t); echo "<br>";
			echo $result;  
		endif ; 
		session_destroy(); 
		echo ob_get_clean(); 
		die;
	}

}