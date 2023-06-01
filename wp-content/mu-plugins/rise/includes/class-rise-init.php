<?php
/**
 * Initialize the plugin and run sanity checks.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */

/**
 * This class defines all code necessary to run during the plugin's startup.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Rise_Init {

	/**
	 * TGM Plugin Activation.
	 *
	 * @return void
	 */
	public function register_required_plugins() {
		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = [
			[
				'name'               => 'WP GraphqQL',
				'slug'               => 'wp-graphql',
				'required'           => true,
				'version'            => '1.13.7',
				'force_activation'   => true,
				'force_deactivation' => false,
			],
			[
				'name'     => 'Pods - Custom Content Types and Fields',
				'slug'     => 'pods',
				'version'  => '2.9.10.2',
				'required' => true,
			],
			[
				'name'             => 'WP GraphQL CORS',
				'slug'             => 'wp-graphql-cors',
				'source'           => '/wp-graphql-cors.zip',
				'required'         => true,
				'force_activation' => true,
				'external_url'     => 'https://github.com/funkhaus/wp-graphql-cors',
			],
		];

		/**
		 * Array of configuration settings.
		 */
		$config = [
			'id'           => 'rise',
			'default_path' => dirname( __DIR__ ) . '/lib/plugins',
			'menu'         => 'tgmpa-install-plugins',
			'parent_slug'  => 'plugins.php',
			'capability'   => 'manage_options',
			'has_notices'  => true,
			'dismissable'  => true,
			'dismiss_msg'  => '',
			'is_automatic' => false,
			'message'      => '',
		];

		tgmpa( $plugins, $config );
	}

	/**
	 * Blocks the user from accessing the admin area if they are not an administrator.
	 *
	 * @since  1.0.4
	 *
	 * @return void
	 */
	public function restrict_admin_user_roles() {
		// Check if user is trying to access wp-admin
		if ( is_admin() && !is_user_logged_in() ) {
			// User is not logged in, redirect to log in
			rise_nocache_redirect( wp_login_url(), 301, 'RISE' );
			exit;
		} elseif ( is_admin() && !current_user_can( 'administrator' ) ) {
			// User is logged in but not an administrator
			wp_logout();
			rise_nocache_redirect( defined( 'RISE_FRONTEND_URL' ) ? RISE_FRONTEND_URL : home_url(), 302, 'RISE' );
			exit;
		}
	}

	/**
	 * Allow additional redirect hosts.
	 *
	 * @since  1.0.4
	 *
	 * @param  string[] $hosts
	 * @return string[] The allowed hosts.
	 */
	public function allowed_redirect_hosts( $hosts ) {
		$allowed = [
			'work.risetheatre.org',
			'risetheatre.org',
			'dev.risedirectory.pages.dev',
		];

		return array_merge( $hosts, $allowed );
	}
}
