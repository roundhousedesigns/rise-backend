<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Rise
 * @subpackage Rise/public
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rise
 * @subpackage Rise/public
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 */
class Rise_Public {

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rise-public.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rise-public.js', ['jquery'], $this->version, false );
	}

	/**
	 * Filter password reset request email's body.
	 *
	 * @param  string $message
	 * @param  string $key
	 * @param  string $user_login
	 * @return string The email message to send.
	 */
	public function filter_retrieve_password_message( $message, $key, $user_login ) {
		$site_name       = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$reset_link_base = defined( 'RISE_FRONTEND_URL' ) ? esc_url( RISE_FRONTEND_URL ) : home_url();
		$reset_link      = esc_url( $reset_link_base ) . '/reset-password?key=' . $key . '&login=' . rawurlencode( $user_login );

		// Create new message
		$message = __( 'Someone has requested a password reset for the following account:' . $user_login, 'rise' ) . "\n";
		$message .= sprintf( __( 'Site Name: %s' ), $site_name ) . "\n";
		$message .= sprintf( __( 'Username: %s', 'rise' ), $user_login ) . "\n";
		$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'rise' ) . "\n";
		$message .= __( 'To reset your password, visit the following address:', 'rise' ) . "\n";
		$message .= $reset_link . "\n";

		return $message;
	}
}
