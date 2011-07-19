<?php


class Matrix{


	function transponieren($m){
		$a = count($m);
		for($j=0;$j<$a;$j++){
			$b = count($m[$j]);
			for($i=0;$i<$b;$i++){
				$n[$i][$j] = $m[$j][$i];
			}
		}
		return $n;
	}

};


?>