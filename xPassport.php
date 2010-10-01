<?php

/**
 * A handy class to check a german passport number.
 *
 * @version 1.0
 * @author Robert Eisele <robert@xarg.org>
 * @copyright Copyright (c) 2009, Robert Eisele
 * @link http://www.xarg.org/2009/12/handy-php-classes/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class xPassport {

    private function subcalc($str) {
	$len = strlen($str);
	$x = 0;

	for ($i = 0; $i < $len; $i++) {
	    $x+= $str{$i} * ((1 << (3 - $i % 3)) - 1);
	}

	return $x % 10;
    }

    private function strip($str) {
	preg_match('|([0-9]{9})([0-9]{1})d<<([0-9]{6})([0-9]{1})<([0-9]{6})([0-9]{1})<{7}([0-9]{1})|i', $str, $ret);
	if (count($ret) == 8) {
	    return $ret;
	} else {
	    return false;
	}
    }

    /**
     * Validates a passport number
     *
     * @param string $str - Passport number in the format nnnnnnnnnnd<<nnnnnnn<nnnnnnn<<<<<<<n
     * @return returns true or false dependent on that the number is valid
     */
    public function validate($str) {

	if (($e = $this->strip($str)) === false) {
	    return false;
	}

	for ($i = 1; $i < 6; $i+= 2) {
	    if ($this->subcalc($e[$i]) != $e[$i + 1]) {
		return false;
	    }
	}

	$ret = '';
	for ($i = 1; $i < 7; $i++) {
	    $ret.= $e[$i];
	}

	if ($this->subcalc($ret) != $e[7]) {
	    return false;
	}
	return true;
    }

    /**
     * Get's the age of the person with this passport number
     *
     * @param string $str - Passport number in the format nnnnnnnnnnd<<nnnnnnn<nnnnnnn<<<<<<<n
     * @return returns the age of the person
     */
    public function getAge($str) {

	if ($this->validate($str)) {
	    if (($e = $this->strip($str)) === false) {
		return false;
	    }

	    $b = array();
	    for ($i = 0; $i < 6; $i+= 2) {
		$b[] = substr($e[3], 4 - $i, 2);
	    }

	    /**
	     * For details of the accuracy of this calculation see:
	     * @link http://www.xarg.org/2009/12/age-calculation-with-mysql/
	     */
	    return (int)(($_SERVER["REQUEST_TIME"] - mktime(0, 0, 0, $b[1], $b[0], '19' . $b[2])) / 31560000);
	} else {
	    return 0;
	}
    }

    /**
     * Checks if the person with this passport number reached a given age - let's say 18
     *
     * @param string $str - Passport number in the format nnnnnnnnnnd<<nnnnnnn<nnnnnnn<<<<<<<n
     * @return returns true or false dependent on that the person is old enough
     */
    public function checkAge($str, $age) {
	if ($this->getage($str) >= $age) {
	    return true;
	} else {
	    return false;
	}
    }

    /**
     * Gets the issuing date of the passport
     *
     * @param string $str - Passport number in the format nnnnnnnnnnd<<nnnnnnn<nnnnnnn<<<<<<<n
     * @return returns timestamp of the issuing date
     */
    public function getIssuingDate($str) {

	if ($this->validate($str)) {
	    if (($e = $this->strip($str)) === false) {
		return false;
	    }

	    $b = array();
	    for ($i = 0; $i < 6; $i+= 2) {
		$b[] = substr($e[4], 4 - $i, 2);
	    }

	    return mktime(0, 0, 0, $b[1], $b[0], '19' . $b[2]);
	} else {
	    return 0;
	}
    }
}
