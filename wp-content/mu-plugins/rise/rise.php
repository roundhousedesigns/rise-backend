<?php
/**
 * Plugin Name:       Rise Theatre Directory
 * Description:       The main site functionality for the Rise Theatre Directory backend.
 * Version:           1.2-network-partners
 * Author:            Roundhouse Designs
 * Author URI:        https://roundhouse-designs.com
 * Text Domain:       rise
 * Domain Path:       /languages
 *
 * Copyright (c) 2024 Maestra Music and Roundhouse Designs. All rights reserved.
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}
/**
 * Current plugin version.
 */
define( 'RISE_VERSION', '1.2' );

/**
 * Define the plugin directory.
 */
define( 'RISE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Load Composer autoloader with error handling.
 */
function rise_load_autoloader() {
	$autoloader_path = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

	require_once $autoloader_path;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in src/Core/Activator.php
 */
function activate_rise() {
	rise_load_autoloader();
	\RHD\Rise\Core\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in src/Core/Deactivator.php
 */
function deactivate_rise() {
	rise_load_autoloader();
	\RHD\Rise\Core\Deactivator::deactivate();
}

/**
 * Register activation and deactivation hooks.
 */
register_activation_hook( __FILE__, 'activate_rise' );
register_deactivation_hook( __FILE__, 'deactivate_rise' );

/**
 * Load Composer autoloader for classes and standalone files.
 */
rise_load_autoloader();

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
	$plugin = new \RHD\Rise\Core\Rise();
	$plugin->run();
}
run_rise();
