<?php

final class cUrl {

	private static $info = null;
	private static $data = null;

	private static $header = 0;
	private static $method = 0;

	private static $referer = null;
	private static $agent = 'xPyron/0.8 (+http://www.xarg.org/)';

	public static function doGet($url) {

		cUrl::$header = 0;
		cUrl::$method = 0;

		return cUrl::_request($url);
	}

	public static function doPost($url, $data, $raw = false) {

		cUrl::$header = 0;
		cUrl::$method = 1;

		if ($raw) {
			return cUrl::_request($url, $data);
		} else {
			$p = '';
			$s = false;

			if(is_array($data)) foreach($data as $k => $d) {

				if ($s) {
					$p.= '&';
				} else {
					$s = true;
				}
				$p.= urlencode($k).'='.urlencode($d);
			}
			return cUrl::_request($url, $p);
		}
	}

	public static function doHead($url) {

		cUrl::$header = 1;
		cUrl::$method = 2;

		return cUrl::_request($url);
	}

	public static function setAgent($agent){
		cUrl::$agent = $agent;
	}

	public static function setReferer($ref=null){
		cUrl::$referer = $ref;
	}

	public static function getInformation() {
		return cUrl::$info;
	}

	private static function _request($host, $data=null) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 				$host);
		curl_setopt($ch, CURLOPT_HEADER,			cUrl::$header);
		curl_setopt($ch, CURLOPT_USERAGENT,			cUrl::$agent);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,	1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,	1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,	3);

		if(null !== cUrl::$referer) {
			curl_setopt($ch, CURLOPT_REFERER,		cUrl::$referer);
		}

		if (1 === cUrl::$method) {
			curl_setopt($ch, CURLOPT_POST,			1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,	$data);
		} else if (2 === cUrl::$method) {
			curl_setopt($ch, CURLOPT_NOBODY,		1);
		}

		cUrl::$data = curl_exec($ch);
		cUrl::$info = curl_getinfo($ch);

		curl_close($ch);
		return cUrl::$data;
	}

}

