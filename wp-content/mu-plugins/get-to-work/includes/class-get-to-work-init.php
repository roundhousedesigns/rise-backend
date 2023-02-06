<?php
/**
 * Initialize the plugin and run sanity checks.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Get_To_Work_Init {

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
			// [
			// 	'name'             => 'WP GraphQL CORS',
			// 	'slug'             => 'wp-graphql-cors',
			// 	'source'           => '/wp-graphql-cors.zip',
			// 	'required'         => true,
			// 	'force_activation' => true,
			// 	'external_url'     => 'https://github.com/funkhaus/wp-graphql-cors',
			// ],
			// [
			// 	'name'         => 'WP GraphQL for Advanced Custom Fields',
			// 	'slug'         => 'wp-graphql-acf-master',
			// 	'source'       => /wp-graphql-acf.zip',
			// 	'required'     => true,
			// 	'external_url' => 'https://github.com/wp-graphql/wp-graphql-acf',
			// ],
			// [
			// 	'name'     => 'Advanced Custom Fields',
			// 	'slug'     => 'advanced-custom-fields',
			// 	'required' => true,
			// ],
		];

		/**
		 * Array of configuration settings.
		 */
		$config = [
			'id'           => 'gtw',
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

}
