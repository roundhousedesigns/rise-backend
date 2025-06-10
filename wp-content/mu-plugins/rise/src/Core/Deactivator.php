<?php

namespace RHD\Rise\Core;

/**
 * Fired during plugin deactivation
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

} 