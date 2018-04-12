<?php

namespace App; 
use App\Core as Core;

Class Base{
    public function loadDocx($file)
    {
        $zip = new \ZipArchive;
        if ($zip->open($file) === true)
        {     
            if (($index = $zip->locateName('word/document.xml')) !== false)
            {                           
                $data = $zip->getFromIndex($index);
                $zip->close();
                $data = $this->transform_docx($data); # transform the returned text
                $data = str_replace('</w:p>',"^^", $data);
                $data = str_replace('^^^^',"^^", $data);
                $data = strip_tags($data);

                return $data;
            }
        }
    }

    /**
    * 
    * @highlight special charatcters within .docx file
    * @return XML data
    */
    protected function transform_docx($data){
        
        $dom = new \DOMDocument();
        $dom->loadXML($data);

        $xpath = new \DomXPath($dom);
        $nodes = $xpath->query("/w:document/w:body/w:p/w:r");

        foreach ($nodes as $key => $value): 
            if($value->childNodes->item(1) !== null) :
                if($value->childNodes->item(1)->tagName == "w:t"):
                    if($value->childNodes->item(0)->tagName == "w:rPr") : 

                        $length = $value->childNodes->item(0)->childNodes->length; 
                        $node = $value->childNodes->item(0)->childNodes ;

                        $is_bold = false; 
                        $is_link_color = false;
                        $is_underlined =  false; 

                        for ( $i = 0  ; $i< $length; $i++ ) : 
                            if( $node->item($i)->tagName == "w:b") : 
                                $is_bold  = true;
                            endif; 
                            if( $node->item($i)->tagName == "w:color" && $node->item($i)->hasAttributes() ) : 
                                if($node->item($i)->attributes->getNamedItem('val')->nodeValue == "0000FF"): 
                                    $is_link_color  = true;
                                endif; 
                            endif; 
                            if( $node->item($i)->tagName == "w:u") : 
                                $is_underlined  = true;
                            endif; 
                            if( $node->item($i)->tagName == "w:caps") : 
                                $value->nodeValue = mb_strtoupper($value->nodeValue, 'UTF-8') ;
                            endif; 
                        endfor; 

                        $date_pattern = "@\[[JM]\s*[\+\-]\s*\d+\]@"; 

                        if($is_link_color && $is_bold /*&& $is_underlined*/) :
                            if(preg_match($date_pattern, $value->nodeValue)):
                                $value->nodeValue = '<b>'. $value->nodeValue .'</b>'; #bold
                            else: 
                                $value->nodeValue = '<link>'. $value->nodeValue .'</link>'; #link
                            endif;
                        elseif ($is_bold && $is_underlined) : 
                            $value->nodeValue = '<ub>'. $value->nodeValue .'</ub>'; #ubold
                        elseif ($is_bold && !$is_underlined) : 
                            $value->nodeValue = '<b>'. $value->nodeValue .'</b>'; #bold
                        elseif ($is_underlined && !$is_bold) : 
                            $value->nodeValue = '<u>'. $value->nodeValue .'</u>'; #undernlined
                        endif;

                    endif; 
                endif; 
            endif; 
        endforeach; 

        return $dom->saveXML();
    }

    public function loadDoc($file)
    {
        $fileHandle     = fopen( $file, "r" );
        $line           = @fread( $fileHandle, filesize($file) );
        $lines          = explode( chr(0x0D), $line );
        $outtext        = "";
        $start          = false;

        foreach($lines as $thisline)
        {
            $pos = strpos( $thisline, chr(0x00));
            if($pos!== FALSE)
            {
                if($start) break;
            }
            else
            {
                if(!$start) $start = true;
                $data .= $thisline."^^";
            }
        }
        $data = mb_convert_encoding($data, "HTML-ENTITIES", "ASCII");
        return $data;
    }
    
    public function loadLinkHtm($file)
    {
         /*resolution of direct server problem*/
        $file = preg_match("@^direct@", $file) ? 'http://'.$file  : $file; 
        $data   = file_get_contents($file);   
        $search = array('@<script[^>]*?>.*?</script>@si');
        $data   = preg_replace($search, '', $data);      
        $data   = str_replace( array( "\n", "\r" ), array(' ', ' ' ), $data );
        ob_start(function ($buffer){
            $search = array(
                '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
                '/[^\S ]+\</s',  // strip whitespaces before tags, except space
                '/(\s)+/s'       // shorten multiple whitespace sequences
            );

            $replace = array(
                '>',
                '<',
                '\\1'
            );

            $buffer = preg_replace($search, $replace, $buffer);

            return $buffer;
        });

        echo $data; 
        return ob_get_clean();
    }
    
    public function readDoc($doc)
    {
        $ext = explode('.',$doc[0]);
        $ext = strtolower($ext[1]);
        switch($ext)
        {
            case 'docx' :
                return $this->loadDocx($doc[1]);
            break;
        }

    }

    public function getParams($query_string){
        if(preg_match("@index.php/Site@i", $query_string)):  # yii platform
            $str = strstr($query_string, "index?");
            $str = str_replace("index?", "", $str);
        else : 
            $str = strstr($query_string, "index.php?"); # old platform
            $str = str_replace("index.php?", "", $str);
        endif; 
        $str = str_replace("#BDC", "", $str);
        $str  = explode("&", $str);  
        $params  = array(); 
        foreach ($str as $key => $value) {
            $temp =explode("=", $value); 
            $params[$temp[0]] = $temp[1]; 
        }
        // echo "<pre>"; print_r($params); die; 
        return $params; 
    }
}

?>