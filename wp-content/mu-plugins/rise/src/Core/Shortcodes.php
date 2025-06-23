<?php
/**
 * Shortcodes functionality.
 *
 * @package    Rise
 * @subpackage Rise/Core
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.2.0
 */

namespace RHD\Rise\Core;

/**
 * Shortcodes class.
 *
 * @since 1.2.0
 */
class Shortcodes {
	/**
	 * Initialize the class.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.2.0
	 */
	private function init_hooks() {
		add_shortcode( 'rise_copyright_year', [$this, 'copyright_year'] );
	}

	/**
	 * Display the current year with a copyright symbol.
	 *
	 * @since 1.2.0
	 *
	 * @return string The HTML of the year.
	 */
	public function copyright_year() {
		return sprintf(
			esc_html__( '© %d', 'rise' ),
			date( 'Y' )
		);
	}
}
