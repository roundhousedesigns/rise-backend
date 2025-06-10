<?php

namespace RHD\Rise\Core;

/**
 * Fired during plugin activation
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1.0
	 */
	public static function activate() {
		flush_rewrite_rules();
	}

} 