<?php


class xSequence {

	private $xf = null;

	private function _state1($arr, $num, $as_frac) {

		if($as_frac) {

			if($this->xf === null) {
				$this->xf = new xFraction;
			}

			$xf = $this->xf;

			$r = $xf->div($xf->sub($arr[1], $arr[2]), $xf->sub($arr[0], $arr[1]));
			$d = $xf->sub($arr[1], $xf->mul($arr[0], $r));

			$arr[0] = $xf->cancel($arr[0]);
			$arr[1] = $xf->cancel($arr[1]);
			$arr[2] = $xf->cancel($arr[2]);

			for($i=3; $i < $num; $i++) {

				$tmp = $xf->add($xf->mul($r, $arr[$i-1]), $d);

				if(isset($arr[$i]) && !$xf->equal($arr[$i], $tmp)) {
					return false;
				}

				$arr[$i] = $tmp;
			}

		} else {

			$r = ($arr[1] - $arr[2]) / ($arr[0] - $arr[1]);
			$d = $arr[1] - $arr[0] * $r;

			for($i=3; $i < $num; $i++) {

				$tmp = $r * $arr[$i-1] + $d;

				if(isset($arr[$i]) && $arr[$i] != $tmp) {
					return false;
				}

				$arr[$i] = $tmp;
			}
		}

		return $arr;
	}

	public function analyze($arr, $num=10, $as_frac=false) {

		if(!isset($arr[2], $arr[1], $arr[0])) {
			return false;
		}

		if(false !== ($ret = $this->_state1($arr, $num, $as_frac))) {
			return $ret;
		}
		return false;
	}
}

$x = new xSequence();

var_dump($x->analyze(
#array(30, 29, 27, 26, 24, 23, 21, 20)
array(0,1,3,6,10)
));


exit;

/* TODO
3 , 5, 8, 13, 21

3 , 4 , 8, 17, 33

30, 29, 27, 26, 24, 23, 21, 20


1,4,9,16,25,36
2,3,5,7,11,13,17
1,3,5,7,9,11,13
2,4,8,16,32,64

*/


/*
1 2 3 4

2 4 8 16 32

1 0 -1 0


1 15   3 5 8 5 13
 14 -12 2 3 -3


http://www.emath.de/Mathe-Board/messages/10/9891.html?1238093566
 */

namespace xarg;

final class xSequence {



}
