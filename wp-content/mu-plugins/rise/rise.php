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
define( 'RISE_VERSION', '1.2-autoloader' );

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

	// Add filters for job post admin views
	add_filter( 'views_edit-job_post', 'rise_add_admin_job_posts_views' );
	add_filter( 'pre_get_posts', 'rise_filter_job_posts_query' );
}
run_rise();

/**
 * Add "Expired" view to job posts admin screen
 *
 * @param  array $views   The array of views
 * @return array Modified views array
 */
function rise_add_admin_job_posts_views( $views ) {
	// Get count of expired posts including private posts
	$args = [
		'post_type'   => 'job_post',
		'limit'       => -1,
		'post_status' => 'any',
	];

	$expired_args = [
		'meta_query' => [
			[
				'key'     => 'expired',
				'value'   => '1',
				'compare' => '=',
			],
		],
	];

	$active_args = [
		'meta_query' => [
			'relation' => 'OR',
			[
				'key'     => 'expired',
				'value'   => '0',
				'compare' => '=',
			],
			[
				'key'     => 'expired',
				'compare' => 'NOT EXISTS',
			],
		],
	];

	$pending_args = [
		'post_status' => 'pending',
		'meta_query'  => [
			'relation' => 'OR',
			[
				'key'     => 'expired',
				'value'   => '0',
				'compare' => '=',
			],
		],
		[
			'key'     => 'expired',
			'compare' => 'NOT EXISTS',
		],
	];

	$expired_count = count( get_posts( array_merge( $args, $expired_args ) ) );
	$active_count  = count( get_posts( array_merge( $args, $active_args ) ) );

	// Modify 'publish' view to exclude Expired
	$views['publish'] = sprintf(
		'<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
		admin_url( 'edit.php?post_type=job_post&expired=0' ),
		isset( $_GET['expired'] ) ? '' : 'current',
		__( 'Published', 'rise' ),
		$active_count
	);

	// Add 'expired' view
	$views['expired'] = sprintf(
		'<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
		admin_url( 'edit.php?post_type=job_post&expired=1' ),
		isset( $_GET['expired'] ) ? 'current' : '',
		__( 'Expired', 'rise' ),
		$expired_count
	);

	// Reorder views to put Active and Expired after All
	if ( isset( $views['all'] ) ) {
		$all_view     = $views['all'];
		$publish_view = $views['publish'];
		$expired_view = $views['expired'];

		// Remove the views we want to reorder
		unset( $views['all'], $views['publish'], $views['expired'] );

		// Create new array with desired order
		$views = array_merge(
			['all' => $all_view],
			['publish' => $publish_view],
			['expired' => $expired_view],
			$views
		);
	}

	return $views;
}

/**
 * Filter job posts query based on expired status
 *
 * @param  WP_Query $query   The WP_Query instance
 * @return WP_Query Modified query
 */
function rise_filter_job_posts_query( $query ) {
	// Only modify queries in admin and for job posts
	if ( !is_admin() || !$query->is_main_query() || $query->get( 'post_type' ) !== 'job_post' ) {
		return $query;
	}

	// If expired view is selected
	if ( isset( $_GET['expired'] ) ) {
		$query->set( 'meta_query', [
			[
				'key'     => 'expired',
				'value'   => '1',
				'compare' => '=',
			],
		] );
	} else {
		// Exclude expired posts from "All" view
		$query->set( 'meta_query', [
			'relation' => 'OR',
			[
				'key'     => 'expired',
				'value'   => '0',
				'compare' => '=',
			],
			[
				'key'     => 'expired',
				'compare' => 'NOT EXISTS',
			],
		] );
	}

	return $query;
}
