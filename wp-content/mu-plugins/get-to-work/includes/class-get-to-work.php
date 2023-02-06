<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
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
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Get_To_Work {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @access   protected
	 * @var Get_To_Work_Loader $loader Maintains and registers all hooks for the plugin.
	 * @since    0.1.0
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @access   protected
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 * @since    0.1.0
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @access   protected
	 * @var string $version The current version of the plugin.
	 * @since    0.1.0
	 */
	protected $version;

	/**
	 * The allowed origins for CORS.
	 *
	 * @access   public
	 * @var string
	 * @since 0.1.0
	 */
	public $allowed_origins = [
		'http://localhost:3000',
		'https://gtw-frontend.pages.dev',
		'https://dev.gtw-frontend.pages.dev',
	];

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		if ( defined( 'GET_TO_WORK_VERSION' ) ) {
			$this->version = GET_TO_WORK_VERSION;
		} else {
			$this->version = '0.1.0';
		}
		$this->plugin_name = 'get-to-work';

		// Fire away.
		$this->load_dependencies();
		$this->set_locale();
		$this->define_init_hooks();
		$this->define_data_hooks();
		$this->define_graphql_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Get_To_Work_Loader. Orchestrates the hooks of the plugin.
	 * - Get_To_Work_i18n. Defines internationalization functionality.
	 * - Get_To_Work_Admin. Defines all hooks for the admin area.
	 * - Get_To_Work_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-get-to-work-loader.php';

		/**
		 * The class responsible for plugin intialization.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-get-to-work-init.php';

		/**
		 * The class responsible for registering data types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-get-to-work-data.php';

		/**
		 * The class responsible for registering GrapQL queries and mutations.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-get-to-work-graphql-queries.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-get-to-work-graphql-mutations.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-get-to-work-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-get-to-work-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-get-to-work-public.php';

		$this->loader = new Get_To_Work_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Get_To_Work_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function set_locale() {
		$plugin_i18n = new Get_To_Work_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all required external plugins.
	 *
	 * @return void
	 */
	private function define_init_hooks() {
		$plugin_data = new Get_To_Work_Init();

		/**
		 * TGMPA class.
		 */
		require dirname( __DIR__ ) . '/lib/tgmpa/class-tgm-plugin-activation.php';

		$this->loader->add_action( 'tgmpa_register', $plugin_data, 'register_required_plugins' );
	}

	/**
	 * Register all of the main plugin hooks.
	 *
	 * @access private
	 * @since 0.1.0
	 */
	private function define_data_hooks() {
		$plugin_data = new Get_To_Work_Data();

		/**
		 * User roles.
		 */
		$this->loader->add_action( 'admin_init', $plugin_data, 'add_roles' );

		/**
		 * Custom Post Type: credit
		 */
		$this->loader->add_action( 'init', $plugin_data, 'credit_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'credit_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'credit_bulk_updated_messages', 10, 2 );
		$this->loader->add_action( 'init', $plugin_data, 'blockusers_init' );

		/**
		 * Taxonomy: position (`credit`)
		 */
		$this->loader->add_action( 'init', $plugin_data, 'position_init' );
		$this->loader->add_filter( 'term_updated_messages', $plugin_data, 'position_updated_messages' );

		/**
		 * Custom Post Type: saved_search
		 */
		$this->loader->add_action( 'init', $plugin_data, 'saved_search_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'saved_search_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'saved_search_bulk_updated_messages', 10, 2 );
	}

	/**
	 * Register all of the hooks related to GraphQL.
	 *
	 * @return void
	 */
	private function define_graphql_hooks() {
		$plugin_data_queries   = new Get_To_Work_GraphQL_Queries();
		$plugin_data_mutations = new Get_To_Work_GraphQL_Mutations( $this->allowed_origins );

		$this->loader->add_action( 'graphql_register_types', $plugin_data_queries, 'register_types' );
		$this->loader->add_filter( 'graphql_register_types', $plugin_data_mutations, 'register_mutations' );
		$this->loader->add_filter( 'graphql_response_headers_to_send', $plugin_data_mutations, 'response_headers_to_send' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_admin_hooks() {
		$plugin_data = new Get_To_Work_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_data, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_data, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_public_hooks() {
		$plugin_data = new Get_To_Work_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_data, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_data, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 *
	 * @return Get_To_Work_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
