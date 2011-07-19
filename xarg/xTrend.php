<?php

class Trend {

	/* welches gewicht soll f√ºr die intelligente berechnung jeder stunde zugemessen werden? */
	private $hourWeight = array(1,1,1,2,3,3,3,4,4,6,6,8,9,10,11,11,12,11,13,9,8,4,3,2);

	/* summe von $hourWeight, reicht ja einmalig und muss nicht immer neu berechnet werden */
	private $hourSum = 145;

	/*
	trend √ºber eine anzahl von tagen zur√ºck. alpha ist zum justieren.
	ich denke ideal ist etwas um 2/3
	*/
	public function getTrend($array, $alpha) {

		if (empty($array)) {
			return 0;
		}

		$r = $array[0];

		foreach ($array as $a) {
			$r = $alpha * $a + (1 - $alpha) * $r;
		}
		return $r;
	}

	/* intelligente trend-analyse. aktueller tag ist im array enthalten. */
	public function getIntelligentTrend($array, $alpha) {

		$current = array_pop($array);

		if (($trend = $this->getTrend($array, $alpha)) < $current) {

			$hour = (int)date('H');

			for ($sum = 0, $i = 23; $hour <= $i; $i--) {
				echo $i,"\n";
				$sum+= $this->hourWeight[$i];
			}
			return $current + ($current - $trend) * ($sum / $this->hourSum);
		}
		return $trend;
	}
}

// 1031 @19 uhr
$t = new Trend;
echo $t->getTrend(array(
600, 200, 97, 133, 254, 204, 50, 200
), 2/3);

// 1031 @19 uhr
$t = new Trend;
echo $t->getIntelligentTrend(array(
600, 200, 97, 133, 254, 204, 50, 200,900
), 2/3);





class Trend {
	/* welches gewicht soll f√ºr die intelligente berechnung jeder stunde zugemessen werden? */

	private $hourWeight = array(1, 1, 1, 2, 3, 3, 3, 4, 4, 6, 6, 8, 9, 10, 11, 11, 12, 11, 13, 9, 8, 4, 3, 2);

	/* summe von $hourWeight, reicht ja einmalig und muss nicht immer neu berechnet werden */
	private $hourSum = 145;

	/*
	  trend √ºber eine anzahl von tagen zur√ºck. alpha ist zum justieren.
	  ich denke ideal ist etwas um 2/3
	 */

	public function getTrend($array, $alpha) {

		if (empty($array)) {
			return 0;
		}

		$r = $array[0];

		foreach ($array as $a) {
			$r = $alpha * $a + (1 - $alpha) * $r;
		}
		return $r;
	}

	/* intelligente trend-analyse. aktueller tag ist im array enthalten. */

	public function getIntelligentTrend($array, $alpha) {

		$current = array_pop($array);

		if (($trend = $this->getTrend($array, $alpha)) < $current) {

			$hour = (int) date('H');

			for ($sum = 0, $i = 23; $hour <= $i; $i--) {
				echo $i, "\n";
				$sum+= $this->hourWeight[$i];
			}
			return $current + ($current - $trend) * ($sum / $this->hourSum);
		}
		return $trend;
	}

}

$t = new Trend;

echo $t->getTrend(array(
    600, 200, 97, 133, 254, 204, 50, 200
	), 2 / 3);