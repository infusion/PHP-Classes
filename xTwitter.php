<?php

/**
 * The probably most complete twitter API written in PHP
 *
 * @version 1.0
 * @author Robert Eisele <robert@xarg.org>
 * @copyright Copyright (c) 2010, Robert Eisele
 * @link http://www.xarg.org/2010/02/php-twitter-api/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class xTwitter {

    /**
     * Current version of xTwitter.
     *
     */
    const CLIENT_VERSION = '1.0.0';


    /**
     * Our project name.
     *
     */
    const CLIENT_NAME = 'PHP xTwitter';


    /**
     * The twitter API client description file.
     *
     */
    const CLIENT_META = 'http://www.xarg.org/projects/xTwitter.xml';


    /**
     * A back reference to the description.
     *
     */
    const CLIENT_URL = 'http://www.xarg.org/2010/02/php-twitter-api/';


    /**
     * Socket connect timeout in seconds.
     *
     */
    const CLIENT_TIMEOUT = 3;


    /**
     * Maximum number of items twitter make accessable via the API.
     *
     */
    const PAGINATION_LIMIT = 3200;


    /**
     * The famous 140 as maximum length of every new status.
     *
     */
    const MAX_LENGTH = 140;


    /**
     * twitter allows the access using user id's or usernames or both (which is not implemented because it's buggy).
     * Every parameter which indicates the username ($name) can be turned to the user-id by using this flag.
     *
     */
    const USER_IS_ID = false;


    /**
     * The URL prefix for general API calls.
     *
     */
    const TWITTER_API_URL = 'http://twitter.com/';


    /**
     * The URL prefix for search API calls.
     *
     */
    const TWITTER_SEARCH_URL = 'http://search.twitter.com/';


    /**
     * The credentials cache for a running session, as we do not use HTTP Cookies.
     *
     * @var string
     */
    private $credentials = null;
    /**
     * Return type of every API call. By default an object made by json_decode() will returned.
     *
     * @var string
     */
    private $type = 'object';
    /**
     * The last HTTP status code, as twitter sometimes make use of this.
     *
     * @var int
     */
    private $last_http_status = null;

    /**
     * constructor to optionally pass credentials
     *
     * @param string $user username for authentication process
     * @param string $pass password for authentication process
     */
    public function __construct($user=null, $pass=null) {

	if ($pass !== null && $user !== $pass) {
	    $this->setAuth($user, $pass);
	}
    }


    /**
     * Defines the credentials to access private places on twitter
     *
     * @param string $user username for authentication process
     * @param string $pass password for authentication process
     */
    public function setAuth($user, $pass) {
	$this->credentials = $user . ':' . $pass;
    }


    /**
     * Defines the output type of the result. Every method has several types like XML, JSON and so on.
     * It's also possible to pass "object" (which is default) to get a json_decoded() object.
     *
     * @param string $type return type. What types are possible depends on the API method. Consult the manual.
     */
    public function setType($type) {
	$this->type = $type;
    }


    /**
     * Enter description here...
     *
     * @param unknown_type $url
     * @param unknown_type $api_key
     * @param unknown_type $callback
     * @return unknown
     */
    public function getShortUrl($url, $api_key=null, $callback=null) {

	if ($api_key === null) {
	    return $this->_request('http://api.tr.im/v1/trim_simple?url=' . urlencode($url));
	} else if ($callback === null) {
	    return $this->_request('http://api.tr.im/api/trim_url.json?url=' . urlencode($url));
	}
	return $this->_request('http://tr.im/api/trim_url.json?api_key=' . $api_key . '&url=' . urlencode($url) . '&callback=' . $callback);
    }


    ### Search API Methods

    /**
     * Returns tweets that match a specified query.
     *
     * @param string $query the search query
     * @param int $per_page number of items per page
     * @param int $page page number, starting with 1
     * @param string $lang the ISO language code
     * @return the API result or null if there was an error
     */
    public function search($query, $per_page=20, $page=1, $lang='en') {

	if ($page < 1 || $per_page * $page > xTwitter::PAGINATION_LIMIT / 2) {
	    return null;
	}
	return $this->_search('search', 'json,atom', array('rpp' => (int)$per_page, 'page' => (int)$page, 'lang' => $lang, 'q' => $query));
    }


    /**
     * Returns the top ten topics that are currently trending on Twitter.
     *
     * @return the API result
     */
    public function getTrends() {
	return $this->_search('trends', 'json', array());
    }


    /**
     * Returns the current top 10 trending topics on Twitter.
     *
     * @param string $exclude exclude something from the result
     * @return the API result
     */
    public function getCurrentTrends($exclude=null) {
	return $this->_search('trends/current', 'json', array('exclude' => $exclude));
    }


    /**
     * Returns the top 20 trending topics for each hour in a given day.
     *
     * @param int/string $date get trends by unix_timestamp (int) or by string in format YYYY-MM-DD
     * @param string $exclude exclude something from the result
     * @return the API result
     */
    public function getDailyTrends($date=0, $exclude=null) {

	if ($date === 0) {
	    $date = null;
	} else if (gettype($date) === 'integer') {
	    $date = date('Y-m-d', $date);
	}
	return $this->_search('trends/daily', 'json', array('exclude' => $exclude, 'date' => $date));
    }


    /**
     * Returns the top 30 trending topics for each day in a given week.
     *
     * @param int/string $date get trends by unix_timestamp (int) or by string in format YYYY-MM-DD
     * @param string $exclude exclude something from the result
     * @return the API result
     */
    public function getWeeklyTrends($date=0, $exclude=null) {

	if ($date === 0) {
	    $date = null;
	} else if (gettype($date) === 'integer') {
	    $date = date('Y-m-d', $date);
	}
	return $this->_search('trends/weekly', 'json', array('exclude' => $exclude, 'date' => $date));
    }


    # Timeline Methods

    /**
     * Returns the 20 most recent statuses from non-protected users who have set a custom user icon.
     *
     * @return the API result
     */
    public function getPublicTimeline() {
	return $this->_call('get', 'statuses/public_timeline', 'xml,json,rss,atom', array(), false);
    }


    /**
     * Returns the 20 most recent statuses posted by the authenticating user and that user's friends.
     *
     * @param int $per_page numbers of results per page
     * @param int $page the desired page
     * @return the API result or null if there was an error
     */
    public function getFriendTimeline($per_page=20, $page=1) {

	if ($page < 1 || $per_page * $page > xTwitter::PAGINATION_LIMIT) {
	    return null;
	}
	return $this->_call('get', 'statuses/friends_timeline', 'xml,json,rss,atom', array('page' => (int)$page, 'count' => (int)$per_page), true);
    }


    /**
     * Returns the 20 most recent statuses posted from the authenticating user.
     *
     * @param string $name get the user of a certain user
     * @param int $per_page numbers of results per page
     * @param int $page the desired page
     * @return the API result or null if there was an error
     */
    public function getUserTimeline($name=null, $per_page=20, $page=1) {

	if ($page < 1 || $per_page * $page > xTwitter::PAGINATION_LIMIT) {
	    return null;
	}
	return $this->_call('get', 'statuses/user_timeline', 'xml,json,rss,atom', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name, 'page' => (int)$page, 'count' => (int)$per_page), !empty($user));
    }


    /**
     * Returns the 20 most recent mentions (status containing @username) for the authenticating user.
     *
     * @param int $per_page numbers of results per page
     * @param int $page the desired page
     * @return the API result or null if there was an error
     */
    public function getMentions($per_page=20, $page=1) {

	if ($page < 1 || $per_page * $page > xTwitter::PAGINATION_LIMIT) {
	    return null;
	}
	return $this->_call('get', 'statuses/mentions', 'xml,json,rss,atom', array('page' => (int)$page, 'count' => (int)$per_page), true);
    }


    /**
     * Returns replies to the authenticated user.
     *
     * @param int $page the desired page
     * @param int $since the date of since when we need replies
     * @param int $since_id the id of since when we need replies
     * @return the API result
     */
    public function getReplies($page=null, $since=null, $since_id=null) {
	return $this->_call('get', 'statuses/replies', 'xml,json,rss,atom', array('page' => (int)$page, 'since' => (int)$since, 'since_id' => (int)$since_id), true);
    }


    /**
     * Returns a single status, specified by the id parameter.
     *
     * @param id $id status id
     * @return the API result
     */
    public function showStatus($id) {
	return $this->_call('get', 'statuses/show', 'xml,json', array('id' => (int)$id), false);
    }


    /**
     * Updates the authenticating user's status. Probably the most important method.
     *
     * @param string $status the status text
     * @param int $replying_to new status is in reply to another id
     * @param float $lat the latitude of the current location
     * @param unknown_type $lon the longitude of the current location
     * @return the API result or null if there was an error
     */
    public function updateStatus($status, $replying_to=null, $lat=null, $lon=null) {

	$status = preg_replace_callback('|(http://[^\s]+)|', array(&$this, '_short_url_cb'), $status);

	if (strlen($status) > xTwitter::MAX_LENGTH) {
	    return null;
	}
	return $this->updateSimple($status, $replying_to, $lat, $lon);
    }


    /**
     * Update the authenticating user's status without any modification and check
     *
     * @param string $status the status text
     * @param int $replying_to new status is in reply to another id
     * @param float $lat the latitude of the current location
     * @param unknown_type $lon the longitude of the current location
     * @return the API result
     */
    public function updateSimple($status, $replying_to=null, $lat=null, $lon=null) {
	return $this->_call('post', 'statuses/update', 'xml,json', array('status' => $status, 'in_reply_to_status_id' => $replying_to, 'lat' => $lat, 'long' => $lon), true);
    }


    /**
     * Destroys the status specified by the required ID parameter.
     *
     * @param int $id the status id
     * @return the API result
     */
    public function deleteStatus($id) {
	return $this->_call('post', 'statuses/destroy', 'xml,json', array('id' => (int)$id), true);
    }


    /**
     * Returns extended information of a given user, specified by screen name.
     *
     * @param string $name the name of the user
     * @return the API result
     */
    public function showUser($name) {
	return $this->_call('get', 'users/show', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), false);
    }


    /**
     * Returns a user's friends, each with current status inline.
     *
     * @param string $name the name of the user
     * @return the API result
     */
    public function getFriendStatus($name) {
	return $this->_call('get', 'statuses/friends', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), false);
    }


    /**
     * Returns the authenticating user's followers, each with current status inline.
     *
     * @param string $name the name of the user
     * @return the API result
     */
    public function getFollowerStatus($name) {
	return $this->_call('get', 'statuses/followers', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), false);
    }


    /**
     * Returns a list of the 20 most recent direct messages sent to the authenticating user.
     *
     * @param int $per_page number of results per page
     * @param int $page the desired page
     * @return the API result or null if there was an error
     */
    public function getDirectMessages($per_page=20, $page=1) {

	if ($page < 1 || $per_page * $page > xTwitter::PAGINATION_LIMIT) {
	    return null;
	}
	return $this->_call('get', 'direct_messages', 'xml,json', array('count' => (int)$per_page, 'page' => (int)$page), true);
    }


    /**
     * Returns a list of the 20 most recent direct messages sent by the authenticating user.
     *
     * @param int $per_page number of results per page
     * @param int $page the desired page
     * @return the API result or null if there was an error
     */
    public function getSentDirectMessages($per_page=20, $page=1) {

	if ($page < 1 || $per_page * $page > xTwitter::PAGINATION_LIMIT) {
	    return null;
	}
	return $this->_call('get', 'direct_messages/sent', 'xml,json', array('count' => (int)$per_page, 'page' => (int)$page), true);
    }


    /**
     * Sends a new direct message to the specified user from the authenticating user.
     *
     * @param string $name the recipient name
     * @param string $text the text to send
     * @return the API result
     */
    public function sendDirectMessage($name, $text) {
	return $this->_call('post', 'direct_messages/new', 'xml,json', array('text' => $text, self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Destroys the direct message specified in the required ID parameter.
     *
     * @param int $id id of the DM
     * @return the API result
     */
    public function deleteDirectMessage($id) {
	return $this->_call('post', 'direct_messages/destroy', 'xml,json', array('id' => (int)$id), true);
    }


    /**
     * Allows the authenticating users to follow the user specified in the ID parameter.
     *
     * @param string $name the name of the user to be a friend
     * @return the API result
     */
    public function createFriendship($name) {
	return $this->_call('post', 'friendships/create', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Allows the authenticating users to unfollow the user specified in the ID parameter.
     *
     * @param string $name the name of the friend we want to delete
     * @return the API result
     */
    public function deleteFriendship($id, $name) {
	return $this->_call('post', 'friendships/destroy', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Tests for the existence of friendship between two users.
     * Use getFriends() for more details and an USER_IS_ID implementation instead!
     *
     * @param string $user_a name of one user
     * @param string $user_b name of the other user
     * @return the API result
     */
    public function isFriendship($user_a, $user_b) {
	return $this->_call('get', 'friendships/exists', 'xml,json', array('user_a' => $user_a, 'user_b' => $user_b), false);
    }


    /**
     * Returns detailed information about the relationship between two users.
     *
     * @param string $user_a name of one user
     * @param string $user_b name of the other user
     * @return the API result
     */
    public function getFriendshipDetails($user_a, $user_b) {
	return $this->_call('get', 'friendships/show', 'xml,json', array(
	    self::USER_IS_ID ? 'source_id' : 'source_screen_name' => $user_a,
	    self::USER_IS_ID ? 'target_id' : 'target_screen_name' => $user_b
		), false);
    }


    /**
     * Returns an array of numeric IDs for every user the specified user is following.
     *
     * @param string $name name of the user we want to view the friends
     * @return the API result
     */
    public function getFriends($name) {
	return $this->_call('get', 'friends/ids', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), false);
    }


    /**
     * Returns an array of numeric IDs for every user following the specified user.
     *
     * @param string $name name of the user we want to view the followers
     * @return the API result
     */
    public function getFollowers($name) {
	return $this->_call('get', 'followers/ids', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), false);
    }


    /**
     * Verifies the user credentials
     *
     * @return the API result
     */
    public function verifyCredentials() {
	$ret = $this->_call('get', 'account/verify_credentials', 'xml,json', array(), true);

	if ($this->last_http_status === 401) {
	    return false;
	}
	return $ret;
    }


    /**
     * Returns the remaining number of API requests available to the requesting user before the API limit is reached for the current hour.
     *
     * @param bool $ip_limit show the ip rate or the rate of the authenticated user (default)
     * @return the API result
     */
    public function getRateStatus($ip_limit=false) {
	return $this->_call('get', 'account/rate_limit_status', 'xml,json', array(), !$ip_limit);
    }


    /**
     * Sets which device Twitter delivers updates to for the authenticating user.
     *
     * @param enum $device one of "im", "sms", "none"
     * @return the API result or null if there was an error
     */
    public function updateDeliveryDevice($device) {

	if (!in_array($device, array('im', 'sms', 'none'))) {
	    return null;
	}
	return $this->_call('post', 'account/update_delivery_device', 'xml,json', array('device' => $device), true);
    }


    /**
     * Sets one or more color values that control the color scheme of the authenticating user's profile page on twitter.com.
     *
     * @param mixed $background color of the background
     * @param mixed $text color of the text
     * @param mixed $link color of the link text
     * @param mixed $sidebar color of the sidebar
     * @param mixed $sidebar_border color of the sidebar border
     * @return the API result or null if no colors are set or readable
     */
    public function updateColors($background=null, $text=null, $link=null, $sidebar=null, $sidebar_border=null) {

	$sum = $this->_parse_color($background);
	$sum+= $this->_parse_color($text);
	$sum+= $this->_parse_color($link);
	$sum+= $this->_parse_color($sidebar);
	$sum+= $this->_parse_color($sidebar_border);

	if ($sum === 0) {
	    return null;
	}

	return $this->_call('post', 'account/update_profile_colors', 'xml,json', array(
	    'profile_background_color' => $background,
	    'profile_text_color' => $text,
	    'profile_link_color' => $link,
	    'profile_sidebar_fill_color' => $sidebar,
	    'profile_sidebar_border_color' => $sidebar_border), true);
    }


    /**
     * Updates the authenticating user's profile image.
     *
     * @param string $image file name
     * @return the API result or null if there was an error
     */
    public function updateAvatar($image) {

	if (false === ($stat = stat($image)) || $stat['size'] >= 716800) {
	    return null;
	}
	return $this->_call('post', 'account/update_profile_image', 'xml,json', array('image' => '@' . $image), true);
    }


    /**
     * Updates the authenticating user's profile background image.
     *
     * @param string $file file name
     * @param bool $tile should the background tile?
     * @return the API result or null if there was an error
     */
    public function updateBackground($file, $tile=null) {

	if (false === ($stat = stat($file)) || $stat['size'] >= 819200) {
	    return null;
	}
	return $this->_call('post', 'account/update_profile_background_image', 'xml,json', array('image' => '@' . $file, 'tile' => $tile), true);
    }


    /**
     * Sets values that users are able to set under the "Account" tab of their settings page.
     *
     * @param string $name the full name
     * @param string $url the url of the users blog/site
     * @param string $loc the location as plain text (no geo coding is involved)
     * @param string $descr a short description of the user
     * @return the API result
     */
    public function updateProfile($name=null, $url=null, $loc=null, $descr=null) {

	if ($name !== null) {
	    $name = substr($name, 0, 20);
	}

	if ($url !== null && strlen($url) > 100) {
	    $url = null;
	}

	if ($loc !== null) {
	    $loc = substr($loc, 0, 20);
	}

	if ($descr !== null) {
	    $descr = substr($descr, 0, 160);
	}

	return $this->_call('post', 'account/update_profile', 'xml,json', array(
	    'name' => $name,
	    'url' => $url,
	    'location' => $loc,
	    'description' => $descr), true);
    }


    /**
     * Returns the 20 most recent favorite statuses for the authenticating user or user specified by the ID parameter in the requested format.
     *
     * @param string $id username or user id
     * @param int $page the desired page
     * @return the API result
     */
    public function getFavorites($id=null, $page=1) {
	return $this->_call('get', 'favorites', 'xml,json,rss,atom', array('id' => $id, 'page' => (int)$page), true);
    }


    /**
     * Favorites the status specified in the ID parameter as the authenticating user.
     *
     * @param int $id status id
     * @return the API result
     */
    public function createFavorite($id) {
	return $this->_call('post', 'favorites/create', 'xml,json', array('id' => (int)$id), true);
    }


    /**
     * Un-favorites the status specified in the ID parameter as the authenticating user.
     *
     * @param int $id the status id
     * @return the API result
     */
    public function deleteFavorite($id) {
	return $this->_call('post', 'favorites/destroy', 'xml,json', array('id' => (int)$id), true);
    }


    /**
     * Enables device notifications for updates from the specified user.
     *
     * @param string $name the username
     * @return the API result
     */
    public function followNotify($name) {
	return $this->_call('post', 'notifications/follow', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Disables notifications for updates from the specified user to the authenticating user.
     *
     * @param string $name the username
     * @return the API result
     */
    public function leaveNotify($name) {
	return $this->_call('post', 'notifications/leave', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Blocks the specified user as the authenticating user.
     *
     * @param string $name the username
     * @return the API result
     */
    public function createBlock($name) {
	return $this->_call('post', 'blocks/create', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Un-blocks the user specified in the ID parameter for the authenticating user.
     *
     * @param string $name the username
     * @return the API result
     */
    public function deleteBlock($name) {
	return $this->_call('post', 'blocks/destroy', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Returns if the authenticating user is blocking a target user.
     *
     * @param string $name the username
     * @return the API result
     */
    public function isBlocked($name) {
	return $this->_call('get', 'blocks/exists', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Returns an array of user objects that the authenticating user is blocking.
     *
     * @param int $page the desired page of block listing
     * @return the API result
     */
    public function getBlocking($page=1) {
	return $this->_call('get', 'blocks/blocking', 'xml,json', array('page' => $page), true);
    }


    /**
     * Returns an array of numeric user ids the authenticating user is blocking.
     *
     * @return the API result
     */
    public function getBlockingIds() {
	return $this->_call('get', 'blocks/blocking/ids', 'xml,json', array(), true);
    }


    /**
     * The specified user is blocked by the authenticated user and reported as a spammer.
     *
     * @param string $name the username
     * @return the API result
     */
    public function reportSpam($name) {
	return $this->_call('post', 'report_spame', 'xml,json', array(self::USER_IS_ID ? 'user_id' : 'screen_name' => $name), true);
    }


    /**
     * Returns the authenticated user's saved search queries.
     *
     * @return the API result
     */
    public function getSavedSearches() {
	return $this->_call('get', 'saved_searches', 'xml,json', array(), true);
    }


    /**
     * Retrieve the data for a saved search owned by the authenticating user specified by the given id.
     *
     * @param int $id id of the saved search item
     * @return the API result
     */
    public function showSavedSearch($id) {
	return $this->_call('get', 'saved_searches/show', 'xml,json', array('id' => (int)$id), true);
    }


    /**
     * Creates a saved search for the authenticated user.
     *
     * @param string $query
     * @return the API result
     */
    public function createSavedSearch($query) {
	return $this->_call('post', 'saved_searches/create', 'xml,json', array('query' => $query), true);
    }


    /**
     * Destroys a saved search for the authenticated user.
     *
     * @param int $id id of the saved search item
     * @return the API result
     */
    public function deleteSavedSearch($id) {
	return $this->_call('post', 'saved_searches/destroy', 'xml,json', array('id' => (int)$id), true);
    }


    /**
     * Returns the string "ok" in the requested format.
     *
     * @return the API result
     */
    public function test() {
	return $this->_call('get', 'help/test', 'xml,json', array(), false);
    }


    protected function _call($method, $url, $types, $param, $auth=true) {

	$param = array_filter($param);

	if ($this->type === 'object') {
	    $type = 'json';
	} else {
	    $type = $this->type;
	}

	if (false === strpos($types, $type)) {
	    return null;
	}

	$url = self::TWITTER_API_URL . $url . '.' . $type;

	if ($method === 'post') {
	    if (empty($param)) {
		return null;
	    }
	    $ret = $this->_request($url, $param, $auth);
	} else {
	    $ret = $this->_request($url . '?' . http_build_query($param), null, $auth);
	}

	if ($this->type === 'object') {
	    return json_decode($ret);
	}
	return $ret;
    }


    protected function _search($url, $types, $param) {

	$param = http_build_query(array_filter($param));

	if ($this->type === 'object') {
	    $type = 'json';
	} else {
	    $type = $this->type;
	}

	if (false === strpos($types, $type)) {
	    return null;
	}

	$ret = $this->_request(self::TWITTER_SEARCH_URL . $url . '.' . $type . '?' . $param);

	if ($this->type === 'object') {
	    return json_decode($ret);
	}
	return $ret;
    }


    private function _short_url_cb($arr) {

	if (strlen($arr[1]) > 18 /* strlen('http://tr.im/aaaaa') */) {
	    return $this->getShortUrl($arr[1]);
	}
	return $arr[1];
    }


    private function _parse_color(&$col) {

	switch (gettype($col)) {
	    case 'NULL':
		return 0;

	    case 'string':
		$col = trim($col, '#');

		switch (strlen($col)) {
		    case 3:
			$a = str_split($col);
			$a[0].= $a[0];
			$a[1].= $a[1];
			$a[2].= $a[2];
			$col = array(hexdec($a[0]), hexdec($a[1]), hexdec($a[2]));
			break 2;

		    case 6:
			$a = str_split($col, 2);
			$col = array(hexdec($a[0]), hexdec($a[1]), hexdec($a[2]));
			break 2;

		    default:
			$col = null;
			return 0;
		}

	    case 'integer':
		$col = array(($col >> 16) & 0xff, ($col >> 8) & 0xff, $col & 0xff);
		break;

	    case 'object':
		$col = (array)$col;

	    case 'array':
		switch (true) {
		    case isset($col[0], $col[1], $col[2]):
			$col = array((int)$col[0], (int)$col[1], (int)$col[2]);
			break 2;

		    case isset($col['r'], $col['g'], $col['b']):
			$col = array((int)$col['r'], (int)$col['g'], (int)$col['b']);
			break 2;

		    default:
			$col = null;
			return 0;
		}
	}
	$col = sprintf('%02x%02x%02x', $col[0], $col[1], $col[2]);

	return 1;
    }


    private function _request($host, $data=null, $auth=false) {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $host);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CLIENT_TIMEOUT);
	curl_setopt($ch, CURLOPT_USERAGENT, self::CLIENT_NAME . '/' . self::CLIENT_VERSION . ' +' . self::CLIENT_URL);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Expect:", // fucking bug in lighttpd
	    'X-Twitter-Client: ' . self::CLIENT_NAME,
	    'X-Twitter-Client-URL: ' . self::CLIENT_META,
	    'X-Twitter-Client-Version: ' . self::CLIENT_VERSION
	));

	if ($data !== null) { // POST
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}

	if ($auth && $auth !== empty($this->credentials)) {
	    curl_setopt($ch, CURLOPT_USERPWD, $this->credentials);
	}

	$data = curl_exec($ch);
	$info = curl_getinfo($ch);
	$this->last_http_status = $info['http_code'];

	curl_close($ch);
	return $data;
    }


}
