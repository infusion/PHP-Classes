<?php

/**
 * A handy class to calculate color values.
 *
 * @version 2.0
 * @author Robert Eisele <robert@xarg.org>
 * @copyright Copyright (c) 2010, Robert Eisele
 * @license Dual licensed under the MIT or GPL Version 2 licenses.
 */


class xColor {

    public $r, $g, $b, $a, $success;
    // http://www.w3.org/TR/css3-color/#svg-color
    private $color_names = array(
	'transparent' => 16777216,
	'aliceblue' => 15792383,
	'antiquewhite' => 16444375,
	'aqua' => 65535,
	'aquamarine' => 8388564,
	'azure' => 15794175,
	'beige' => 16119260,
	'bisque' => 16770244,
	'black' => 0,
	'blanchedalmond' => 16772045,
	'blue' => 255,
	'blueviolet' => 9055202,
	'brown' => 10824234,
	'burlywood' => 14596231,
	'cadetblue' => 6266528,
	'chartreuse' => 8388352,
	'chocolate' => 13789470,
	'coral' => 16744272,
	'cornflowerblue' => 6591981,
	'cornsilk' => 16775388,
	'crimson' => 14423100,
	'cyan' => 65535,
	'darkblue' => 139,
	'darkcyan' => 35723,
	'darkgoldenrod' => 12092939,
	'darkgray' => 11119017,
	'darkgreen' => 25600,
	'darkgrey' => 11119017,
	'darkkhaki' => 12433259,
	'darkmagenta' => 9109643,
	'darkolivegreen' => 5597999,
	'darkorange' => 16747520,
	'darkorchid' => 10040012,
	'darkred' => 9109504,
	'darksalmon' => 15308410,
	'darkseagreen' => 9419919,
	'darkslateblue' => 4734347,
	'darkslategray' => 3100495,
	'darkslategrey' => 3100495,
	'darkturquoise' => 52945,
	'darkviolet' => 9699539,
	'deeppink' => 16716947,
	'deepskyblue' => 49151,
	'dimgray' => 6908265,
	'dimgrey' => 6908265,
	'dodgerblue' => 2003199,
	'firebrick' => 11674146,
	'floralwhite' => 16775920,
	'forestgreen' => 2263842,
	'fuchsia' => 16711935,
	'gainsboro' => 14474460,
	'ghostwhite' => 16316671,
	'gold' => 16766720,
	'goldenrod' => 14329120,
	'gray' => 8421504,
	'green' => 32768,
	'greenyellow' => 11403055,
	'grey' => 8421504,
	'honeydew' => 15794160,
	'hotpink' => 16738740,
	'indianred' => 13458524,
	'indigo' => 4915330,
	'ivory' => 16777200,
	'khaki' => 15787660,
	'lavender' => 15132410,
	'lavenderblush' => 16773365,
	'lawngreen' => 8190976,
	'lemonchiffon' => 16775885,
	'lightblue' => 11393254,
	'lightcoral' => 15761536,
	'lightcyan' => 14745599,
	'lightgoldenrodyellow' => 16448210,
	'lightgray' => 13882323,
	'lightgreen' => 9498256,
	'lightgrey' => 13882323,
	'lightpink' => 16758465,
	'lightsalmon' => 16752762,
	'lightseagreen' => 2142890,
	'lightskyblue' => 8900346,
	'lightslategray' => 7833753,
	'lightslategrey' => 7833753,
	'lightsteelblue' => 11584734,
	'lightyellow' => 16777184,
	'lime' => 65280,
	'limegreen' => 3329330,
	'linen' => 16445670,
	'magenta' => 16711935,
	'maroon' => 8388608,
	'mediumaquamarine' => 6737322,
	'mediumblue' => 205,
	'mediumorchid' => 12211667,
	'mediumpurple' => 9662683,
	'mediumseagreen' => 3978097,
	'mediumslateblue' => 8087790,
	'mediumspringgreen' => 64154,
	'mediumturquoise' => 4772300,
	'mediumvioletred' => 13047173,
	'midnightblue' => 1644912,
	'mintcream' => 16121850,
	'mistyrose' => 16770273,
	'moccasin' => 16770229,
	'navajowhite' => 16768685,
	'navy' => 128,
	'oldlace' => 16643558,
	'olive' => 8421376,
	'olivedrab' => 7048739,
	'orange' => 16753920,
	'orangered' => 16729344,
	'orchid' => 14315734,
	'palegoldenrod' => 15657130,
	'palegreen' => 10025880,
	'paleturquoise' => 11529966,
	'palevioletred' => 14381203,
	'papayawhip' => 16773077,
	'peachpuff' => 16767673,
	'peru' => 13468991,
	'pink' => 16761035,
	'plum' => 14524637,
	'powderblue' => 11591910,
	'purple' => 8388736,
	'red' => 16711680,
	'rosybrown' => 12357519,
	'royalblue' => 4286945,
	'saddlebrown' => 9127187,
	'salmon' => 16416882,
	'sandybrown' => 16032864,
	'seagreen' => 3050327,
	'seashell' => 16774638,
	'sienna' => 10506797,
	'silver' => 12632256,
	'skyblue' => 8900331,
	'slateblue' => 6970061,
	'slategray' => 7372944,
	'slategrey' => 7372944,
	'snow' => 16775930,
	'springgreen' => 65407,
	'steelblue' => 4620980,
	'tan' => 13808780,
	'teal' => 32896,
	'thistle' => 14204888,
	'tomato' => 16737095,
	'turquoise' => 4251856,
	'violet' => 15631086,
	'wheat' => 16113331,
	'white' => 16777215,
	'whitesmoke' => 16119285,
	'yellow' => 16776960,
	'yellowgreen' => 10145074
    );

    public function isSuccess() {
	return (bool)$this->success;
    }

    private function _normalize(&$n, $s=null) {

	if (null === $s) {
	    $n = (int)$n;
	    $s = 255;
	    $m = 255;
	} else {

	    if (1 === $s) {

		if (!isset($n)) {
		    return 1;
		}

		$s = 100;
		$m = 1;
	    } else {
		$m = $s;
	    }

	    $n = (float)$n;
	}

	if (is_nan($n) || $n <= 0) {
	    return 0;
	}

	if ($s < $n) {
	    return $m;
	}

	if ($n <= 1) {
	    if ($m === 1) {
		return $n;
	    } else {
		return ($n * $m) | 0;
	    }
	}
	return $n * $m / $s;
    }

    private function _hue($v1, $v2, $h) {
	if ($h < 0)
	    $h++;
	if ($h > 1)
	    $h--;
	if (6 * $h < 1)
	    return $v1 + ($v2 - $v1) * 6 * $h;
	if (2 * $h < 1)
	    return $v2;
	if (3 * $h < 2)
	    return $v1 + ($v2 - $v1) * (4 - 6 * $h);
	return $v1;
    }

    function _hsl($h, $s, $l) {

	$h = $this->_normalize($h, 360) / 360;
	$s = $this->_normalize($s, 1);
	$l = $this->_normalize($l, 1);

	if (0 == $s) {
	    $l = round($l * 255);
	    return array($l, $l, $l);
	}

	$v = $l < 0.5 ? ($l * (1 + $s)) : ($l + $s - $l * $s);
	$m = $l + $l - $v;

	return array(
	    round(255 * $this->_hue($m, $v, $h + 1 / 3)),
	    round(255 * $this->_hue($m, $v, $h)),
	    round(255 * $this->_hue($m, $v, $h - 1 / 3)));
    }

    private function _hsv($h, $s, $v) {

	$h = $this->_normalize($h, 360) / 60;
	$s = $this->_normalize($s, 1);
	$v = $this->_normalize($v, 1);

	$hi = $h | 0;
	$f = $h - $hi;

	if (!($hi & 1))
	    $f = 1 - $f;

	$m = round(255 * ($v * (1 - $s)));
	$n = round(255 * ($v * (1 - $s * $f)));

	$v = round(255 * $v);

	switch ($hi) {
	    case 6:
	    case 0:
		return array($v, $n, $m);
	    case 1:
		return array($n, $v, $m);
	    case 2:
		return array($m, $v, $n);
	    case 3:
		return array($m, $n, $v);
	    case 4:
		return array($n, $m, $v);
	    case 5:
		return array($v, $m, $n);
	}
    }

    public function setColor($color) {

	$this->success = true;

	switch (gettype($color)) {

	    case 'integer':
		$this->a = (($color >> 24) & 0xff) / 255;
		$this->r = ($color >> 16) & 0xff;
		$this->g = ($color >> 8) & 0xff;
		$this->b = ($color ) & 0xff;
		return;

	    case 'object':
		$color = (array)$color;
	    case 'array':
		if (isset($color[2], $color[1], $color[0])) {

		    $this->a = $this->_normalize($color[3], 1);
		    $this->r = $this->_normalize($color[0]);
		    $this->g = $this->_normalize($color[1]);
		    $this->b = $this->_normalize($color[2]);
		    return;
		} else if (isset($color['r'], $color['g'], $color['b'])) {
		    $this->a = $this->_normalize($color['a'], 1);
		    $this->r = $this->_normalize($color['r']);
		    $this->g = $this->_normalize($color['g']);
		    $this->b = $this->_normalize($color['b']);
		    return;
		} else if (isset($color['h'], $color['s'])) {
		    switch (true) {
			case isset($color['l']):
			    $rgb = $this->_hsl($color['h'], $color['s'], $color['l']);
			    break;
			case isset($color['v']):
			    $rgb = $this->_hsv($color['h'], $color['s'], $color['v']);
			    break;
			case isset($color['b']):
			    $rgb = $this->_hsv($color['h'], $color['s'], $color['b']);
			    break;
		    }
		    $this->a = $this->_normalize($color['a'], 1);
		    $this->r = $rgb[0];
		    $this->g = $rgb[1];
		    $this->b = $rgb[2];
		    return;
		}
		break;
	    case 'string':
		break;
	    default:
		$this->success = false;
		return;
	}

	$color = strtolower(preg_replace('/[^a-z0-9,.()#%]/', '', $color));

	if (isset($this->color_names[$color])) {

	    $c = $this->color_names[$color];

	    $this->a = (!(($c >> 24) & 0xff)) | 0;
	    $this->r = (($c >> 16) & 0xff);
	    $this->g = (($c >> 8) & 0xff);
	    $this->b = (($c ) & 0xff);
	    return;
	}

	// 53892983
	if (preg_match('/^([1-9]\d*)$/', $color, $part)) {

	    $c = (int)$part[1];

	    $this->a = (($c >> 24) & 0xff) / 255;
	    $this->r = (($c >> 16) & 0xff);
	    $this->g = (($c >> 8) & 0xff);
	    $this->b = (($c ) & 0xff);

	    if (empty($this->a)) {
		$this->a = 1;
	    }
	    return;
	}

	// #ff9000, #ff0000
	if (preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/', $color, $part)) {
	    $this->a = 1;
	    $this->r = hexdec($part[1]);
	    $this->g = hexdec($part[2]);
	    $this->b = hexdec($part[3]);
	    return;
	}

	// #f00, fff
	if (preg_match('/^#?([0-9a-f])([0-9a-f])([0-9a-f])$/', $color, $part)) {
	    $this->a = 1;
	    $this->r = hexdec($part[1] . $part[1]);
	    $this->g = hexdec($part[2] . $part[2]);
	    $this->b = hexdec($part[3] . $part[3]);
	    return;
	}

	// rgb(1, 234, 56)
	if (preg_match('/^rgba?\((\d{1,3}),(\d{1,3}),(\d{1,3})(,([0-9.]+))?\)$/', $color, $part)) {
	    $this->a = $this->_normalize($part[5], 1);
	    $this->r = $this->_normalize($part[1]);
	    $this->g = $this->_normalize($part[2]);
	    $this->b = $this->_normalize($part[3]);
	    return;
	}

	// rgb(66%, 55%, 44%) in [0,100]%, [0,100]%, [0,100]%
	if (preg_match('/^rgba?\(([0-9.]+\%),([0-9.]+\%),([0-9.]+\%)(,([0-9.]+)\%?)?\)$/', $color, $part)) {
	    $this->a = $this->_normalize($part[5], 1);
	    $this->r = round($this->_normalize($part[1], 100) * 2.55);
	    $this->g = round($this->_normalize($part[2], 100) * 2.55);
	    $this->b = round($this->_normalize($part[3], 100) * 2.55);
	    return;
	}

	// hsv(64, 40, 16) in [0, 360], [0,100], [0,100]
	if (preg_match('/^hs([bvl])a?\((\d{1,3}),(\d{1,3}),(\d{1,3})(,([0-9.]+))?\)$/', $color, $part)) {

	    if ($part[1] == "l") {
		$c = $this->_hsl((int)$part[2], (int)$part[3], (int)$part[4]);
	    } else {
		$c = $this->_hsv((int)$part[2], (int)$part[3], (int)$part[4]);
	    }

	    $this->a = $this->_normalize($part[6], 1);
	    $this->r = $c[0];
	    $this->g = $c[1];
	    $this->b = $c[2];
	    return;
	}

	// 1, 234, 56
	if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,3})(,([0-9.]+))?$/', $color, $part)) {
	    $this->a = $this->_normalize($part[5], 1);
	    $this->r = $this->_normalize($part[1]);
	    $this->g = $this->_normalize($part[2]);
	    $this->b = $this->_normalize($part[3]);
	    return;
	}

	$this->success = false;
    }

    public function getColor($type=null) {

	if (null !== $type)
	    switch (strtolower($type)) {
		case "rgb":
		    return $this->getRGB();
		case "hsv":
		case "hsb":
		    return $this->getHSV();
		case "hsl":
		    return $this->getHSL();
		case "int":
		    return $this->getInt();
		case "array":
		    return $this->getArray();
		case "fraction":
		    return $this->getFraction();
		case "css":
		case "style":
		    return $this->getCSS();
		case "name":
		    return $this->getName();
	    }
	return $this->getHex();
    }

    public function getRGB() {

	if ($this->success) {

	    return array(
		'r' => $this->r,
		'g' => $this->g,
		'b' => $this->b,
		'a' => $this->a
	    );
	}
	return null;
    }

    public function getCSS() {

	if ($this->success) {

	    if ($this->a == 1) {
		return 'rgb(' . $this->r . ', ' . $this->g . ', ' . $this->b . ')';
	    }
	    return 'rgba(' . $this->r . ', ' . $this->g . ', ' . $this->b . ', ' . $this->a . ')';
	}
	return null;
    }

    public function getArray() {

	if ($this->success) {
	    return array($this->r, $this->g, $this->b, $this->a * 100 | 0);
	}
	return null;
    }

    public function getName() {

	if ($this->success) {

	    $lowest = null;

	    $a = $this->getHSL();

	    foreach ($this->color_names as $k => $t) {

		/* We do not handle transparency */
		$x = new xColor($t);
		$b = $x->getHSL();

		$tmp = sqrt(0.5 * ($a['h'] - $b['h']) * ($a['h'] - $b['h']) + 0.5 * ($a['s'] - $b['s']) * ($a['s'] - $b['s']) + ($a['l'] - $b['l']) * ($a['l'] - $b['l']));

		if (null === $lowest || $tmp < $lowest) {
		    $lowest = $tmp;
		    $lowest_ndx = $k;
		}
	    }
	    return $lowest_ndx;
	}
	return null;
    }

    public function getFraction() {

	if ($this->success) {

	    return array(
		'r' => $this->r / 255,
		'g' => $this->g / 255,
		'b' => $this->b / 255,
		'a' => $this->a
	    );
	}
	return null;
    }

    public function getHSL() {

	// inspiration: http://130.113.54.154/~monger/hsl-rgb.html
	if ($this->success) {

	    $r = $this->r / 255;
	    $g = $this->g / 255;
	    $b = $this->b / 255;

	    $min = min($r, $g, $b);
	    $max = max($r, $g, $b);
	    $delta = $max - $min;

	    $l = ($max + $min) / 2;

	    if (0 == $delta) {
		$h = 0;
		$s = 0;
	    } else {

		if ($l < .5) {
		    $s = $delta / ($max + $min);
		} else {
		    $s = $delta / (2.0 - ($max + $min));
		}

		if ($max == $r) {
		    $h = ($g - $b) / $delta;
		} else if ($max == $g) {
		    $h = 2.0 + ($b - $r) / $delta;
		} else if ($max == $b) {
		    $h = 4.0 + ($r - $g) / $delta;
		}

		if ($h < 0) {
		    $h+= 6;
		}
	    }
	    return array(
		'h' => round($h * 60),
		's' => round($s * 100),
		'l' => round($l * 100),
		'a' => $this->a
	    );
	}
	return null;
    }

    public function getHSV() {

	if ($this->success) {

	    $r = $this->r / 255;
	    $g = $this->g / 255;
	    $b = $this->b / 255;

	    $min = min($r, $g, $b);
	    $max = max($r, $g, $b);
	    $delta = $max - $min;

	    $v = $max;

	    if (0 == $delta) {
		$h = 0;
		$s = 0;
	    } else {
		$s = $delta / $max;

		$delta*= 6;

		$dR = 0.5 + ($max - $r) / $delta;
		$dG = 0.5 + ($max - $g) / $delta;
		$dB = 0.5 + ($max - $b) / $delta;

		if ($r == $max) {
		    $h = $dB - $dG;
		} else if ($g == $max) {
		    $h = 1 / 3 + $dR - $dB;
		} else if ($b == $max) {
		    $h = 2 / 3 + $dG - $dR;
		}

		if ($h < 0)
		    $h++;
		if ($h > 1)
		    $h--;
	    }

	    return array(
		'h' => round($h * 360),
		's' => round($s * 100),
		'v' => round($v * 100),
		'a' => $this->a
	    );
	}
	return null;
    }

    public function getHex() {

	if ($this->success) {

	    $chars = "0123456789abcdef";

	    $r1 = $this->r >> 4;
	    $g1 = $this->g >> 4;
	    $b1 = $this->b >> 4;

	    $r2 = $this->r & 0xf;
	    $g2 = $this->g & 0xf;
	    $b2 = $this->b & 0xf;

	    if (0 == (($r1 ^ $r2) | ($g1 ^ $g2) | ($b1 ^ $b2))) {
		return '#' . $chars[$r1] . $chars[$g1] . $chars[$b1];
	    }
	    return '#'
	    . $chars[$r1] . $chars[$r2]
	    . $chars[$g1] . $chars[$g2]
	    . $chars[$b1] . $chars[$b2];
	}
	return null;
    }

    public function getInt($alpha=false) {

	if ($this->success) {
	    if ($alpha) {
		return (($this->a * 100 | 0) << 24 ^ $this->r << 16 ^ $this->g << 8 ^ $this->b);
	    }
	    return ($this->r << 16 ^ $this->g << 8 ^ $this->b) & 0xffffff;
	}
	return null;
    }

    function __construct($color) {
	$this->setColor($color);
    }

    public function __toString() {
	return $this->getHex();
    }
}

class xColorMix {

    public function test($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    return $c;
	}
	return null;
    }

    public function red($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    $c->g = 0xff;
	    $c->b = 0xff;
	    return $c;
	}
	return null;
    }

    public function blue($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    $c->r = 0xff;
	    $c->g = 0xff;
	    return $c;
	}
	return null;
    }

    public function green($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    $c->r = 0xff;
	    $c->b = 0xff;
	    return $c;
	}
	return null;
    }

    public function random() {

	return new xColor(array(
	    rand(0, 255),
	    rand(0, 255),
	    rand(0, 255),
	));
    }

    public function complementary($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    $c->r^= 0xff;
	    $c->g^= 0xff;
	    $c->b^= 0xff;
	    return $c;
	}
	return null;
    }

    public function opacity($x, $y, $o) {

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {

	    if ($o > 1) {
		$o/= 100;
	    }

	    $o = max($o - 1 + $b->a, 0);

	    $a->r = round(($b->r - $a->r) * $o + $a->r);
	    $a->g = round(($b->g - $a->g) * $o + $a->g);
	    $a->b = round(($b->b - $a->b) * $o + $a->b);

	    return $a;
	}
	return null;
    }

    public function greyfilter($col, $formula=3) {

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    switch ($formula) {
		case 1:
		    // My own formula
		    $v = .35 + 13 * ($c->r + $c->g + $c->b) / 60;
		    break;
		case 2:
		    // Sun's formula: (1 - avg) / (100 / 35) + avg)
		    $v = (13 * ($c->r + $c->g + $c->b) + 5355) / 60;
		    break;
		default:
		    $v = $c->r * .3 + $c->g * .59 + $c->b * .11;
	    }
	    $c->r = $c->g = $c->b = min($v | 0, 255);

	    return $c;
	}
	return null;
    }

    public function webround($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    if (($c->r+= 0x33 - $c->r % 0x33) > 0xff)
		$c->r = 0xff;
	    if (($c->g+= 0x33 - $c->g % 0x33) > 0xff)
		$c->g = 0xff;
	    if (($c->b+= 0x33 - $c->b % 0x33) > 0xff)
		$c->b = 0xff;
	    return $c;
	}
	return null;
    }

    public function distance($x, $y) {

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {
	    // Approximation attempt of http://www.compuphase.com/cmetric.htm
	    return sqrt(3 * ($b->r - $a->r) * ($b->r - $a->r) + 4 * ($b->g - $a->g) * ($b->g - $a->g) + 2 * ($b->b - $a->b) * ($b->b - $a->b));
	}
	return null;
    }

    public function readable($bg, $col) {

	$a = new xColor($col);
	$b = new xColor($bg);

	if ($a->isSuccess() & $b->isSuccess()) {
	    return (
	    ($b->r - $a->r) * ($b->r - $a->r) +
	    ($b->g - $a->g) * ($b->g - $a->g) +
	    ($b->b - $a->b) * ($b->b - $a->b)) > 0x28A4;
	}
	return null;
    }

    public function combine($x, $y) {

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {
	    $a->r^= $b->r;
	    $a->g^= $b->g;
	    $a->b^= $b->b;
	    return $a;
	}
	return null;
    }

    public function breed($x, $y) {

	$a = new xColor($x);
	$b = new xColor($y);

	$mask = 0;

	if ($a->isSuccess() & $b->isSuccess()) {

	    for ($i = 0; $i < 6; $i++) {
		if (rand(0, 100) < 50) {
		    $mask|= 0x0f << ($i << 2);
		}
	    }

	    $a->r = ($a->r & (($mask >> 0x10) & 0xff)) | ($b->r & ((($mask >> 0x10) & 0xff) ^ 0xff));
	    $a->g = ($a->g & (($mask >> 0x08) & 0xff)) | ($b->g & ((($mask >> 0x08) & 0xff) ^ 0xff));
	    $a->b = ($a->b & (($mask >> 0x00) & 0xff)) | ($b->b & ((($mask >> 0x00) & 0xff) ^ 0xff));
	    return $a;
	}
	return null;
    }

    public function additive($x, $y) {

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {

	    if (($a->r+= $b->r) > 0xff)
		$a->r = 0xff;
	    if (($a->g+= $b->g) > 0xff)
		$a->g = 0xff;
	    if (($a->b+= $b->b) > 0xff)
		$a->b = 0xff;

	    return $a;
	}
	return null;
    }

    public function subtractive($x, $y) {

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {

	    if (($a->r+= $b->r - 0xff) < 0)
		$a->r = 0;
	    if (($a->g+= $b->g - 0xff) < 0)
		$a->g = 0;
	    if (($a->b+= $b->b - 0xff) < 0)
		$a->b = 0;

	    return $a;
	}
	return null;
    }

    public function subtract($x, $y) {

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {

	    if (($a->r-= $b->r) < 0)
		$a->r = 0;
	    if (($a->g-= $b->g) < 0)
		$a->g = 0;
	    if (($a->b-= $b->b) < 0)
		$a->b = 0;

	    return $a;
	}
	return null;
    }

    public function multiply($x, $y) {

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {
	    $a->r = ($a->r / 255 * $b->r) | 0;
	    $a->g = ($a->g / 255 * $b->g) | 0;
	    $a->b = ($a->b / 255 * $b->b) | 0;
	    return $a;
	}
	return null;
    }

    public function average($x, $y) {

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {
	    $a->r = ($a->r + $b->r) >> 1;
	    $a->g = ($a->g + $b->g) >> 1;
	    $a->b = ($a->b + $b->b) >> 1;
	    return $a;
	}
	return null;
    }

    public function triad($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {

	    return array($c,
		new xColor(array($c->b, $c->r, $c->g)),
		new xColor(array($c->g, $c->b, $c->r)));
	}
	return null;
    }

    public function tetrad($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {

	    return array($c,
		new xColor(array($c->b, $c->r, $c->b)),
		new xColor(array($c->b, $c->g, $c->r)),
		new xColor(array($c->r, $c->b, $c->r)));
	}
	return null;
    }

    public function gradientlevel($x, $y, $level, $deg) {

	if ($level > $deg)
	    return null;

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {

	    $a->r = ($a->r + (($b->r - $a->r) / $deg) * $level) | 0;
	    $a->g = ($a->g + (($b->g - $a->g) / $deg) * $level) | 0;
	    $a->b = ($a->b + (($b->b - $a->b) / $deg) * $level) | 0;

	    return $a;
	}
	return null;
    }

    public function gradientarray($arr, $ndx, $size) {

	if ($ndx > $size)
	    return null;

	$len = count($arr);

	$e = ($ndx * ($len - 1) / $size) | 0;
	$m = ($ndx - $size * $e / ($len - 1)) / $size;

	$a = new xColor($x);
	$b = new xColor($y);

	if ($a->isSuccess() & $b->isSuccess()) {

	    $a->r = ($a->r + $len * ($b->r - $a->r) * $m) | 0;
	    $a->g = ($a->g + $len * ($b->g - $a->g) * $m) | 0;
	    $a->b = ($a->b + $len * ($b->b - $a->b) * $m) | 0;

	    return $a;
	}
	return null;
    }

    public function nearestname($a) {

	$c = new xColor($a);

	if ($c->isSuccess()) {
	    return $c->getName();
	}
	return null;
    }

    public function darken($col, $by=1, $shade=32) {

	if ($by < 0)
	    return $this->lighten($col, -$by, $shade);

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    if (($c->r-= $shade * $by) < 0)
		$c->r = 0;
	    if (($c->g-= $shade * $by) < 0)
		$c->g = 0;
	    if (($c->b-= $shade * $by) < 0)
		$c->b = 0;
	    return $c;
	}
	return null;
    }

    public function lighten($col, $by=1, $shade=32) {

	if ($by < 0)
	    return $this->darken($col, -$by, $shade);

	$c = new xColor($col);

	if ($c->isSuccess()) {
	    if (($c->r+= $shade * $by) > 0xff)
		$c->r = 0xff;
	    if (($c->g+= $shade * $by) > 0xff)
		$c->g = 0xff;
	    if (($c->b+= $shade * $by) > 0xff)
		$c->b = 0xff;
	    return $c;
	}
	return null;
    }

    public function analogous($col, $results=8, $slices=30) {

	$c = new xColor($col);

	if ($c->isSuccess()) {

	    $hsv = $c->getHSV();
	    $part = 360 / $slices;
	    $ret = array($c);

	    for ($hsv['h'] = (($hsv['h'] - ($part * $results >> 1)) + 720) % 360; --$results;) {
		$hsv['h']+= $part;
		$hsv['h']%= 360;
		$ret[] = new xColor($hsv);
	    }
	    return $ret;
	}
	return null;
    }

    public function splitcomplement($col) {

	$c = new xColor($col);

	if ($c->isSuccess()) {

	    $hsv = $c->getHSV();
	    $ret = array($c);

	    $hsv['v']+= 72;
	    $hsv['v']%= 360;
	    $ret[] = new xColor($hsv);

	    $hsv['v']+= 144;
	    $hsv['v']%= 360;
	    $ret[] = new xColor($hsv);

	    return $ret;
	}
	return null;
    }

    public function monochromatic($col, $results=6) {

	$c = new xColor($col);

	if ($c->isSuccess()) {

	    $hsv = $c->getHSV();
	    $ret = array($c);

	    while (--$results) {
		$hsv['v']+= 20;
		$hsv['v']%= 100;
		$ret[] = new xColor($hsv);
	    }
	    return $ret;
	}
	return null;
    }
}