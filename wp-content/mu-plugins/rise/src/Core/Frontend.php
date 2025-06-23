<?php

namespace RHD\Rise\Core;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Frontend {

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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rise-public.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rise-public.js', ['jquery'], $this->version, false );
	}
}
