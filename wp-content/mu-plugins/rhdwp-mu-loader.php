<?php
/**
 * Plugin Name: RHDWP MU Loader
 * Author: Roundhouse Designs
 * Author URI: https://roundhouse-designs.com
 * Description: Loads plugins in directories from the `mu-plugins` folder.
 * Version: 1.0.0
 *
 * @package RHD
 * @version 1.0.0
 */

$wpmu_plugin_dir = opendir( WPMU_PLUGIN_DIR );

// List all the entries in this directory.
// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
while ( false !== ( $entry = readdir( $wpmu_plugin_dir ) ) ) {
	$plugin_path = WPMU_PLUGIN_DIR . '/' . $entry;

	// Load the plugin if this is a subdirectory.
	if ( '.' !== $entry && '..' !== $entry && is_dir( $plugin_path ) && substr( $entry, 0, 1 ) !== '.' ) {
		require $plugin_path . '/' . $entry . '.php';
	}
}

closedir( $wpmu_plugin_dir );
