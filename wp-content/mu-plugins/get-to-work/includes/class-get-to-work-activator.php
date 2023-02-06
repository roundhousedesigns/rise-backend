<?php
/**
 * Fired during plugin activation
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 */
class Get_To_Work_Activator {

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
