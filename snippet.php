<?php

function str_random($len=32, $base=62) {

	static $chars = '0123456789abcdefABCDEFghijklmnopqrstuvwxyzGHIJKLMNOPQRSTUVWXYZ';

	$fd = fopen('/dev/urandom', 'rb');
	$str = fread($fd, $len);
	fclose($fd);

	while ($len--) {
		$str[$len] = $chars[(int)(ord($str[$len]) / 256 * $base)];
	}
	return $str;
}

function array_extract_key($arr, $key) {
	$ret = array();

	foreach ($arr as &$a) {

		if (isset($a[$key])) {
			$ret[$a[$key]] = $a;
			unset($a[$key]);
		} else {
			return null;
		}
	}
	return $ret;
}

function array_weighted_rand($values) {
	$r = mt_rand(1, array_sum($values));

	foreach ($values as $k => $v) {
		if ($r <= $v)
			return $k;
		$r-= $v;
	}
	return null;
}

function array_mask($arr, $mask) {
	$ret = array();

	foreach ($arr as $a) {
		if ($mask & 1) {
			$ret[] = $a;
		}
		$mask>>= 1;
	}
	return $ret;
}

function log2($n) {
	return log($n) / M_LN2;
}

function readable_byte($byte) {

	static $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

	$e = (int)(log($byte) / (M_LN2 * 10));

	return sprintf('%.2f' . $s[$e], $byte / pow(1024, $e));
}

function array_psum($arr, $start, $len) {

	$i = 0;
	$sum = 0;

	$len+= $start;

	foreach ($arr as $a) {
		if ($i > $len)
			break;
		if ($i > $start)
			$sum+= $a;
		++$i;
	}
	return $sum;
}

function array_assocsort(&$data, $col, $dir=SORT_DESC) {

	$tmp = array();
	foreach ($data as $k => &$v) {
		$tmp[$k] = &$v[$col];
	}
	array_multisort($tmp, $dir, $data);
}

function normalize_angle($angle) {
	return (360 + $angle % 360) % 360;
}

function normalize_version($version, $num=2) {

	$tok = strtok($version, '._,');

	for ($i = 0, $str = ''; $tok !== false; $tok = strtok('._,')) {

		if ($i > $num)
			break;
		if (++$i > 1)
			$str.= '.';
		$str.= $tok;
	}
	return $str;
}

function xsprintf($str, $cb, $ch='%') {

	if (empty($ch) || !is_callable($cb)) {
		return $str;
	}

	for ($ret = "", $i = 0, $l = strlen($str); $i < $l; $i++) {

		if ($ch[0] == $str[$i]) {

			for ($start = ++$i; $i < $l; ++$i) {
				if (ctype_alpha($str[$i]))
					$ret.= $cb(substr($str, $start, $i - $start + 1));
				else
					continue;
				break;
			}
		} else
			$ret.= $str[$i];
	}
	return $ret;
}

function gpp($x) {

	$f = 1;
	$n = 1;

	if ($x <= 0) {
		return 1;
	}

	for (;; $n++) {

		if (($nn = $n * $n) >= $x) {
			break;
		} if (0 == ($x % $nn)) {
			$f = $n;
		}
	}
	return $f;
}

function possessive($str) {
	return $str . (substr($str, -1) === 's' ? "'" : "'s");
}

function strcut($str, $max, $c="...") {

	$sl = strlen($str);
	$cl = strlen($c);

	if ($max <= 0) {
		return false;
	}

	if ($sl - $cl > $max) {

		for ($i = min($sl - 1, $max); $i >= 0; $i--) {
			switch ($str[$i]) {
				case ' ':
				case"\t":
				case "\r":
				case "\n":
					break 2;
			}
		}

		if ($i == -1) {
			$len = min($sl, $max);
		} else {
			$len = $i;
		}

		return substr($str, 0, $len) . $c;
	} else {
		return $str;
	}
}

function rotbit($bit, $m) {

	$m = (31 + ($m % 31)) % 31;
	return (($bit << $m) | ($bit >> (15 - $m))) & 0x7FFFFFFF;
}

function rotint($n, $x, $y, $m) {

	if ($y < $x || $x < 1) {
		return false;
	}

	$y-= $x;
	$m = ($y + ($m % $y)) % $y;

	$a = (1 << $y) - 1;
	$b = ($n >> $x) & $a;

	return ($n & (~($a << $x))) | (((($b << $m) | ($b >> ($y - $m))) & $a) << $x);
}

function utf8_chr($c) {

	if ($c < 0x80)
		return chr($c);
	if ($c < 0x800)
		return pack('CC', ($c >> 0x06) + 0xC0, (($c >> 0x00) & 0x3F) + 0x80);;
	if ($c < 0x10000)
		return pack('CCC', ($c >> 0x0C) + 0xE0, (($c >> 0x06) & 0x3F) + 0x80, (($c >> 0x00) & 0x3F) + 0x80);
	if ($c < 0x200000)
		return pack('CCCC', ($c >> 0x12) + 0xF0, (($c >> 0x0C) & 0x3F) + 0x80, (($c >> 0x06) & 0x3F) + 0x80, ($c & 0x3F) + 0x80);
}

function simple_gif($r=0, $g=0, $b=0, $t=true) {

	if ($t) {
		$t = '21f90401000001002c00000000010001000002024c01003b';
	} else {
		$t = '2c00000000010001000002024c01003b';
	}
	return pack('H32CCCH*', '47494638396101000100900100ffffff', $r, $g, $b, $t);
}

function array_squares($n) {

	$x = (int)sqrt($n);
	$y = 0;
	$r = array();

	do {

		if ($x < $y)
			break;

		$sum = $x * $x + $y * $y;

		if ($sum < $n) {
			++$y; continue;
		}

		if ($sum > $n) {
			--$x; continue;
		}
		$r[] = array($x--, $y++);
	} while (1);

	return $r;
}