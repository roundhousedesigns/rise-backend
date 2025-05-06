<?php
/**
 * Plugin Name: pexlechris_adminer_avoid_conflicts_with_other_plugins.php
 * Description: This mu plugin disables on the fly all other plugins to avoid conflicts.
 * 				Version is controlled by option pexlechris_adminer_mu_plugin_version.
 * 				Delete option to reinstall, or set option to 0 to ignore version updates forever
 *  Version: 4.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}



add_filter( 'option_active_plugins', function( $plugins ){

	$plugin_constructor_file = 'pexlechris-adminer/pexlechris-adminer.php';

	if( !in_array( $plugin_constructor_file, $plugins ) ){
		return $plugins;
	}

	include_once WP_PLUGIN_DIR . '/pexlechris-adminer/pluggable-functions.php';

	// Only disable all other plugins, when WP Adminer will be shown
	if( function_exists('pexlechris_is_current_url_the_wp_adminer_url') &&
		pexlechris_is_current_url_the_wp_adminer_url() &&
		pexlechris_adminer_only_this_plugin_active()
	){
		return [$plugin_constructor_file];
	}

	return $plugins;
} );

/**
 * Determines whether all other plugins should be disabled.
 *
 * In some cases, the current user is not yet set, which can cause a fatal error.
 * To prevent this, we check the cookie instead.
 *
 * No validation is performed on the cookie, as this function is only used
 * to determine whether all other plugins should be deactivated on the fly
 * when accessing WP Adminer.
 *
 * @since 4.0.0
 *
 * @return bool
 */
function pexlechris_adminer_only_this_plugin_active()
{
	if ( defined('SECURE_AUTH_COOKIE') ) {

		$adminer_caps = function_exists('pexlechris_adminer_access_capabilities')
			? pexlechris_adminer_access_capabilities()
			: [];

		foreach ($adminer_caps as $capability) {
			require_once ABSPATH . WPINC . '/pluggable.php';
			if( current_user_can($capability) ){
				return true;
			}
		}

		return false;

	}else{


		if (empty($_COOKIE)) {
			return false;
		}

		$logged_in_cookie = array_filter($_COOKIE, function ($k) {
			return strpos($k, 'wordpress_logged_in_') === 0;
		}, ARRAY_FILTER_USE_KEY);

		if (empty($logged_in_cookie)) {
			return false;
		}

		$logged_in_cookie = array_pop($logged_in_cookie);

		$logged_in_cookie_elements = explode('|', $logged_in_cookie);

		if (count($logged_in_cookie_elements) !== 4) {
			return false;
		}

		list($username, $expiration, $token, $hmac) = $logged_in_cookie_elements;


		$current_user = new WP_User(0, $username);

		$adminer_caps = function_exists('pexlechris_adminer_access_capabilities')
			? pexlechris_adminer_access_capabilities()
			: [];

		foreach ($adminer_caps as $capability) {
			if( user_can($current_user, $capability) ){
				return true;
			}
		}

		return false;


	}

}