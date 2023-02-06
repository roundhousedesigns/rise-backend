<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 */
class Get_To_Work_Deactivator {

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
