<?php

class Human {

	private $data = array(
		'height' => 0,
		'age' => 0,
		'weight' => 0,
	);

	function __construct($height, $weight, $age) {
		if($height < 2.5) {
			$heigt*= 100;
		}
		$this->data['height'] = (float)$height;
		$this->data['weight'] = (float)$weight;
		$this->data['age'] = (int)$age;
	}

	function getBMI(array $amputation=array()) {

		$ex = array(
		'hand' => 0.008,
		'underarm' => 0.022,
		'upperarm' => 0.085,
		'foot' => 0.018,
		'lowerleg' => 0.053,
		'upperleg' => 0,116,
		);

		$sum = 0;

		foreach($amputation as $e) {
			if(isset($ex[$e])) $sum+= $ex[$e];
		}

		$m = $this->data['weight'] / (1 - $s);

		return $m / pow($this->data['height'] / 100, 2);
	}

	function getBodyArea() {

		if($this->data['age'] < 17) {
			return sqrt($this->data['weight'] * $this->data['height'] / 3600) * 10000;
		} else {
			return pow($this->data['weight'], 0.425) * pow($this->data['height'], 0.725) * 71.84;
		}
	}



	function bmiCalculator($sex,$bmi){
		if($sex=='m' && $bmi<20 || $sex=='w' && $bmi<19)
			return 1;
		elseif($sex=='m' && $bmi>=20 && $bmi<25 || $sex=='w' && $bmi>=19 && $bmi<24)
			return 2;
		elseif($sex=='m' && $bmi>=25 && $bmi<30 || $sex=='w' && $bmi>=24 && $bmi<30)
			return 3;
		elseif($bmi>=30 && $bmi<40)
			return 4;
		elseif($bmi>=40)
			return 5;
	}

	function zodiac($day,$month,$year){
		$dat = $day/100+$month;

		// Abendlndische Sternzeichen
		$da = array(
		array('Wassermann',	01.21,02.19),
		array('Fische',		02.20,03.20),
		array('Widder',		03.21,04.20),
		array('Stier',		04.21,05.20),
		array('Zwilling',	05.21,06.20),
		array('Krebs',		06.21,07.22),
		array('L√∂we',		07.23,08.23),
		array('Jungfrau',	08.24,09.23),
		array('Waage',		09.24,10.23),
		array('Skorpion',	10.24,11.22),
		array('Schtze',	11.23,12.21),
		array('Steinbock',	12.22,01.20));
		for($i=0,$r[0]=$da[11];$i<12;$i++)
		  if($dat>=$da[$i][1]&&$dat<=$da[$i][2]){
			$r[0]=$da[$i][0];
			break;
		  }

		// Indianische Sternzeichen
		$db = array(
		array('Otter',		01.20,02.18),
		array('Wolf',		02.19,03.20),
		array('Falke',		03.21,04.19),
		array('Biber',		04.20,05.20),
		array('Hirsch',		05.21,06.20),
		array('Specht',		06.21,07.21),
		array('Lachs',		07.22,08.21),
		array('Braunb r',	08.22,09.21),
		array('Rabe',		09.22,10.22),
		array('Schlange',	10.23,11.22),
		array('Eule',		11.23,12.21),
		array('Gans',		12.22,01.19));
		for($i=0,$r[1]=$db[11];$i<12;$i++)
		  if($dat>=$db[$i][1]&&$dat<=$db[$i][2]){
			$r[1] = $db[$i][0];
			break;
		  }

		// Chinesische Sternzeichen
		$dc = array(
		'Ratte',
		'Bffel',
		'Tiger',
		'Hase',
		'Drache',
		'Schlange',
		'Pferd',
		'Ziege',
		'Affe',
		'Hahn',
		'Hund',
		'Schwein');

		$r[2] = $dc[($year-4)%12];
		return $r;
	}

}



?>