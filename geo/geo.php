<?php


define('GEO_EARTH_RADIUS', 6371.0);

class xGeo {

	private function _parse($x) {

		$x = strtr($x, ',', '.');

		if(preg_match_all('/([\d.]+)(¬∞|‚Ä≤|‚Ä≥|"|\'|\'\')/', $x, $out)) {

			$s = 0;
			for($c=count($out[2]), $i=0; $i<$c; $i++) {

				switch($out[2][$i]) {
					case "'":
					case '‚Ä≤':
						$s+= $out[1][$i] / 3600;
						break;

					case '‚Ä≥':
					case '"':
					case "''":
						$s+= $out[1][$i] / 60;
						break;

					case '¬∞':
						$s+= $out[1][$i];
						break;
				}
			}
			return ($s);
		}

		if(($x = (double)$x) < 1) {
			return $x;
		}
		return deg2rad($x);
	}

	function _input($x) {

		switch(gettype($x)) {

			case 'array':
				if(isset($x[0], $x[1])) {

					if(gettype($x[0]) === 'string') {
						$x[0] = $this->_parse($x[0]);
					}
					if(gettype($x[1]) === 'string') {
						$x[1] = $this->_parse($x[1]);
					}
					return array((float)$x[0], (float)$x[1]);

				} else if(isset($x['lat'], $x['lon'])) {

					if(gettype($x['lat']) === 'string') {
						$x['lat'] = $this->_parse($x['lat']);
					}
					if(gettype($x['lon']) === 'string') {
						$x['lon'] = $this->_parse($x['lon']);
					}
					return array((float)$x['lat'], (float)$x['lon']);
				}
				break;

			case 'string':
				// TODO: MySQL geometry object
				if(false !== ($p = strpos($x, ' '))) {
					return array($this->_parse(substr($x, 0, $p)), $this->_parse(substr($x, $p + 1)));
				}
				$x = (float)$x;

			case 'double':
				if($x < 1) {
					break;
				}
			case 'integer':
				return array(deg2rad($x), deg2rad($x));

		}

		return array(deg2rad($x), (float)$x);
	}

	public function orientation($p1, $p2) {

		$x = $this->distanceEW($p1, $p2);
		$y = $this->distanceNS($p1, $p2);

		$ret = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW');

		if($y < 0) {
			$n = 8.5;
		} else {
			$n = 0.5;
		}
		return $ret[(int)(4 * atan2($y, $x) / M_PI + $n) & 7];
	}

	public function distance($p1, $p2) {

		$p1 = $this->_input($p1);
		$p2 = $this->_input($p2);

		return acos((sin($p1[0]) * sin($p2[0])) + (cos($p1[0]) * cos($p2[0]) * cos($p1[1] - $p2[1]))) * GEO_EARTH_RADIUS;
	}

	public function distanceEW($p1, $p2) {

		$p1 = $this->_input($p1);
		$p2 = $this->_input($p2);

		if($p1[1] > $p2[1]) {
			$dir =-1;
		} else {
			$dir = 1;
		}
		return $dir * acos(pow(sin($p1[0]), 2) + (pow(cos($p1[0]), 2) * cos($p1[1] - $p2[1]))) * GEO_EARTH_RADIUS;
	}

	public function distanceNS($p1, $p2) {

		$p1 = $this->_input($p1);
		$p2 = $this->_input($p2);

		if($p1[0] > $p2[0]) {
			$dir =-1;
		} else {
			$dir = 1;
		}
		return $dir * acos((sin($p1[0]) * sin($p2[0])) + (cos($p1[0]) * cos($p2[0]))) * GEO_EARTH_RADIUS;
	}
}





$x = new xGeo;
var_dump($x->_input('51¬∞14‚Ä≤4,2" 51¬∞14‚Ä≤4,2"'));