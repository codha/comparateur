<?php  

namespace lib; 
/**
* @class Onp, a php implementation of the "An O(NP) Sequence Comparison Algorithm"
* described by Sun Wu, Udi Manber and Gene Myers, check the documentation for more details.
*
*/

class ONP {

    protected $a              = ""; 
    protected $b              = ""; 
    protected $style_a        = ""; 
    protected $style_b        = ""; 
    protected $m              = 0; 
    protected $n              = 0; 

    protected $reverse        = false; 
    protected $editDistance   = null; # edit distance 
    protected $offset         = false; 
    protected $path           = array();  
    protected $correctPath    = array();
    protected $ses            = array();
    protected $lcs            = "";

    const SES_DELETE = -1;
    const SES_COMMON = 0;
    const SES_ADD    = 1;

    /**
    * @param a , b the text (marked) input 
    * @param style_a , style_b the style of the input 
    */
    function initialize($a,$style_a, $b, $style_b){
        $this->a  = $a;
        $this->b  = $b;
        $this->style_a  = $style_a;
        $this->style_b  = $style_b;
        $this->m = count($a); 
        $this->n = count($b); 
        $this->offset = $this->m + 1; 

        $this->init(); 
    }

    /* getters*/

    public function getSessions (){
        return $this->ses; 
    }

    public function getEditDistance(){
        return $this->editDistance; 
    }

    protected function init(){
        if ($this->m >= $this->n) {
            $tmp1           = $this->a;
            $tmp2           = $this->m;
            $tmp3           = $this->style_a;
            $this->a        = $this->b;
            $this->style_a  = $this->style_b;
            $this->b        = $tmp1;
            $this->style_b  = $tmp3;
            $this->m        = $this->n;
            $this->n        = $tmp2;
            $this->reverse  = true;
        }
    }

    protected function P ($x, $y, $k) {
        $object =  new \stdclass(); 
        $object -> x =  $x ; 
        $object -> y =  $y ; 
        $object -> k =  $k ; 
        return $object; 
    }
    //what if i changed that 
    protected function getElements ($elem, $style, $t) {
        $result  = new \stdclass();
        $result->elem = $elem;
        $result->style = $style;  
        $result->t = $t; 
        return $result; 
    }

    protected function equals($a, $b){ 
        return $a == $b;
    }

    protected function snake ($k, $p, $pp) {
        if ($p > $pp) {
            $r = $this->path[$k-1+$this->offset];
        } else {
            $r = $this->path[$k+1+$this->offset];
        }
        
        $y = max($p, $pp);
        $x = $y - $k;
        // file_put_contents (BASEPATH.'/logs/combine.php', '|'.$this->a[$x]."| against |".$this->b[$y]."|".json_encode($this->style_a[$x]).' ::: '.json_encode($this->style_b[$y])."\n\n", FILE_APPEND);
        while ( $x < $this->m && $y < $this->n && /*strcmp($this->a[$x], $this->b[$y]) == 0*/ $this->a[$x] === $this->b[$y] && $this->style_a[$x] == $this->style_b[$y] ){
            ++$x;
            ++$y;
        }
        
        $this->path[$k+$this->offset] = count($this->correctPath);
        $this->correctPath[count($this->correctPath)] = $this -> P($x, $y, $r);
        return $y;
    }

    protected function recordseq ($epc) {

        $x_idx  = $y_idx  = 1;
        $px_idx = $py_idx = 0;
        $epc_length = count($epc); 
        for ($i=$epc_length-1;$i>=0;--$i) {
            while($px_idx < $epc[$i]->x || $py_idx < $epc[$i]->y) {
                if ($epc[$i]->y - $epc[$i]->x > $py_idx - $px_idx) { 
                    if ($this->reverse) {
                        $this->ses[count($this->ses)] = $this -> getElements($this->b[$py_idx], $this->style_b[$py_idx],  self::SES_DELETE); 
                    } else {
                        $this->ses[count($this->ses)] = $this -> getElements($this->b[$py_idx], $this->style_b[$py_idx],  self::SES_ADD);
                    }
                    ++$y_idx;
                    ++$py_idx;
                } else if ($epc[$i]->y - $epc[$i]->x < $py_idx - $px_idx) {
                    if ($this->reverse) {
                        $this->ses[count($this->ses)] = $this-> getElements($this->a[$px_idx], $this->style_a[$px_idx], self::SES_ADD);
                    } else {
                        $this->ses[count($this->ses)] = $this-> getElements($this->a[$px_idx], $this->style_a[$px_idx], self::SES_DELETE);
                    }
                    ++$x_idx;
                    ++$px_idx;
                } else {
                    $this->ses[count($this->ses)] = $this-> getElements($this->a[$px_idx], $this->style_a[$px_idx],self::SES_COMMON);
                    $this->lcs += $this->a[$px_idx];
                    ++$x_idx;
                    ++$y_idx;
                    ++$px_idx;
                    ++$py_idx;
                }
            }
        }
    }

    public function run (){

        $delta  = $this->n - $this->m;
        $size   = $this->m + $this->n + 3;
        $fp     = array();
        for ($i=0;$i<$size;++$i) {
            $fp[$i] = -1;
            $this->path[$i] = -1;
        }
        $p = -1;
        do {
            ++$p;
            for ($k=-$p;$k<=$delta-1;++$k) {
                $fp[$k+$this->offset] = $this->snake($k, $fp[$k-1+$this->offset]+1, $fp[$k+1+$this->offset]);
            }
            for ($k=$delta+$p;$k>=$delta+1;--$k) {
                $fp[$k+$this->offset] = $this->snake($k, $fp[$k-1+$this->offset]+1, $fp[$k+1+$this->offset]);
            }
            $fp[$delta+$this->offset] = $this->snake($delta, $fp[$delta-1+$this->offset]+1, $fp[$delta+1+$this->offset]);
        } while ($fp[$delta+$this->offset] !== $this->n);
        
        $this->editDistance = $delta + 2 * $p;
        
        $r = $this->path[$delta+$this->offset];
        
        $epc  = array();
        gc_collect_cycles(); // easing memory leak
       
        while ($r !== -1) { 
            $epc[count($epc)] = $this -> P($this->correctPath[$r]->x, $this->correctPath[$r]->y, null);
            $r = $this->correctPath[$r]->k;
        } 
        $this->recordseq($epc);
    }

}