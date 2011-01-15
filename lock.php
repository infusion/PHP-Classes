<?php

/**
 * An Application locking mechanism using DB abstraction class
 *
 * @version 1.0
 * @author Robert Eisele <robert@xarg.org>
 * @copyright Copyright (c) 2011, Robert Eisele
 * @license Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * @uses http://www.xarg.org/2010/11/transparent-query-layer-for-mysql/
 */
final class Lock {

	/**
	 * Sets the cluster for the next query
	 * If no parameter is present, the cluster of the active session is used.
	 * By default the function expects a cluster number until you use $lookup=true, then DB::getCluster() is used to obtain the cluster number
	 *
	 * @param integer $srv Server number or the number for lookup
	 * @param boolean $lookup lookup the number or use it direclty
	 */
	public final static function setCluster($srv=null, $lookup=false) {

		return DB::setCluster($srv, $lookup);
	}

	/**
	 * Acquires an application level lock
	 * @param string $name The lock name to be acquired
	 * @param integer $wait_timeout The timeout until the acquiring attempt finally fails
	 * @return boolean Signals if the lock could be obtained
	 */
	public final static function getLock($name, $wait_timeout=0) {

		switch (DB::value("SELECT GET_LOCK(#s, #i)", $name, $wait_timeout)) {
			case 1:
				return true;
			case 0:
				return false;
			default:
				throw new Exception("Lock couldn't get acquired");
		}
	}

	/**
	 * Releases an application level lock
	 * @param string $name The lock name to be released
	 * @return boolean Signals if the lock could be released
	 */
	public final static function releaseLock($name) {

		switch (DB::value('SELECT RELEASE_LOCK(#s)', $name)) {
			case 0:
				return false;
			default:
				return true;
		}
	}
}
