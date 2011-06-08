<?php

/**
 * Smarty tag cloud plugin
 *
 * @version 1.0
 * @author Robert Eisele <robert@xarg.org>
 * @copyright Copyright (c) 2008, Robert Eisele
 * @license Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * @see http://www.xarg.org/2008/08/tag-cloud-plugin-for-smarty/
 */
function smarty_function_cloud($params, &$smarty) {

	if (isset($params['tags'])) {
		$tags = $params['tags'];
	} else {
		return false;
	}

	if (isset($params['minsize'])) {
		$params['minsize'] = (int)$params['minsize'];
	} else {
		$params['minsize'] = 10;
	}

	if (isset($params['maxsize'])) {
		$params['maxsize'] = (int)$params['maxsize'];
	} else {
		$params['maxsize'] = 30;
	}

	if (isset($params['format'])) {
		$format = $params['format'];
	} else {

		$format = '<a href="';
		if (isset($params['url'])) {
			$format.= $params['url'];
		} else {
			$format.= '#';
		}
		$format.= '"';

		if (isset($params['class'])) {
			$format.= ' class="';
			$format.= $params['class'];
			$format.= '"';
		}

		if (isset($params['font'])) {
			$format.= ' style="font-size: ';
			$format.= $params['font'];
			$format.= '"';
		}
		$format.= ' rel="tag">%tag%</a>';
	}

	$min = $tags[0]['num'];
	$max = 0;

	if (isset($params['sort'])) {

		$sort = array();
		$dir = SORT_ASC;
		foreach ($tags as $k => $t) {
			if ($max < $t['num'])
				$max = $t['num'];
			if ($min > $t['num'])
				$min = $t['num'];

			switch ($params['sort']) {
				case 'random':
					$sort[$k] = rand();
					break;
				case 'alphabetic-desc':
					$dir = SORT_DESC;
				case 'alphabetic':
					$sort[$k] = $t['tag'];
					break;
				case 'weight-desc':
					$dir = SORT_DESC;
				case 'weight':
					$sort[$k] = $t['num'];
					break;
				default:
					return false;
			}
		}
		array_multisort($sort, $dir, $tags);
	} else {

		foreach ($tags as $t) {
			if ($max < $t['num'])
				$max = $t['num'];
			if ($min > $t['num'])
				$min = $t['num'];
		}
	}

	if (isset($params['limit'])) {
		$tags = array_splice($tags, 0, $params['limit']);
	}

	$delta = ($params['maxsize'] - $params['minsize']) / (($max = log($max + 2)) - ($min = log($min + 2)));

	ob_start();
	foreach ($tags as $t) {

		echo str_replace('%tag%', $t['tag'],
				str_replace('%link%', $t['link'],
						str_replace('%size%', $params['minsize'] + round((log($t['num'] + 2) - $min) * $delta),
								$format
						)
				)
		), ' ';
	}
	return ob_get_clean();
}
