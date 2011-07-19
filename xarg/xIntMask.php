<?php


class IntMask {

	function _input() {







	}


	function _handle($argc, $args, $cb) {

		if(count($args) !== $argc) return false;

		$m = 0;
		$ret = array();

		foreach($args as &$a) {
			$a = $this->_input($a);
			$m = max($m, count($a));
		}

		for($i=0; $i < $m; $i++) {
			$ret[] = $cb(array(
			isset($args[0][$i]) ? $args[0][$i] : 0,
			isset($args[1][$i]) ? $args[1][$i] : 0,
			isset($args[2][$i]) ? $args[2][$i] : 0,
			), $i);
		}
		return $ret;
	}


	function add() {

		return $this->_handle(2, func_get_args(), function($data) {

		});
	}

	function sub() {

		return $this->_handle(2, func_get_args(), function($data) {



		});
	}

	function mul() {

	}

	function div() {

	}

	function mod() {

	}





	function bit_shl() {

	}

	function bit_shr() {

		return $this->_handle(1, func_get_args(), function($data, $pos) {
			return ~$data[0];
		});
	}

	function bit_not() {

		return $this->_handle(1, func_get_args(), function($data) {
			return ~$data[0];
		});
	}

	function bit_or() {

		return $this->_handle(2, func_get_args(), function($data) {
			return $data[0] | $data[1];
		});
	}

	function bit_nor() {

		return $this->_handle(2, func_get_args(), function($data) {
			return ~($data[0] | $data[1]);
		});
	}


	function bit_and() {

		return $this->_handle(2, func_get_args(), function($data) {
			return $data[0] & $data[1];
		});
	}

	function bit_nand() {

		return $this->_handle(2, func_get_args(), function($data) {
			return ~($data[0] & $data[1]);
		});
	}


	function bit_xor() {

		return $this->_handle(2, func_get_args(), function($data) {
			return $data[0] ^ $data[1];
		});
	}

	function bit_xnor() {

		return $this->_handle(2, func_get_args(), function($data) {
			return ~($data[0] ^ $data[1]);
		});
	}


	function bit_if() {

	}

	function bit_nif() {

	}

	function bit_iff() {

	}


	function bit_imp() {

	}

	function bit_nimp() {

	}

}





