<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    Bulk_Taxonomy_Term_Editor
 * @subpackage Bulk_Taxonomy_Term_Editor/includes
 *
 * @since      1.0.0
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Bulk_Taxonomy_Term_Editor
 * @subpackage Bulk_Taxonomy_Term_Editor/includes
 *
 * @author     Nick roundhouse-designs *
 *
 * @since      1.0.0
 */
class Bulk_Taxonomy_Term_Editor {

	// The single instance of the class.
	protected static $_instance = null;

	// The plugin version.
	public $version = '1.0.0';

	// The plugin name.
	public $plugin_name = 'bulk-taxonomy-term-editor';

	/**
	 * Main Bulk_Taxonomy_Term_Editor Instance.
	 *
	 * Ensures only one instance of Bulk_Taxonomy_Term_Editor is loaded or can be loaded.
	 *
	 * @static
	 * @see bulk_taxonomy_term_editor()
	 * @since 1.0.0
	 *
	 * @return Bulk_Taxonomy_Term_Editor - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Bulk_Taxonomy_Term_Editor Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 * @since  1.0.0
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, ['Bulk_Taxonomy_Term_Editor_Activator', 'activate'] );
		register_deactivation_hook( __FILE__, ['Bulk_Taxonomy_Term_Editor_Deactivator', 'deactivate'] );
	}

	/**
	 * Define Bulk_Taxonomy_Term_Editor Constants.
	 */
	private function define_constants() {

		// Plugin version.
		if ( ! defined( 'BULK_TAXONOMY_TERM_EDITOR_VERSION' ) ) {
			define( 'BULK_TAXONOMY_TERM_EDITOR_VERSION', $this->version );
		}

		// Plugin Folder Path.
		if ( ! defined( 'BULK_TAXONOMY_TERM_EDITOR_PLUGIN_DIR' ) ) {
			define( 'BULK_TAXONOMY_TERM_EDITOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'BULK_TAXONOMY_TERM_EDITOR_PLUGIN_URL' ) ) {
			define( 'BULK_TAXONOMY_TERM_EDITOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'BULK_TAXONOMY_TERM_EDITOR_PLUGIN_FILE' ) ) {
			define( 'BULK_TAXONOMY_TERM_EDITOR_PLUGIN_FILE', __FILE__ );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		include_once BULK_TAXONOMY_TERM_EDITOR_PLUGIN_DIR . 'includes/class-bulk-taxonomy-term-editor-activator.php';
		include_once BULK_TAXONOMY_TERM_EDITOR_PLUGIN_DIR . 'includes/class-bulk-taxonomy-term-editor-deactivator.php';
	}

}
