<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    Rise
 * @subpackage Rise/includes
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
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Rise {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @access   protected
	 * @var Rise_Loader $loader Maintains and registers all hooks for the plugin.
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {

		if ( defined( 'RISE_VERSION' ) ) {
			$this->version = RISE_VERSION;
		}

		$this->plugin_name = 'rise';

		// Fire away.
		$this->load_dependencies();
		$this->set_locale();
		$this->define_init_hooks();
		$this->define_user_hooks();
		$this->define_post_type_hooks();
		$this->define_cron_jobs();
		$this->define_graphql_types();
		$this->define_graphql_queries();
		$this->define_graphql_mutations();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Magic getter for our object.
	 *
	 * @param  string $property
	 * @return void
	 */
	public function __get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->property;
		}
	}

	/**
	 * Magic setter for our object.
	 *
	 * @param  string $property
	 * @param  mixed  $value
	 * @return void
	 */
	public function __set( $property, $value ) {
		if ( property_exists( $this, $property ) ) {
			$this->property = $value;
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rise_Loader. Orchestrates the hooks of the plugin.
	 * - Rise_i18n. Defines internationalization functionality.
	 * - Rise_Admin. Defines all hooks for the admin area.
	 * - Rise_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	// TODO set up autoloader
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-loader.php';

		/**
		 * The class responsible for plugin intialization.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-init.php';

		/**
		 * The class responsible for creating taxonomies.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-taxonomies.php';

		/**
		 * The classes responsible for registering data types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-userprofile.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-credit.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-types.php';

		/**
		 * The class responsible for registering user data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-users.php';

		/**
		 * The classes responsible for registering GraphQL types, queries, and mutations.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-graphql-types.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-graphql-queries.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-graphql-mutations.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rise-admin.php';

		/**
		 * The class responsible for defining cron jobs.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-cron.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rise-public.php';

		$this->loader = new Rise_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rise_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access   private
	 * @since    0.1.0
	 *
	 * @return void
	 */
	private function set_locale() {
		$plugin_i18n = new Rise_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all required external plugins.
	 *
	 * @return void
	 */
	private function define_init_hooks() {
		$plugin_data = new Rise_Init();

		/**
		 * TGMPA class.
		 */
		require dirname( __DIR__ ) . '/lib/tgmpa/class-tgm-plugin-activation.php';
		$this->loader->add_action( 'tgmpa_register', $plugin_data, 'register_required_plugins' );

		/**
		 * Safe redirects.
		 */
		$this->loader->add_action( 'allowed_redirect_hosts', $plugin_data, 'allowed_redirect_hosts' );
	}

	/**
	 * Register all of the custom user data hooks.
	 *
	 * @access private
	 * @since 0.1.0
	 */
	private function define_user_hooks() {
		$user_data = new Rise_Users();

		/**
		 * User roles.
		 */
		$this->loader->add_action( 'admin_init', $user_data, 'add_roles' );
		$this->loader->add_action( 'current_screen', $user_data, 'redirect_crew_members_from_dashboard' );
		$this->loader->add_action( 'after_setup_theme', $user_data, 'remove_admin_bar_for_crew_members' );
		$this->loader->add_action( 'after_setup_theme', $user_data, 'add_image_sizes' );

		/**
		 * Custom taxonomy: gender_identity (`user`)
		 */
		$this->loader->add_action( 'init', $user_data, 'gender_identity_init' );
		$this->loader->add_action( 'admin_menu', $user_data, 'add_gender_identity_to_user_menu' );
		$this->loader->add_action( 'show_user_profile', $user_data, 'add_gender_identity_to_user_profile' );
		$this->loader->add_action( 'edit_user_profile', $user_data, 'add_gender_identity_to_user_profile' );
		$this->loader->add_action( 'personal_options_update', $user_data, 'save_gender_identity_on_user_profile' );
		$this->loader->add_action( 'edit_user_profile_update', $user_data, 'save_gender_identity_on_user_profile' );

		/**
		 * Custom taxonomy: personal_identity (`user`)
		 */
		$this->loader->add_action( 'init', $user_data, 'personal_identity_init' );
		$this->loader->add_action( 'admin_menu', $user_data, 'add_personal_identity_to_user_menu' );
		$this->loader->add_action( 'show_user_profile', $user_data, 'add_personal_identity_to_user_profile' );
		$this->loader->add_action( 'edit_user_profile', $user_data, 'add_personal_identity_to_user_profile' );
		$this->loader->add_action( 'personal_options_update', $user_data, 'save_personal_identity_on_user_profile' );
		$this->loader->add_action( 'edit_user_profile_update', $user_data, 'save_personal_identity_on_user_profile' );

		/**
		 * Custom taxonomy: racial_identity (`user`)
		 */
		$this->loader->add_action( 'init', $user_data, 'racial_identity_init' );
		$this->loader->add_action( 'admin_menu', $user_data, 'add_racial_identity_to_user_menu' );
		$this->loader->add_action( 'show_user_profile', $user_data, 'add_racial_identity_to_user_profile' );
		$this->loader->add_action( 'edit_user_profile', $user_data, 'add_racial_identity_to_user_profile' );
		$this->loader->add_action( 'personal_options_update', $user_data, 'save_racial_identity_on_user_profile' );
		$this->loader->add_action( 'edit_user_profile_update', $user_data, 'save_racial_identity_on_user_profile' );

		/**
		 * Custom taxonomy: union (`user`)
		 */
		$this->loader->add_action( 'init', $user_data, 'union_init' );
		$this->loader->add_action( 'admin_menu', $user_data, 'add_union_to_user_menu' );
		$this->loader->add_action( 'show_user_profile', $user_data, 'add_union_to_user_profile' );
		$this->loader->add_action( 'edit_user_profile', $user_data, 'add_union_to_user_profile' );
		$this->loader->add_action( 'personal_options_update', $user_data, 'save_union_on_user_profile' );
		$this->loader->add_action( 'edit_user_profile_update', $user_data, 'save_union_on_user_profile' );

		/**
		 * Custom taxonomy: location (`user`)
		 */
		$this->loader->add_action( 'init', $user_data, 'location_init' );
		$this->loader->add_action( 'admin_menu', $user_data, 'add_location_to_user_menu' );
		$this->loader->add_action( 'show_user_profile', $user_data, 'add_location_to_user_profile' );
		$this->loader->add_action( 'edit_user_profile', $user_data, 'add_location_to_user_profile' );
		$this->loader->add_action( 'personal_options_update', $user_data, 'save_location_on_user_profile' );
		$this->loader->add_action( 'edit_user_profile_update', $user_data, 'save_location_on_user_profile' );

		/**
		 * Custom taxonomy: experience_level (`user`)
		 */
		$this->loader->add_action( 'init', $user_data, 'experience_level_init' );
		$this->loader->add_action( 'admin_menu', $user_data, 'add_experience_level_to_user_menu' );
		$this->loader->add_action( 'show_user_profile', $user_data, 'add_experience_level_to_user_profile' );
		$this->loader->add_action( 'edit_user_profile', $user_data, 'add_experience_level_to_user_profile' );
		$this->loader->add_action( 'personal_options_update', $user_data, 'save_experience_level_on_user_profile' );
		$this->loader->add_action( 'edit_user_profile_update', $user_data, 'save_experience_level_on_user_profile' );

		/**
		 * Custom taxonomy: partner_directory (`user`)
		 */
		$this->loader->add_action( 'init', $user_data, 'partner_directory_init' );
		$this->loader->add_action( 'admin_menu', $user_data, 'add_partner_directory_to_user_menu' );
		$this->loader->add_action( 'show_user_profile', $user_data, 'add_partner_directory_to_user_profile' );
		$this->loader->add_action( 'edit_user_profile', $user_data, 'add_partner_directory_to_user_profile' );
		$this->loader->add_action( 'personal_options_update', $user_data, 'save_partner_directory_on_user_profile' );
		$this->loader->add_action( 'edit_user_profile_update', $user_data, 'save_partner_directory_on_user_profile' );
	}

	/**
	 * Register all of the custom data hooks.
	 *
	 * @access private
	 * @since 0.1.0
	 */
	private function define_post_type_hooks() {
		$plugin_data = new Rise_Types();

		/**
		 * Custom Post Type: credit
		 */
		$this->loader->add_action( 'init', $plugin_data, 'credit_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'credit_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'credit_bulk_updated_messages', 10, 2 );
		$this->loader->add_filter( 'manage_users_columns', $plugin_data, 'add_credit_posts_column' );
		$this->loader->add_filter( 'manage_users_custom_column', $plugin_data, 'display_credit_posts_column', 10, 3 );
		$this->loader->add_filter( 'manage_users_sortable_columns', $plugin_data, 'make_credit_posts_column_sortable' );

		/**
		 * Taxonomy: position (`credit`)
		 */
		$this->loader->add_action( 'init', $plugin_data, 'position_init' );
		$this->loader->add_filter( 'term_updated_messages', $plugin_data, 'position_updated_messages' );

		/**
		 * Taxonomy: skill (`credit`)
		 */
		$this->loader->add_action( 'init', $plugin_data, 'skill_init' );
		$this->loader->add_filter( 'term_updated_messages', $plugin_data, 'skill_updated_messages' );

		/**
		 * Custom Post Type: user_notice
		 */
		$this->loader->add_action( 'init', $plugin_data, 'user_notice_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'user_notice_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'user_notice_bulk_updated_messages', 10, 2 );

		/**
		 * Custom Post Type: saved_search
		 */
		$this->loader->add_action( 'init', $plugin_data, 'saved_search_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'saved_search_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'saved_search_bulk_updated_messages', 10, 2 );
		$this->loader->add_filter( 'use_block_editor_for_post_type', $plugin_data, 'saved_search_disable_block_editor', 10, 2 );
		$this->loader->add_filter( 'user_can_richedit', $plugin_data, 'saved_search_remove_visual_editor' );

		/**
		 * Custom Post Type: conflict_range
		 */
		$this->loader->add_action( 'init', $plugin_data, 'conflict_range_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'conflict_range_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'conflict_range_bulk_updated_messages', 10, 2 );
		$this->loader->add_filter( 'use_block_editor_for_post_type', $plugin_data, 'conflict_range_disable_block_editor', 10, 2 );
		$this->loader->add_filter( 'user_can_richedit', $plugin_data, 'conflict_range_remove_visual_editor' );

		/**
		 * Custom Post Type: job
		 */
		$this->loader->add_action( 'init', $plugin_data, 'job_post_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'job_post_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'job_post_bulk_updated_messages', 10, 2 );

		/**
		 * Custom Post Type: network_partner
		 */
		$this->loader->add_action( 'init', $plugin_data, 'network_partner_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'network_partner_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'network_partner_bulk_updated_messages', 10, 2 );

		/**
		 * Taxonomy: network_partner_tag (`network_partner`)
		 */
		$this->loader->add_action( 'init', $plugin_data, 'network_partner_tag_init' );
		$this->loader->add_filter( 'term_updated_messages', $plugin_data, 'network_partner_tag_updated_messages' );
	}

	/**
	 * Register GraphQL object types, connections, interfaces, etc.
	 */
	private function define_graphql_types() {
		$plugin_data_types = new Rise_GraphQL_Types();

		$this->loader->add_filter( 'graphql_is_valid_http_content_type', $plugin_data_types, 'is_valid_http_content_type', 10, 2 );
		$this->loader->add_action( 'graphql_register_types', $plugin_data_types, 'register_types' );
	}

	/**
	 * Register cron jobs.
	 *
	 * @return void
	 */
	private function define_cron_jobs() {
		$cron_data = new Rise_Cron();

		$this->loader->add_action( 'rise_delete_expired_conflict_ranges_cron', $cron_data, 'delete_expired_conflict_ranges' );
	}

	/**
	 * Register custom GraphQL queries.
	 *
	 * @return void
	 */
	private function define_graphql_queries() {
		$plugin_data_queries = new Rise_GraphQL_Queries();

		$this->loader->add_action( 'graphql_require_authentication_allowed_fields', $plugin_data_queries, 'require_authentication_allowed_fields', 10, 1 );
		$this->loader->add_action( 'graphql_register_types', $plugin_data_queries, 'register_queries' );

		// TODO `remove_graphql_extensions_response_data` doesn't seem to be working. `extensions` still in responses. THIS IS ALSO BREAKING USER REG AND PASSWORD RESET.
		// $this->loader->add_action( 'graphql_request_results', $plugin_data_queries, 'remove_graphql_extensions_response_data', 10, 1 );
	}

	/**
	 * Register custom GraphQL mutations.
	 *
	 * @return void
	 */
	private function define_graphql_mutations() {
		$plugin_gql_muts = new Rise_GraphQL_Mutations();

		$this->loader->add_filter( 'graphql_register_types', $plugin_gql_muts, 'register_mutations' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_admin_hooks() {
		$plugin_data = new Rise_Admin( $this->plugin_name, $this->version );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_data, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_data, 'enqueue_scripts' );

		/**
		 * Menus
		 */
		$this->loader->add_action( 'admin_init', $plugin_data, 'plugin_settings_init' );
		$this->loader->add_action( 'admin_menu', $plugin_data, 'plugin_options_page' );
		$this->loader->add_action( 'admin_menu', $plugin_data, 'remove_menu_pages' );

		/**
		 * Dashboard widget
		 */
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_data, 'register_rise_basic_stats_widget' );
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_data, 'remove_dashboard_widgets' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_public_hooks() {
		$plugin_data = new Rise_Public( $this->plugin_name, $this->version );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_data, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_data, 'enqueue_scripts' );
		$this->loader->add_filter( 'retrieve_password_message', $plugin_data, 'filter_retrieve_password_message', 20, 3 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}
}
