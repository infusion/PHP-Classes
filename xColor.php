<?php

/**
 * A handy class to calculate color values.
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
define('T_COLOR_INT', 0);
define('T_COLOR_HEX', 1);
define('T_COLOR_RGB', 2);
define('T_COLOR_HSL', 3);
define('T_COLOR_HSV', 4);

class xColor {

    private $in = T_COLOR_HEX;
    private $out = T_COLOR_HEX;

    /**
     * Describes the input and output handling
     *
     * @param enum $from - one of INT, HEX, RGB, HSL, HSV
     * @param enum $to - one of INT, HEX, RGB, HSL, HSV
     * @return - whether the input range is correct
     */
    public function setType($from, $to) {

	if (0 <= $from && $from <= 4 && 0 <= $to && $to <= 4) {
	    $this->in = (int)$from;
	    $this->out = (int)$to;
	    return true;
	} else {
	    return false;
	}
    }


    ###

    protected function _input($col) {

	switch ($this->in) {
	    case 0:
		return $this->_int2rgb($col);
	    case 1:
		return $this->_hex2rgb($col);
	    case 2:
		return $col;
	    case 3:
		return $this->_hsl2rgb($col);
	    case 4:
		return $this->_hsv2rgb($col);
	}

	return false;
    }


    protected function _output($rgb) {

	switch ($this->out) {
	    case 0:
		return $this->_rgb2int($rgb);
	    case 1:
		return $this->_rgb2hex($rgb);
	    case 2:
		return $rgb;
	    case 3:
		return $this->_rgb2hsl($rgb);
	    case 4:
		return $this->_rgb2hsv($rgb);
	}

	return false;
    }


    protected function _int2rgb($col) {
	return array(($col >> 16) & 0xff, ($col >> 8) & 0xff, $col & 0xff);
    }


    protected function _hex2rgb($hex) {
	$hex = str_replace('#', '', $hex);

	switch (strlen($hex)) {
	    case 3:
		$a = str_split($hex);
		$a[0].= $a[0];
		$a[1].= $a[1];
		$a[2].= $a[2];
		return array(hexdec($a[0]), hexdec($a[1]), hexdec($a[2]));
		break;
	    case 6:
		$a = str_split($hex, 2);
		return array(hexdec($a[0]), hexdec($a[1]), hexdec($a[2]));
		break;
	}
	return false;
    }


    protected function _hsl2rgb($hls) {

	$rgb = array(0, 0, 0);

	$m2 = ($hls[1] <= .5) ? ($hls[1] * (1 + $hls[2])) : ($hls[1] + $hls[2] * (1 - $hls[1]));
	$m1 = 2 * $hls[1] - $m2;

	if (!$hls[2]) {
	    if ($hls[0] === null) {
		$rgb[0] = $rgb[1] = $rgb[2] = $hls[1];
	    } else {
		return false;
	    }
	} else {
	    $rgb[0] = $this->_hval($m1, $m2, $hls[0] + 120);
	    $rgb[1] = $this->_hval($m1, $m2, $hls[0]);
	    $rgb[2] = $this->_hval($m1, $m2, $hls[0] - 120);
	}

	for ($c = 0; $c < 3; ++$c) {
	    $rgb[$c] = round($rgb[$c] * 255);
	}
	return $rgb;
    }


    protected function _hsv2rgb($hsv) {

	$rgb = array();

	if (!$hsv[1]) {
	    if (0 === $hsv[0]) {
		$rgb[0] = $rgb[1] = $rgb[2] = $hsv[2];
	    } else {
		return false;
	    }
	} else {
	    if (360 === $hsv[0]) {
		$hsv[0] = 0;
	    }

	    $hsv[0]/= 60;
	    $i = floor($hsv[0]);
	    $f = $hsv[0] - $i;
	    $p = $hsv[2] * (1 - $hsv[1]);
	    $q = $hsv[2] * (1 - ($hsv[1] * $f));
	    $t = $hsv[2] * (1 - ($hsv[1] * (1 - $f)));

	    switch ($i) {
		case 0:
		    $rgb[0] = $hsv[2];
		    $rgb[1] = $t;
		    $rgb[2] = $p;
		    break;
		case 1:
		    $rgb[0] = $q;
		    $rgb[1] = $hsv[2];
		    $rgb[2] = $p;
		    break;
		case 2:
		    $rgb[0] = $p;
		    $rgb[1] = $hsv[2];
		    $rgb[2] = $t;
		    break;
		case 3:
		    $rgb[0] = $p;
		    $rgb[1] = $q;
		    $rgb[2] = $hsv[2];
		    break;
		case 4:
		    $rgb[0] = $t;
		    $rgb[1] = $p;
		    $rgb[2] = $hsv[2];
		    break;
		case 5:
		    $rgb[0] = $hsv[2];
		    $rgb[1] = $p;
		    $rgb[2] = $q;
		    break;
	    }
	}

	$rgb[0] = round($rgb[0] * 255);
	$rgb[1] = round($rgb[1] * 255);
	$rgb[2] = round($rgb[2] * 255);

	return $rgb;
    }


    protected function _rgb2int($rgb) {
	return (($rgb[0] << 16) + ($rgb[1] << 8) + $rgb[2]) & 0xffffff;
    }


    protected function _rgb2hex($rgb) {
	return sprintf('%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    }


    protected function _rgb2hsl($rgb) {

	$hsl = array();
	$min = max($rgb);
	$min = min($rgb);

	$hsl[2] = ($max + $min) / 2;

	if ($max === $min) { // R = G = B
	    $hsl[0] = 0;
	    $hsl[1] = 0;
	} else {
	    $delta = $max - $min;

	    if ($hsl[2] <= .5) {
		$hsl[1] = $delta / ($max + $min);
	    } else {
		$hsl[1] = $delta / (2 - ($max + $min));
	    }

	    if ($max === $rgb[0] && $rgb[1] >= $rgb[2]) {
		$hsl[0] = 60 * ($rgb[1] - $rgb[2]) / $delta;
	    } else if ($max === $rgb[0] && $rgb[1] < $rgb[2]) {
		$hsl[0] = 60 * ($rgb[1] - $rgb[2]) / $delta + 360;
	    } else if ($max === $rgb[1]) {
		$hsl[0] = 60 * ($rgb[2] - $rgb[0]) / $delta + 120;
	    } else if ($max === $b) {
		$hsl[0] = 60 * ($rgb[0] - $rgb[1]) / $delta + 240;
	    }
	}
	return $hsl;
    }


    protected function _rgb2hsv($rgb) {

	$hsv = array();
	$max = max($rgb);
	$min = min($rgb);

	$hsv[1] = $max ? 1 - $max / $min : 0;
	$hsv[2] = $max;

	if (!$hsv[1]) {
	    $hsv[0] = 0;
	} else {
	    $delta = $max - $min;

	    if ($max === $rgb[0] && $rgb[1] >= $rgb[2]) {
		$hsv[0] = 60 * ($rgb[1] - $rgb[2]) / $delta;
	    } else if ($max === $rgb[0] && $rgb[1] < $rgb[2]) {
		$hsv[0] = 60 * ($rgb[1] - $rgb[2]) / $delta + 360;
	    } else if ($max === $rgb[1]) {
		$hsv[0] = 60 * ($rgb[2] - $rgb[0]) / $delta + 120;
	    } else if ($max === $b) {
		$hsv[0] = 60 * ($rgb[0] - $rgb[1]) / $delta + 240;
	    }
	}
	return $hsv;
    }


    protected function _hval($n1, $n2, $h) {

	if ($h > 360) {
	    $h-= 360;
	} else if ($h < 0) {
	    $h+= 360;
	}

	if ($h < 60) {
	    return $n1 + ($n2 - $n1) * $h / 60;
	} else if ($h < 180) {
	    return $n2;
	} else if ($h < 240) {
	    return $n1 + ($n2 - $n1) * (240 - $h) / 60;
	} else {
	    return $n1;
	}
    }


    ###

    /**
     * Gets the red portion of a color
     *
     * @param color $c - the color
     * @return returns the red portion of the color
     */
    public function red($c) {

	$x = $this->_input($c);

	$r = array(
	    $x[0],
	    0xff,
	    0xff
	);

	return $this->_output($r);
    }


    /**
     * Gets the green portion of a color
     *
     * @param color $c - the color
     * @return returns the green portion of the color
     */
    public function green($c) {

	$x = $this->_input($c);

	$r = array(
	    0xff,
	    $x[1],
	    0xff
	);

	return $this->_output($r);
    }


    /**
     * Gets the blue portion of a color
     *
     * @param color $c - the color
     * @return returns the blue portion of the color
     */
    public function blue($c) {

	$x = $this->_input($c);

	$r = array(
	    0xff,
	    0xff,
	    $x[2]
	);

	return $this->_output($r);
    }


    /**
     * Gets a random color
     *
     * @return returns a random number
     */
    public function random() {

	$r = array(
	    rand(0, 255),
	    rand(0, 255),
	    rand(0, 255)
	);

	return $this->_output($r);
    }


    /**
     * Gets a merged color with the respect of an alpha value
     *
     * @param color $b - the first color
     * @param color $f - the second color
     * @param int $o - alpha value
     * @return returns the color of both colors combined with an alpha value
     */
    public function opacity($b, $f, $o) {

	if ($o > 1) {
	    $o/= 100;
	}

	$x = $this->_input($f);
	$y = $this->_input($b);

	$r = array(
	    (int)(($x[0] - $y[0]) * $o + $y[0]),
	    (int)(($x[1] - $y[1]) * $o + $y[1]),
	    (int)(($x[2] - $y[2]) * $o + $y[2]),
	);

	return $this->_output($r);
    }


    /**
     * Inverts a color
     *
     * @param color $n - the color
     * @return returns the inverted color
     */
    public function invert($n) {

	$r = $this->_input($n);

	$r[0]^= 0xff;
	$r[1]^= 0xff;
	$r[2]^= 0xff;

	return $this->_output($r);
    }


    /**
     * Get's a XOR'ed combination of both colors
     *
     * @param color $a - the first color
     * @param color $b - the second color
     * @return returns the XOR'ed combination color of both colors
     */
    public function combine($a, $b) {

	$r = $this->_input($a);
	$s = $this->_input($b);

	$r[0]^= $s[0];
	$r[1]^= $s[0];
	$r[2]^= $s[0];

	return $this->_output($r);
    }


    /**
     * Get's the additive color mixing of 2 colors
     *
     * @param color $b - the first color
     * @param color $f - the second color
     * @return returns the additive color of both colors
     */
    public function additive($b, $f) {

	$x = $this->_input($b);
	$y = $this->_input($f);

	$r = array(
	    limit($x[0] + $y[0], 0xff),
	    limit($x[1] + $y[1], 0xff),
	    limit($x[2] + $y[2], 0xff)
	);

	return $this->_output($r);
    }


    /**
     * Get's the subtractive color mixing of 2 colors
     *
     * @param color $b - the first color
     * @param color $f - the second color
     * @return returns the subtractive color of both colors
     */
    public function subtractive($b, $f) {

	$x = $this->_input($b);
	$y = $this->_input($f);

	$r = array(
	    bound($x[0] + $y[0] - 0xff, 0, 0xff),
	    bound($x[1] + $y[1] - 0xff, 0, 0xff),
	    bound($x[2] + $y[2] - 0xff, 0, 0xff)
	);

	return $this->_output($r);
    }


    /**
     * Get's the difference color of 2 colors
     *
     * @param color $b - the first color
     * @param color $f - the second color
     * @return returns the difference of both colors
     */
    public function subtract($b, $f) {

	$x = $this->_input($b);
	$y = $this->_input($f);

	$r = array(
	    bound($x[0] - $y[0], 0, 0xff),
	    bound($x[1] - $y[1], 0, 0xff),
	    bound($x[2] - $y[2], 0, 0xff)
	);

	return $this->_output($r);
    }


    /**
     * Get's the multiply color of 2 colors
     *
     * @param color $b - the first color
     * @param color $f - the second color
     * @return returns a multiply of both colors
     */
    public function multiply($b, $f) {

	$x = $this->_input($b);
	$y = $this->_input($f);

	$r = array(
	    (int)((($x[0] / 0xff) * ($y[0] / 0xff)) * 0xff),
	    (int)((($x[1] / 0xff) * ($y[1] / 0xff)) * 0xff),
	    (int)((($x[2] / 0xff) * ($y[2] / 0xff)) * 0xff)
	);

	return $this->_output($r);
    }


    /**
     * Get's a average color of both colors
     *
     * @param color $a - the first color
     * @param color $b - the second color
     * @return returns an average of both colors
     */
    function mix($a, $b) {

	$x = $this->_input($a);
	$y = $this->_input($b);

	$r = array(
	    ($x[0] + $y[0]) >> 1,
	    ($x[1] + $y[1]) >> 1,
	    ($x[2] + $y[2]) >> 1
	);

	return $this->_output($r);
    }


    /**
     * Get's a lighten color
     *
     * @param color $col - the color
     * @param int $deg - the step width
     * @return returns a lighten color
     */
    function lightness($col, $deg = 10) {

	$c = $this->_input($col);

	$r = array(
	    bound($c[0] + $deg, 0, 0xff),
	    bound($c[1] + $deg, 0, 0xff),
	    bound($c[2] + $deg, 0, 0xff)
	);

	return $this->_output($r);
    }


    /**
     * Get's a color with parts of both colors
     *
     * @param color $x - the color
     * @param color $y - the second color
     * @return returns a secondary color
     */
    public function breed($x, $y) {

	$a = $this->_input($x);
	$b = $this->_input($y);

	$mask = 0;
	for ($i = 0; $i < 6; ++$i) {
	    if (rand(0, 1)) {
		$mask|= 0x0f << ($i << 2);
	    }
	}

	return $this->_output($this->_int2rgb(($this->_rgb2int($a) & $mask) | ($this->_rgb2int($b) & ($mask ^ 0xffffff))));
    }


    /**
     * Get's the grey scale replacement of a color
     *
     * @param color $x - the color
     * @return returns a grayscale color
     */
    public function greyscale($x) {

	$a = $this->_input($x);

	$y = (int)($a[0] * .3 + $a[1] * .59 + $a[2] * .11);

	// Percentage: (255 - $y) * 100 / 255
	return $this->_output(array($y, $y, $y));
    }


    /**
     * Get's a web safe color
     *
     * @param color $c - the color
     * @return returns a websafe color calculated from the parameter
     */
    public function webround($c) {

	$x = $this->_input($c);

	$r = array(
	    bround($x[0], 0x33),
	    bround($x[1], 0x33),
	    bround($x[2], 0x33)
	);

	return $this->_output($r);
    }


    /**
     * Get's a readable text color dependent on the background
     *
     * @param color $col - the background color
     * @param color $light - return this color if background is dark
     * @param color $dark - return this color if background is light
     * @return returns color $light or $dark dependent on the background color
     */
    function textcolor($col, $light, $dark) {

	if (false === $this->isReadable($col, $light, 0x66)) {
	    return $this->_output($this->_input($dark));
	} else {
	    return $this->_output($this->_input($light));
	}
    }


    /**
     * Check if a color is readable on a certain background
     *
     * @param color $col - foreground/text color
     * @param color $bg - background color
     * @param color $thld - the threshold for the decision
     * @return returns bool whether the threshold is reached
     */
    function isReadable($col, $bg, $thld=0x66) {

	$col = $this->_input($col);
	$bg = $this->_input($bg);

	for ($p = 0, $i = 0; $i < 3; $i++) {
	    $p+= ( $bg[$i] - $col[$i]) * ($bg[$i] - $col[$i]);
	}

	return sqrt($p) > $thld;
    }


    /**
     * Get's the distance between two colors
     *
     * @param color $x - the first color
     * @param color $y - the second color
     * @return returns the numerical distance between two colors
     */
    function distance($x, $y) {

	$a = $this->_input($x);
	$b = $this->_input($y);

	return sqrt(3 * ($b[0] - $a[0]) * ($b[0] - $a[0]) + 4 * ($b[1] - $a[1]) * ($b[1] - $a[1]) + 2 * ($b[2] - $a[2]) * ($b[2] - $a[2]));
    }


    /**
     * Get's the colors between two colors
     *
     * @param color $x - the first color
     * @param color $y - the second color
     * @param int $deg - parts between color one and two
     * @return returns an array of all color parts between the colors
     */
    function gradient($x, $y, $deg = 10) {

	$ret = array();

	if (0 === $deg) {
	    $deg = 1;
	}

	$a = $this->_input($x);
	$b = $this->_input($y);

	$rstep = ($b[0] - $a[0]) / $deg;
	$gstep = ($b[1] - $a[1]) / $deg;
	$bstep = ($b[2] - $a[2]) / $deg;

	for ($i = 0; $i < $deg; ++$i) {
	    $nc[0] = bound($a[0] + $rstep * $i, 0, 0xff);
	    $nc[1] = bound($a[1] + $gstep * $i, 0, 0xff);
	    $nc[2] = bound($a[2] + $bstep * $i, 0, 0xff);
	    $ret[] = $this->_output($nc);
	}

	return $ret;
    }


    /**
     * Gets the level in a fixed size gradient
     *
     * @param color $x - the first color
     * @param color $y - the second color
     * @param int $deg - parts between color one and two
     * @param int $level - which part do we want
     * @return returns the addressed colour
     */
    function gradientLevel($x, $y, $deg, $level) {

	if ($level > $deg)
	    return NULL;

	$a = $this->_input($x);
	$b = $this->_input($y);

	$r = array(
	    bound($a[0] + (($b[0] - $a[0]) / $deg) * $level, 0, 0xff),
	    bound($a[1] + (($b[1] - $a[1]) / $deg) * $level, 0, 0xff),
	    bound($a[2] + (($b[2] - $a[2]) / $deg) * $level, 0, 0xff)
	);

	return $this->_output($r);
    }


}
