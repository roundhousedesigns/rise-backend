<?php
/**
 * Plugin Name:       Get To Work
 * Plugin URI:        https://gtw.roundhouse-designs.com
 * Description:       The main site functionality for the Get To Work backend.
 * Version:           0.2.0
 * Author:            Roundhouse Designs
 * Author URI:        https://roundhouse-designs.com
 * Text Domain:       gtw
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'GET_TO_WORK_VERSION', '0.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-get-to-work-activator.php
 */
function activate_get_to_work() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-get-to-work-activator.php';
	Get_To_Work_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-get-to-work-deactivator.php
 */
function deactivate_get_to_work() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-get-to-work-deactivator.php';
	Get_To_Work_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_get_to_work' );
register_deactivation_hook( __FILE__, 'deactivate_get_to_work' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-get-to-work.php';

/**
 * Helper functions.
 */
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_get_to_work() {
	$plugin = new Get_To_Work();
	$plugin->run();
}
run_get_to_work();
