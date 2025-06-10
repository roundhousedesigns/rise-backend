<?php

namespace RHD\Rise\Includes;

/**
 * Initialize the plugin and run sanity checks.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Includes
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */

/**
 * This class defines all code necessary to run during the plugin's startup.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Init {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var string $plugin_name The ID of this plugin.
	 * @since    0.1.0
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var string $version The current version of this plugin.
	 * @since    0.1.0
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

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
			[
				'name'               => 'WooCommerce',
				'slug'               => 'woocommerce',
				'required'           => true,
				'version'            => '9.8.5',
				'force_activation'   => true,
				'force_deactivation' => false,
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

		\tgmpa( $plugins, $config );
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

	public function register_post_types() {}
	public function add_rewrite_rules() {}
	public function add_custom_image_sizes() {}
	public function add_editor_styles() {}
	public function add_meta_to_rest() {}
	public function initialize_acf_fields() {}
	public function initialize_acf_options() {}
	public function allow_custom_upload_mimes() {}
} 