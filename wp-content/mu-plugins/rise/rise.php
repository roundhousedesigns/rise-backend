<?php
/**
 * Plugin Name:       Rise Theatre Directory
 * Description:       The main site functionality for the Rise Theatre Directory backend.
 * Version:           1.1.1
 * Author:            Roundhouse Designs
 * Author URI:        https://roundhouse-designs.com
 * Text Domain:       rise
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'RISE_VERSION', '1.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rise-activator.php
 */
function activate_rise() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rise-activator.php';
	Rise_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rise-deactivator.php
 */
function deactivate_rise() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rise-deactivator.php';
	Rise_Deactivator::deactivate();
}

/**
 * Register activation and deactivation hooks.
 */
register_activation_hook( __FILE__, 'activate_rise' );
register_deactivation_hook( __FILE__, 'deactivate_rise' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rise.php';

/**
 * Functions.
 */
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

/**
 * Backwards compatibility.
 */
require plugin_dir_path( __FILE__ ) . 'includes/deprecated.php';

/**
 * Utilities and helpers.
 */
require plugin_dir_path( __FILE__ ) . 'includes/utils.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_rise() {
	$plugin = new Rise();
	$plugin->run();
}
run_rise();
