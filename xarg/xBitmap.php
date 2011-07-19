<?php
class bitmask {
    /**
     * Two-dimensional array containing all bits and their values.  This array is populated by the class
     *
     * @var array
     * @access public
     */
    var $mask_array = array();

    /**
     * Contains the mask created by the class.  It can contain multiple 32 bit masks separated by a hyphen (-)
     * in order to represent extremely large bitmasks on systems without gmp available.
     *
     * @var string
     * @access public
     */
    var $assoc_keys = array();
     /**
     * Contains the keys for associative array
     *
     * @var array
     * @access public
     */
    var $forward_mask = '';

    /**
     * Contains the actual binary number represented by the mask.
     *
     * @var string
     * @access public
     */
    var $bin_mask = '';

    /**
    * @return void
    * @desc Sets the $bin_mask variable based on the $forward_mask
    * @access private
    */
    function _set_binmask() {
        $masks = explode('-',$this->forward_mask);
        if (count($masks) > 1) {
            for ($c = count($masks) - 2; $c >= 0; $c--) {
                $bin_val .= str_pad((string)(base_convert($masks[$c],10,2)),32,'0',STR_PAD_LEFT);
            }
            $bin_val = base_convert($masks[count($masks) - 1],10,2) . $bin_val;
        } else {
            $bin_val = base_convert($masks[0],10,2);
        }
        $this->bin_mask = $bin_val;
    }

    /**
    * @return void
    * @desc Sets the $mask_array variable based on the $bin_mask
    * @access private
    */
    function _set_mask_array() {
        unset($this->mask_array);
        for ($c = (strlen($this->bin_mask) - 1); $c >= 0; $c--) {
            $this->mask_array[] = $this->bin_mask{$c};
        }
    }

    function _zeroclean() {
        $this->forward_mask = preg_replace('/(-0)+$/','',$this->forward_mask);
    }

    /**
    * @return bool
    * @param unknown $bitnum
    * @desc Check if the bit at position $bitnum is set.
    */
    function bit_isset($bitnum) {
        return ($this->bin_mask{(strlen($this->bin_mask) - 1 - $bitnum)})?true:false;
    }

    /**
    * @return bool
    * @param int $bitnum
    * @desc Set the bit at location $bitnum
    */
    function set_bit($bitnum)  {
        if (!$this->bit_isset($bitnum)) {
            $masknum = (($bitnum - ($bitnum % 32)) / 32);
            $masks = explode('-',$this->forward_mask);
            if (($masknum - count($masks)) > 0) {
                for ($c = count($masks); $c <= $masknum; $c++) {
                    $masks[$c] = 0;
                }
            }
            $masks[$masknum] += pow(2,($bitnum - (32 * $masknum)));
            $this->forward_mask = implode('-',$masks);
            $this->_set_binmask();
            $this->_set_mask_array();
            return true;
        } else {
            return false;
        }
    }

    /**
    * @return bool
    * @param int $bitnum
    * @desc Unset the bit at location $bitnum
    */
    function unset_bit($bitnum) {
        if ($this->bit_isset($bitnum)) {
            $masknum = (($bitnum - ($bitnum % 32)) / 32);
            $masks = explode('-',$this->forward_mask);
            $masks[$masknum] -= pow(2,($bitnum - (32 * $masknum)));
            $this->forward_mask = implode('-',$masks);
            $this->_zeroclean();
            $this->_set_binmask();
            $this->_set_mask_array();
            return true;
        } else {
            return false;
        }
    }

    /**
    * @return bool
    * @param mixed $mask_element
    * @desc Can be either an array of values or empty.  If you wish to add empty values,
    * they can only be added in arrays where there is a non-empty value subsequent in the array.
    */
    function add_element($mask_element = true) {
        $lastbit = strlen($this->bin_mask);
        if (is_array($mask_element)) {
            foreach ($mask_element as $value) {
                if ($value) $retval = $this->set_bit($lastbit);
                $lastbit++;
            }
        } else {
            $retval = $this->set_bit(strlen($this->bin_mask));
        }
        return $retval;
    }

    /**
    * @return void
    * @param array $assoc_bits
    * @desc Allows you to enter an associative array of bit values
    */
    function add_assoc($assoc_bits) {
        foreach ($assoc_bits as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }
        $this->add_element($values);
        $this->assoc_keys = $keys;
    }

    /**
    * @return array
    * @param bool $all_values
    * @param bool $force
    * @desc Returns associative array of either all values ($all_values == true) or only
    * selected values ($all_values == false).  By setting $force to true, you can force
    * the return of an array where not all selections were taken into account due to not
    * enough key values being entered.
    */
    function assoc_get($all_values = true, $force = false) {
        if ((count($this->assoc_keys) < count($this->mask_array)) && !$force) die ('More bits than array keys');
        if ($all_values) {
            foreach ($this->assoc_keys as $key => $value) {
                $retval[$value] = $this->mask_array[$key];
            }
        } else {
            foreach ($this->assoc_keys as $key => $value) {
                if ($this->mask_array[$key]) $retval[$value] = $this->mask_array[$key];
            }
        }
        return $retval;
    }

    /**
    * @return void
    * @param string $mask
    * @desc Populates the object variables based on the value of $mask which is an integer.
    */
    function reverse_mask($mask) {
        $this->forward_mask = $mask;
        $this->_set_binmask();
        $this->_set_mask_array();
    }
}
?> <?php

/**
* Class for bit manipulations
* @author ukjpriee@ukj.pri.ee ; http://ukj.pri.ee
* v 1.1
* 2006 okt
*
* Usage:
*   // This is container string
*   $bm=chr(0).chr(0).chr(0);
*   $bo=new bitmape;
*   $bo->load($bm);
*
*   $bo->set( 1, 1 ); // now bit 1 == 1
*   $e0 = $bo->get( 13 ); // $e0 == 0
*   $b0->toggle( 12 ); //now bit 12 == 1
*   $e = $bo->swap( 11 , 1 );//return 0 and now bit 11 == 1
*   $e2 = $bo->compare( 10 , 0 );// $e2 === TRUE
*
*       bon                boff
* 0 001 00000001      0 254 11111110
* 1 002 00000010      1 253 11111101
* 2 004 00000100      2 251 11111011
* 3 008 00001000      3 247 11110111
* 4 016 00010000      4 239 11101111
* 5 032 00100000      5 223 11011111
* 6 064 01000000      6 191 10111111
* 7 128 10000000      7 127 01111111
* _ 000 00000000     _ 255 11111111
*/
class bitmape {



    /**
    * Constructor
    */
    function bitmape ( ) {
        $this->bm = NULL;
        $this->loaded = FALSE;
        $this->len = 0;
        $this->lenb = 0;
    }

    /**
    * Load/init bitmap string
    *
    * @param $s reference of string
    * @return bool TRUE if success
    */
    function load ( &$s ) {
        $this->bm = &$s;
        $this->len = strlen ( $this->bm );

        if ( $this->len == 0 )return false;

        $this->loaded = TRUE;
        $this->lenb = $this->len*8;
        return true;
    }


    /**
    * NULL object variables
    */
    function unload ( ) {
        $this->bitmape ( );
    }

    /*
    * all bits turned on, but one
    *
    * @param int $i bit position 0-7 and 8 is 11111111
    * @param bool $m mode: true = char, else in
    * @return mixed if $m === true return char, if $m === false return int
    */
    function boff ( $i,$m = true ) {
        $boff = array ( 254,253,251,247,239,223,291,127,255 );
        return ( $m?chr ( $boff[$i] ):$boff[$i] );
    }

    /**
    * all bits turned off, but one
    *
    * @param int $i bit position 0-7 and 8 is 00000000
    * @param bool $m mode: false = char, else int
    * @return mixed if $m === true return char, if $m === false return int
    */
    function bon ( $i,$m = true ) {
        $bon = array ( 1,2,4,8,16,32,64,128,0 );
        return ( $m?chr ( $bon[$i] ):$bon[$i] );
    }

    /**
    * Validtate input
    *
    * @param mixed $n
    * @return bool
    */
    function valid_n ( $n ) {
        $n = ( int )$n;
        if ( $n > $this->lenb || $n < 0 )return false;
        return true;
    }

    /**
    * Validtate and correct input
    *
    * @param mixed $v
    * @return int
    */
    function valid_v ( $v ) {
        $v = ( int )$v;
        if ( ( int ) $v > 0 )return 1;
        return 0;
    }
    /**
    * Calculate byte position in string and bit position in byte
    * // ~0.0063951015472412
    * @param int $s 0 based
    * @param int $c bufersize - 8 in this case.
    * @internal
    * $return array ( byte_num ( 0 based ),bit_num ( 1 based ) )
    */
    function locate ( $s,$bl = 8 ) {
        if ( $s < $bl )return array ( 0,$s );
        $B = ceil ( ( $s+1 )/$bl )-1;
        $i = $s- ( $B*$bl );
        return array ( $B,$i );
    }// end function locate
    /**
    * Get bit value
    *
    * @param int $n bit number 0 based
    * @return int value of requested bit 1 or 0 or FALSE
    */
    function get ( $n ) {
        if ( $this->loaded === false )return false;
        if ( !$this->valid_n ( $n ) )return false;
        $b = $this->locate ( $n );
        $sm = ord ( $this->bm[$b[0]] | $this->boff ( $b[1],true ) );
        if ( $sm == 255 )return 1;
        return 0;
    }

    /**
    * Set bit value
    *
    * @param int $n bit number 0 based
    * @param int $v value 1 or 0
    * @return boolean
    */
    function set ( $n,$v ) {
        if ( $this->loaded === false )return false;
        if ( !$this->valid_n ( $n ) )return false;
        $v = $this->valid_v ( $v );
        $this->bm[$n] = $v;
        $b = $this->locate ( $n );
        $c = $this->bm[$b[0]] | $this->bon ( $b[1],true );
        if ( $v < 1 )$c = $c ^ $this->bon ( $b[1],true );
        $this->bm[$b[0]] = $c;//put back
        return true;
    }

    /**
    * Toggle bit value
    *
    * @param int $n bit number o based
    * $return bool
    */
    function toggle ( $n ) {
        if ( $this->loaded === false )return false;
        if ( !$this->valid_n ( $n ) )return false;
        $b = $this->locate ( $n );
        $this->bm[$b[0]] = $this->bm[$b[0]] ^ $this->bon ( $b[1],true );
        return true;
    }


    /**
    * set to input value and return old value
    *
    * @param int $n bit number o based
    * @param int $v value 1 or 0
    * $return int 0 or 1, bool false if error occurs
    */
    function swap ( $n,$v ) {
        if ( $this->loaded === false )return false;
        if ( !$this->valid_n ( $n ) )return false;
        $v = $this->valid_v ( $v );
        $b = $this->locate ( $n );
        $c2 = 'b';

        //get
        $sm = ord ( $this->bm[$b[0]] | $this->boff ( $b[1],true ) );
        //set
        $c = $this->bm[$b[0]] | $this->bon ( $b[1],true );
        if ( $v < 1 )$c2 = $c ^ $this->bon ( $b[1],true );
        $this->bm[$b[0]] = $c2;//put back

        if ( $sm == 255 )return 1;
        return 0;
    }

    /**
    * Compare bit value
    *
    * @param int $n bit number
    * @param int $v Comparable value 1 or 0
    * $return bool TRUE if bit vaue is $v else FALSE
    */
    function compare ( $n,$v ) {
        if ( $this->loaded === false )return false;
        if ( !$this->valid_n ( $n ) )return false;
        $v = $this->valid_v ( $v );
        if ( $n > $this->lenb && $n > 0 )return false;
        return ( $this->get ( $this->bm,$n ) == ( int ) ( $v > 0 ) );
    }

    /**
    * print bits of bytesequence
    *
    * @param boolean $r If this parameter is set to TRUE, function will return its output, instead of printing it.
    * @return string
    */
    function print_ ( $m = false ) {
        if ( $this->loaded === false )return false;
        $out = '';
        for ( $i = 0;$i < $this->len;$i++ )
            $out .= str_pad ( decbin ( ord ( $this->bm[$i] ) ) ,8,'0',STR_PAD_LEFT ).' ';
        if ( $m === false )echo $out;
        elseif ( $m === true ) return $out;
    }
}//end class bitmape

?>