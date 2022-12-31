<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 */
// phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid
class Get_To_Work_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'gtw',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
