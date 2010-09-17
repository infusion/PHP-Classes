<?php

/**
 * A handy and mostly intelligent class to calculate fractions
 *
 * @version 1.0
 * @author Robert Eisele <robert@xarg.org>
 * @copyright Copyright (c) 2009, Robert Eisele
 * @link http://www.xarg.org/2009/12/handy-php-classes/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * @uses PHP Infusion Extention of Robert Eisele
 * @link http://www.xarg.org/projects/php-infusion/
 */
class xFraction {

    /**
     * Representation of a fraction
     *
     * This class offers the possebility to calculate fractions.
     * You can pass a fraction in very different form. Either as array, as double, as string or as an integer.
     *
     * Array form
     * [ 0 => <numerator>, 1 => <denumerator> ]
     * [ z => <numerator>, n => <denumerator> ]
     * [ n => <numerator>, d => <denumerator> ]
     *
     * Integer form
     * Single integer value
     *
     * Double form
     * Single double value
     *
     * String form
     * 123.456 - a simple double
     * 123,456 - a simple double in german notation
     * 123.'456' - a double with repeating all decimal places endlessly
     * 123.45'6' - a double with repeating last place endlessly
     *
     * Example:
     *
     * $xf = new xFraction;
     * $xf->div($xf->mul("9.4'31'", [-4, 3]), 4.9);
     */
    private function _gcd($a, $b) {

	while ($b) {
	    $t = $b;
	    $b = $a % $b;
	    $a = $t;
	}
	return $a;
    }


    private function _input($val) {

	switch (gettype($val)) {

	    case 'array':
		if (isset($val['z'], $val['n'])) {
		    return array((int)$val['z'], (int)$val['n']);
		} else if (isset($val['d'], $val['n'])) {
		    return array((int)$val['n'], (int)$val['d']);
		} else if (isset($val[2], $val[1], $val[0])) {
		    return array($val[0] * $val[2], $val[1]);
		} else if (isset($val[1], $val[0])) {
		    return array((int)$val[0], (int)$val[1]);
		}
		return NULL;

	    case 'integer':
		return array($val, 1);

	    case 'string':
		$val = strtr(trim($val), ',', '.');

	    case 'double':
		$val = (string)$val;

		if (strcal('0-9\'.-', $val)) {

		    if (false !== ($p = strpos($val, '.'))) {

			$int = (int)strcut($val, $p);
			$rst = stroff($val, $p + 1);

			if (false !== ($p1 = strpos($rst, '\''))) {

			    if (false !== ($p2 = strpos($rst, '\'', $p1 + 1))) {

				$rep = (int)substr($rst, $p1 + 1, $p2 - 1);

				if ($p1 === 0) {
				    $ret = $this->add(abs($int), array($rep, xround($rep) - 1));
				} else {
				    $m = (int)pow(10, $p1);
				    $ret = $this->div($this->add(abs($int) * $m + (int)strcut($rst, $p1), array($rep, xround($rep) - 1)), $m);
				}

				if ($int < 0) {
				    $ret = $this->mul($ret, -1);
				}
			    } else {
				return NULL;
			    }
			} else {
			    $rst = (int)$rst;

			    if ($int < 0) {
				$ret = $this->mul($this->add(abs($int), array($rst, xround($rst))), -1);
			    } else {
				$ret = $this->add($int, array($rst, xround($rst)));
			    }
			}

			return array($ret[0] * $ret[2], $ret[1]);
		    } else {
			return array((int)$val, 1);
		    }
		}
	}
	return NULL;
    }


    /**
     * Divide the fraction by the Greatest common divisor to get the smallest possible fraction
     *
     * @param fraction $a - fraction to cancel
     * @return a canceled fraction
     */
    public function cancel($a) {

	if (NULL !== ($a = $this->_input($a))) {
	    $g = $this->_gcd($a[0], $a[1]);
	    return array((int)abs($a[0] / $g), (int)abs($a[1] / $g), isset($a[2]) ? $a[2] : sgn($a[0] / $a[1]));
	} else {
	    return NULL;
	}
    }


    /**
     * Adds two fractions
     *
     * @param fraction $x - first fraction
     * @param fraction $y - second fraction
     * @return the sum of both fractions
     */
    public function add($x, $y) {

	if (NULL !== ($x = $this->_input($x)) && NULL !== ($y = $this->_input($y))) {
	    return $this->cancel(array(abs($a = $x[0] * $y[1] + $x[1] * $y[0]), abs($b = $x[1] * $y[1]), sgn($a / $b)));
	} else {
	    return NULL;
	}
    }


    /**
     * Subtract one fraction from another
     *
     * @param fraction $x - first fraction
     * @param fraction $y - second fraction
     * @return the difference of both fractions
     */
    public function sub($x, $y) {

	if (NULL !== ($x = $this->_input($x)) && NULL !== ($y = $this->_input($y))) {
	    return $this->cancel(array(abs($a = $x[0] * $y[1] - $x[1] * $y[0]), abs($b = $x[1] * $y[1]), sgn($a / $b)));
	} else {
	    return NULL;
	}
    }


    /**
     * Multiply two fractions
     *
     * @param fraction $x - first fraction
     * @param fraction $y - second fraction
     * @return the product of both fractions
     */
    public function mul($x, $y) {

	if (NULL !== ($x = $this->_input($x)) && NULL !== ($y = $this->_input($y))) {
	    return $this->cancel(array(abs($a = $x[0] * $y[0]), abs($b = $x[1] * $y[1]), sgn($a / $b)));
	} else {
	    return NULL;
	}
    }


    /**
     * Divide two fractions
     *
     * @param fraction $x - first fraction
     * @param fraction $y - second fraction
     * @return the quotient of both fractions
     */
    public function div($x, $y) {

	if (NULL !== ($x = $this->_input($x)) && NULL !== ($y = $this->_input($y))) {
	    return $this->cancel(array(abs($a = $x[0] * $y[1]), abs($b = $x[1] * $y[0]), sgn($a / $b)));
	} else {
	    return NULL;
	}
    }


    /**
     * Check if two fractions are equal
     *
     * @param fraction $x - first fraction
     * @param fraction $y - second fraction
     * @return the quotient of both fractions
     */
    public function equal($x, $y) {

	if (NULL !== ($x = $this->_input($x)) && NULL !== ($y = $this->_input($y))) {

	    if (($g = $this->_gcd($x[0], $x[1])) === ($h = $this->_gcd($y[0], $y[1]))) {
		return $x[0] === $y[0] && $x[1] === $y[1];
	    } else {
		return $x[0] / $g === $y[0] / $h && $x[1] / $g === $y[1] / $h;
	    }
	} else {
	    return NULL;
	}
    }


    /**
     * Get's the canceled reciprocal of a fraction
     *
     * @param fraction $x - first fraction
     * @param fraction $y - second fraction
     * @return the canceled reciprocal fraction
     */
    public function reciprocal($x) {

	if (NULL !== ($x = $this->_input($x))) {
	    return $this->cancel(array(abs($x[1]), abs($x[0]), sgn($x[0] / $x[1])));
	} else {
	    return NULL;
	}
    }


}
