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

    foreach ($values as $v) {
	if ($r <= $v)
	    return $k;
	$r-= $v;
    }
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
    return log($n) / log(2);
}

function readable_byte($byte) {

    static $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

    $e = (int)(log($byte) / log(1024));

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

    $tok = strtok($version, '._');

    for ($i = 0, $str = ''; $tok !== false; $tok = strtok('._')) {

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
