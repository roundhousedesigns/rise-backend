<?php
/**
 * Plugin Name: Bulk Taxonomy Term Editor
 * Description: Bulk edit taxonomy terms.
 * Version: 1.0.0
 * Author: Nick Gaswirth
 * Author URI: https://roundhouse-designs.com
 *
 * @package Bulk_Taxonomy_Term_Editor
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the main class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-bulk-taxonomy-term-editor.php';

/**
 * Main instance of Bulk_Taxonomy_Term_Editor.
 *
 * Returns the main instance of Bulk_Taxonomy_Term_Editor to prevent the need to use globals.
 *
 * @since  1.0.0
 *
 * @return object Bulk_Taxonomy_Term_Editor
 */
function bulk_taxonomy_term_editor() {
	return Bulk_Taxonomy_Term_Editor::instance();
}

// Run the plugin.
bulk_taxonomy_term_editor();

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bulk-taxonomy-term-editor-activator.php
 */
function activate_bulk_taxonomy_term_editor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bulk-taxonomy-term-editor-activator.php';
	Bulk_Taxonomy_Term_Editor_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bulk-taxonomy-term-editor-deactivator.php
 */
function deactivate_bulk_taxonomy_term_editor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bulk-taxonomy-term-editor-deactivator.php';
	Bulk_Taxonomy_Term_Editor_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bulk_taxonomy_term_editor' );
register_deactivation_hook( __FILE__, 'deactivate_bulk_taxonomy_term_editor' );
