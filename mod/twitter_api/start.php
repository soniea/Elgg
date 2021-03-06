<?php
/**
 * Elgg Twitter Service
 * This service plugin allows users to authenticate their Elgg account with Twitter.
 *
 * @package TwitterAPI
 */

elgg_register_event_handler('init', 'system', 'twitter_api_init');

function twitter_api_init() {

	// require libraries
	$base = elgg_get_plugins_path() . 'twitter_api';
	elgg_register_library('twitter_oauth', "$base/vendors/twitteroauth/twitterOAuth.php");
	elgg_register_library('twitter_api', "$base/lib/twitter_api.php");

	elgg_load_library('twitter_api');

	// extend site views
	elgg_extend_view('metatags', 'twitter_api/metatags');
	elgg_extend_view('css', 'twitter_api/css');

	// sign on with twitter
	if (twitter_api_allow_sign_on_with_twitter()) {
		elgg_extend_view('login/extend', 'twitter_api/login');
	}

	// register page handler
	elgg_register_page_handler('twitter_api', 'twitter_api_pagehandler');
	// backward compatibility
	elgg_register_page_handler('twitterservice', 'twitter_api_pagehandler_deprecated');

	// register Walled Garden public pages
	elgg_register_plugin_hook_handler('public_pages', 'walled_garden', 'twitter_api_public_pages');

	// allow plugin authors to hook into this service
	elgg_register_plugin_hook_handler('tweet', 'twitter_service', 'twitter_api_tweet');
}

/**
 * Handles old pg/twitterservice/ handler
 *
 * @param array$page
 */
function twitter_api_pagehandler_deprecated($page) {
	$url = elgg_get_site_url() . 'pg/twitter_api/authorize';
	$msg = elgg_echo('twitter_api:deprecated_callback_url', array($url));
	register_error($msg);

	return twitter_api_pagehandler($page);
}


/**
 * Serves pages for twitter.
 *
 * @param array$page
 */
function twitter_api_pagehandler($page) {
	if (!isset($page[0])) {
		forward();
	}

	switch ($page[0]) {
		case 'authorize':
			twitter_api_authorize();
			break;
		case 'revoke':
			twitter_api_revoke();
			break;
		case 'forward':
			twitter_api_forward();
			break;
		case 'login':
			twitter_api_login();
			break;
		default:
			forward();
			break;
	}
}

/**
 * Push a tweet to twitter.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function twitter_api_tweet($hook, $entity_type, $returnvalue, $params) {
	static $plugins;
	if (!$plugins) {
		$plugins = elgg_trigger_plugin_hook('plugin_list', 'twitter_service', NULL, array());
	}

	// ensure valid plugin
	if (!in_array($params['plugin'], $plugins)) {
		return NULL;
	}

	// check admin settings
	$consumer_key = elgg_get_plugin_setting('consumer_key', 'twitter_api');
	$consumer_secret = elgg_get_plugin_setting('consumer_secret', 'twitter_api');
	if (!($consumer_key && $consumer_secret)) {
		return NULL;
	}

	// check user settings
	$user_id = elgg_get_logged_in_user_guid();
	$access_key = elgg_get_plugin_user_setting('access_key', $user_id, 'twitter_api');
	$access_secret = elgg_get_plugin_user_setting('access_secret', $user_id, 'twitter_api');
	if (!($access_key && $access_secret)) {
		return NULL;
	}

	// send tweet
	$api = new TwitterOAuth($consumer_key, $consumer_secret, $access_key, $access_secret);
	$response = $api->post('statuses/update', array('status' => $params['message']));

	return TRUE;
}

/**
 * Return tweets for a user.
 *
 * @param int $user_id The Elgg user GUID
 * @param array $options
 */
function twitter_api_fetch_tweets($user_guid, $options=array()) {
	// check admin settings
	$consumer_key = elgg_get_plugin_setting('consumer_key', 'twitter_api');
	$consumer_secret = elgg_get_plugin_setting('consumer_secret', 'twitter_api');
	if (!($consumer_key && $consumer_secret)) {
		return FALSE;
	}

	// check user settings
	$access_key = elgg_get_plugin_user_setting('access_key', $user_guid, 'twitter_api');
	$access_secret = elgg_get_plugin_user_setting('access_secret', $user_guid, 'twitter_api');
	if (!($access_key && $access_secret)) {
		return FALSE;
	}

	// fetch tweets
	$api = new TwitterOAuth($consumer_key, $consumer_secret, $access_key, $access_secret);
	return $api->get('statuses/user_timeline', $options);
}

/**
 * Register as public pages for walled garden.
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $return_value
 * @param unknown_type $params
 */
function twitter_api_public_pages($hook, $type, $return_value, $params) {
	$return_value[] = 'twitter_api/forward';
	$return_value[] = 'twitter_api/login';

	return $return_value;
}
