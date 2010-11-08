<?php

/**
 * A MySQLi connection handler and abstraction class
 *
 * @version 1.0
 * @author Robert Eisele <robert@xarg.org>
 * @copyright Copyright (c) 2010, Robert Eisele
 * @license Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * @uses http://www.xarg.org/project/php-infusion/
 * @uses http://www.firephp.org/
 *
 * @see http://www.xarg.org/2010/11/transparent-query-layer-for-mysql/
 */
define('BASE', 'http://www.example.com');

define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'node');
define('DB_FAIL', '/var/www/error500.html');

define('CLUSTER_SIZE', 32);

if (!extension_loaded('infusion')) {

    function mysqli_safex($str) {
	return "'" . addslashes($str) . "'";
    }
}

final class DB {

    private static $debug = false; // requires FirePHP
    private static $work = array();
    private static $data = array();
    private static $args = array();
    private static $con = array();
    private static $a = 0;
    private static $cur = null;
    private static $result = 0; // MYSQLI_STORE_RESULT
    private static $options = array();
    private static $pre_query = null;

    /**
     * Sets option parameters for the pagination abstraction
     *
     * @param array $opt can contain the following elements:
      'table'	    : optional table name, if you wish to access the temporary table
      'sort'	    : sort column
      'dir'	    : sort direction (asc/desc)
      'offset'    : page offset
      'limit'	    : page limit
     */
    public final static function setOptions(array $opt) {
	DB::$options = $opt;
    }

    /**
     * Sets the read result mode.
     *
     * @param integer $r Either the constant MYSQLI_USE_RESULT or MYSQLI_STORE_RESULT depending on the desired behavior can be used as parameter
     */
    public final static function setStoreResult($r) {
	DB::$result = (int)$r;
    }

    /**
     * Sets the cluster for the next query (will also be called transparently with #o and #u)
     * If no parameter is present, the cluster of the active session is used.
     * By default the function expects a cluster number until you use $lookup=true, then getCluster() is used to obtain the cluster number
     *
     * @param integer $srv Server number or the number for lookup
     * @param boolean $lookup lookup the number or use it direclty
     */
    public final static function setCluster($srv=null, $lookup=false) {

	if (null === $srv) {
	    DB::$work[$_SESSION['SRV']] = 1;
	} else {
	    DB::$work[$lookup === true ? DB::getCluster($srv) : (int)$srv] = 1;
	}
    }

    /**
     * Gets the cluster number of the given entity number.
     * If the given number falls into a range gap, a lookup is performed to get the right cluster node.
     *
     * @param integer $uid entity (or more specific user-id)
     * @return integer cluster number
     */
    public final static function getCluster($uid) {

	if ($uid % CLUSTER_SIZE) {
	    return $uid % CLUSTER_SIZE;
	} if (isset($_SESSION['UID']) && $uid === $_SESSION['UID']) {
	    return $_SESSION['SRV'];
	}

	/* Handle lookup if not calculateable */

	if (!isset(DB::$con[0])) {
	    DB::_connect(apc_fetch('database-0'), 0);
	}

	mysqli_select_db(DB::$con[0], DB_NAME . '0');

	$x = mysqli_fetch_row(mysqli_query(DB::$con[0], 'SELECT UCluster FROM user WHERE UID=' . (int)$uid . ' LIMIT 1'));

	/* TODO: cache result */

	if (isset($x[0])) {
	    return $x[0];
	}

	return 0;
    }

    /**
     * Performs a query on the "super" global cluster node(0)
     *
     * WARNING: do not use #u and #o with super()!
     *
     * @param string $query Query format string
     * @param mixed ... Query parameters
     * @return <type>
     */
    public final static function super($query) {
	DB::$args = func_get_args();

	DB::setCluster(0);
	return DB::_read($query);
    }

    /**
     * Gets a single value (single column of a single row) out of a result
     *
     * @param string $query Query format string
     * @param mixed ... Query parameters
     * @return mixed The value of the result
     */
    public final static function &value($query) {
	DB::$args = func_get_args();

	$ref = &DB::$data[];
	$tmp = mysqli_fetch_row($res = DB::_read($query));
	$ref = $tmp[0];
	mysqli_free_result($res);
	return $ref;
    }

    /**
     * Gets all results as array
     *
     * @param string $query Query format string
     * @param mixed ... Query parameters
     * @return array The full resultset
     */
    public final static function &gets($query) {
	DB::$args = func_get_args();

	$ref = &DB::$data[];
	$ref = mysqli_fetch_all($res = DB::_read($query), MYSQLI_ASSOC);
	mysqli_free_result($res);
	return $ref;
    }

    /**
     * Gets a single row out of a result
     *
     * @param string $query Query format string
     * @param mixed ... Query parameters
     * @return array The value of the result
     */
    public final static function &get($query) {
	DB::$args = func_get_args();

	$ref = &DB::$data[];
	$ref = mysqli_fetch_assoc($res = DB::_read($query));
	mysqli_free_result($res);
	return $ref;
    }

    /**
     * A query to perform before another query is performed (like SET @x:=0)
     *
     * @param string $query Query format string
     * @param mixed ... Query parameters
     */
    public final static function preRead($query) {
	DB::$args = func_get_args();

	DB::$pre_query = xsprintf($query, 'DB::_escape', '#');
	DB::$args = array(); DB::$a = 0;
    }

    /**
     * Performs a read query on a randomly choosen slave
     *
     * @param string $query Query format string
     * @param mixed ... Query parameters
     * @return ressource The mysqli result
     */
    public final static function read($query) {
	DB::$args = func_get_args();

	return DB::_read($query);
    }

    /**
     * Performs a write query on all affected masters
     *
     * @param string $query Query format string
     * @param mixed ... Query parameters
     */
    public final static function write($query) {
	DB::$args = func_get_args();

	$query = xsprintf($query, 'DB::_escape', '#');

	foreach (DB::$work as $q => $d) {
	    if (empty(DB::$con[$p = $q | 0x1000000])) {
		$tmp = apc_fetch('database-' . $q);
		if ($q > 0) {
		    DB::_connect($tmp['master'], $p);
		} else {
		    DB::_connect($tmp, $p);
		}
	    }
	    DB::$cur = &DB::$con[$p];

	    mysqli_select_db(DB::$cur, DB_NAME . $q);

	    if (false === ($res = mysqli_query(DB::$cur, $query))) {
		DB::_log($query);
	    }
	} DB::$work = DB::$args = array(); DB::$a = 0;
    }

    /**
     * Redurects the user agent to a different location and closes all connections
     *
     * @param string $url Destination URL
     * @param boolean $abs Destination URL is absolute
     * @param boolean $perm Destination URL is redirected permanent
     */
    public final static function locate($url='/', $abs=false, $perm=false) {

	header($perm ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 302 Found');
	header('Connection: close'); // stfu IE

	if (false === $abs || '/' === $url) {
	    header('Location: ' . BASE . $url);
	} else {
	    header('Location: ' . $url);
	}
	DB::close();
    }

    /**
     * Closes all opened connections and exits the script
     *
     * @param boolean $exit Weather to terminate the script
     */
    public final static function close($exit = true) {
	foreach (DB::$con as $c)
	    mysqli_close($c);
	if (true === $exit)
	    exit;
    }

    /**
     * Internal read handler, which is used by all read functions
     * It also provides a pagination abstraction to the user
     *
     * @todo Find a better pagination abstraction
     *
     * @param string $query Query format string
     * @param mixed ... Query parameters
     * @return ressource MySQLi result of the query
     */
    public final static function _read($query) {

	$query = xsprintf($query, 'DB::_escape', '#');

	foreach (DB::$work as $q => $d) {
	    if (isset(DB::$con[$q])) {
		DB::$cur = &DB::$con[$q];
		break;
	    }
	} DB::$work = DB::$args = array(); DB::$a = DB::$result = 0;

	if (empty(DB::$con[$q])) {
	    $r = apc_fetch('database-' . $q);
	    if ($q > 0) {
		$r = $r['slaves'];
		DB::_connect($r[array_rand($r)], $q);
	    } else {
		DB::_connect($r, $q);
	    }
	    DB::$cur = &DB::$con[$q];
	}

	mysqli_select_db(DB::$cur, DB_NAME . $q);

	if (null !== DB::$pre_query) {
	    mysqli_query(DB::$cur, DB::$pre_query);
	    DB::$pre_query = null;
	}

	$s = microtime(true);
	if (array() === DB::$options) {
	    if (false === ($res = mysqli_query(DB::$cur, $query, DB::$result))) {
		DB::_log($query);
	    }
	} else {

	    if (empty(DB::$options['table'])) {
		$table = '_rndtbl' . rand(1000, 9999);
	    } else {
		$table = DB::$options['table'];
	    }

	    $param = DB::$options['sort'];

	    if (isset($opt['dir'])) {
		$param.= ' ' . $opt['dir'];
	    }

	    if (false === ($res = mysqli_query(DB::$cur, 'CREATE TEMPORARY TABLE ' . $table . ' (KEY SORT(' . $param . ')) ' . $query))) {
		DB::_log($query);
	    } else {

		if (isset($opt['offset'])) {
		    $offset = (int)$opt['offset'];
		} else {
		    $offset = '0';
		}

		mysqli_query(DB::$cur, 'ALTER TABLE ' . $table . ' ADD OFFSET INT UNSIGNED PRIMARY KEY AUTO_INCREMENT, DROP INDEX SORT, ORDER BY ' . $param);

		if (isset($opt['limit'])) {
		    $param.= ' LIMIT ';
		    $param.= (int)$opt['limit'];
		}

		$res = mysqli_query(DB::$cur, 'SELECT * FROM ' . $table . ' WHERE OFFSET >=' . $offset . ' ORDER BY OFFSET' . $limit);
	    }

	    DB::$options = array();
	}

	if (DB::$debug) {
	    FB::log((microtime(true) - $s) . " - " . $query);
	}

	return $res;
    }

    /**
     * Connect to a single server and store the connection handle in a list
     *
     * @param string $srv Hostname or IP of the server
     * @param int $id ID of the server in the cluster
     */
    private final static function _connect($srv, $id) {

	if (false === (DB::$con[$id] = mysqli_connect($srv, DB_USER, DB_PASS))) {
	    header('HTTP/1.1 503 Service Unavailable');
	    header('Retry-After: 3600');
	    readfile(DB_FAIL);
	    exit;
	}
	mysqli_options(DB::$con[$id], MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
    }

    /**
     * Internal callback for hash-codes
     *
     * @param char $c The current code after a hash
     * @return string Returns the string value of the parameter or something else to the hash-code
     */
    private static function _escape($c) {

	switch ($c) {

	    case 'u':
		DB::setCluster($_SESSION['SRV']);
	    case 'U':
		return $_SESSION['UID'];

	    case 's':
		return mysqli_safe(DB::$args[++DB::$a]);

	    case 'i':
		return (int)DB::$args[++DB::$a];

	    case 'f':
		return (float)DB::$args[++DB::$a];

	    case 'o':

		$val = &DB::$args[++DB::$a];

		if (isset($val['UID'])) {
		    if (isset($val['UCluster']))
			DB::setCluster($val['UCluster']);
		    else
			DB::setCluster($val['UID'], true);

		    return $val['UID'];
		} else {
		    DB::setCluster($val, true);
		    return (int)$val;
		}

	    case 'x':
		return '0x' . bin2hex(DB::$args[++DB::$a]);
	    case 'X':
		return '0x' . DB::$args[++DB::$a];

	    case 'n':
		if (empty(DB::$args[++DB::$a]))
		    return 'NULL';
		return mysqli_safe(DB::$args[DB::$a]);

	    case 'h':
		return mysqli_safe(htmlspecialchars(DB::$args[++DB::$a]));

	    case 'r':
		return sprintf('%u', ip2long(DB::$args[++DB::$a]));

	    case 'd':
		return date("'Y-m-d'", DB::$args[++DB::$a]);
	    case 't':
		return date("'Y-m-d H:m:s'", DB::$args[++DB::$a]);

	    case 'v':
		return empty(DB::$args[++DB::$a]) ? 'NULL' : "''"; // Bool

	    case 'a':
		return implode(',', array_map('mysqli_safe', DB::$args[++DB::$a]));

	    case 'J':
		$fields = DB::$args[++DB::$a];

		$str = "CONCAT('[',GROUP_CONCAT(CONCAT('{\"";

		for ($i = 0, $l = count($fields); $i < $l; ++$i) {
		    $str.= $fields[$i] . "\":',QUOTE(" . $fields[$i];

		    if ($i != $l - 1) {
			$str.= "),'\",\"";
		    }
		}
		$str.= "),'}')),']')";

		return $str;
	}
	return 'FAILED';
    }

    /**
     * Loggs the query and the message when an error has occured
     *
     * @param string $query The full query
     */
    private final static function _log($query) {

	$msg = 'SQL Error:  ' . mysqli_error(DB::$cur) . ' (' . $query . ') @(' . mysqli_get_host_info(DB::$cur) . ')';

	if (isset($_SERVER['REQUEST_URI'])) {
	    $msg.= ' in ' . $_SERVER['REQUEST_URI'];
	}

	if (DB::$debug) {
	    FB::error($msg);
	} else {
	    error_log($msg);
	}
    }
}
