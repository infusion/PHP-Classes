<?php

class xCsv extends xMath {

	var $types = array('ARR', 'CSV');

	var $in  = 1;
	var $out = 1;
	var $sep = ',';

	public function setType($from, $to) {

		if('' != $from) {
			if(false === ($this->in = array_search($from, $this->types, true))) {
				return false;
			}
		}

		if('' != $to) {
			if(false === ($this->out = array_search($to, $this->types, true))) {
				return false;
			}
		}
		return true;
	}

	public function setSeperator($sep) {
		$this->sep = $sep;
	}

	###

	private function _input($val) {

		switch($this->in) {
			case 0:
				return (array)$val;
			case 1:
				return $this->_str2arr($val);
		}

		return false;
	}

	private function _output($val) {

		switch($this->out) {
			case 0:
				return (array)$val;
			case 1:
				return $this->_arr2str($val);
		}

		return false;
	}

	private function _arr2str($val) {
		return implode($this->sep, $val);
	}

	private function _str2arr($val) {
		return explode($this->sep, $val);
	}

	public function append($hay, $val) {

		$a = $this->_input($hay);
		array_push($a, $val);
		return $this->_output($a);
	}

	public function prepend($hay, $val) {

		$a = $this->_input($hay);
		array_unshift($a, $val);
		return $this->_output($a);
	}

	public function insert($hay, $pos, $val) {

		$a = $this->_input($hay);
		if(!$this->between($pos, 0, count($a) - 1)) {
			array_push($a, $val);
		} else {
			$a = array_merge(array_slice($a, 0, $pos), array($val), array_slice($a, $pos));
		}
		return $this->_output($a);
	}

	public function update($hay, $pos, $val) {

		$a = $this->_input($hay);
		if($this->between($pos, 0, count($a) - 1)) {
			$a[$pos] = $val;
		}
		return $this->_output($a);
	}

	public function increment($hay, $pos, $val) {

		$a = $this->_input($hay);
		if($this->between($pos, 0, count($a) - 1)) {
			$a[$pos]+= $val;
		}
		return $this->_output($a);
	}

};

?>