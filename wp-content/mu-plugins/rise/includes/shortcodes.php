<?php
/**
 * Shortcodes.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.2.0
 */

// Only register shortcodes if WordPress is loaded
if ( function_exists( 'add_shortcode' ) ) {
	/**
	 * Add a shortcode to display the current year with a copyright symbol.
	 *
	 * @since 1.2.0
	 *
	 * @param  array  $atts The attributes of the shortcode.
	 * @return string The HTML of the year.
	 */
	function rise_shortcode_year() {
		return sprintf(
			esc_html__( '© %d', 'rise' ),
			date( 'Y' )
		);
	}
	add_shortcode( 'rise_copyright_year', 'rise_shortcode_year' );
}
