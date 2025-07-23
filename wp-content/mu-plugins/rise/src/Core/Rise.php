<?php

namespace RHD\Rise\Core;

use RHD\Rise\Core\Admin;
use RHD\Rise\Core\Cron;
use RHD\Rise\Core\Frontend;
use RHD\Rise\Core\I18n;
use RHD\Rise\Core\Init;
use RHD\Rise\Core\Shortcodes;
use RHD\Rise\Includes\GraphQLMutations;
use RHD\Rise\Includes\GraphQLQueries;
use RHD\Rise\Includes\GraphQLTypes;
use RHD\Rise\Includes\Search;
use RHD\Rise\Includes\Types;
use RHD\Rise\Includes\Users;
use RHD\Rise\Includes\WooCommerce;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
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
	 * @var Loader $loader Maintains and registers all hooks for the plugin.
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
		$this->define_search_hooks();
		$this->define_woocommerce_hooks();
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
	 * @access   private
	 * @since    0.1.0
	 */
	private function load_dependencies() {
		$this->loader = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access   private
	 * @since    0.1.0
	 *
	 * @return void
	 */
	private function set_locale() {
		$plugin_i18n = new I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all required external plugins.
	 *
	 * @return void
	 */
	private function define_init_hooks() {
		$plugin_data = new Init();

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
		$user_data = new Users();

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
		$plugin_data = new Types();

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
		 * Custom Post Type: job_post
		 */
		$this->loader->add_action( 'init', $plugin_data, 'job_post_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'job_post_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'job_post_bulk_updated_messages', 10, 2 );
		$this->loader->add_action( 'transition_post_status', $plugin_data, 'set_job_post_expiration_on_publication', 10, 3 );

		/**
		 * Custom Post Type: network_partner
		 */
		$this->loader->add_action( 'init', $plugin_data, 'network_partner_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'network_partner_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'network_partner_bulk_updated_messages', 10, 2 );

		/**
		 * Custom Post Type: profile_notification
		 */
		$this->loader->add_action( 'init', $plugin_data, 'profile_notification_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_data, 'profile_notification_updated_messages' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_data, 'profile_notification_bulk_updated_messages', 10, 2 );
		$this->loader->add_action( 'profile_update', $plugin_data, 'create_notification_for_profile_starred_by', 10, 1 );
		$this->loader->add_action( 'wp_insert_post', $plugin_data, 'delete_duplicate_profile_notifications', 10, 1 );

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
		$plugin_data_types = new GraphQLTypes();

		$this->loader->add_filter( 'graphql_is_valid_http_content_type', $plugin_data_types, 'is_valid_http_content_type', 10, 2 );
		$this->loader->add_action( 'graphql_register_types', $plugin_data_types, 'register_types' );
	}

	/**
	 * Register cron jobs.
	 *
	 * @return void
	 */
	private function define_cron_jobs() {
		$cron_data = new Cron();

		$this->loader->add_action( 'rise_delete_expired_conflict_ranges_cron', $cron_data, 'delete_expired_conflict_ranges' );
		$this->loader->add_action( 'rise_check_expired_job_posts_cron', $cron_data, 'check_expired_job_posts' );
	}

	/**
	 * Register custom GraphQL queries.
	 *
	 * @return void
	 */
	private function define_graphql_queries() {
		$plugin_data_queries = new GraphQLQueries();

		$this->loader->add_action( 'graphql_require_authentication_allowed_fields', $plugin_data_queries, 'require_authentication_allowed_fields', 10, 1 );
		$this->loader->add_action( 'graphql_register_types', $plugin_data_queries, 'register_queries' );
	}

	/**
	 * Define the WooCommerce hooks.
	 *
	 * @access   private
	 * @since    1.2
	 */
	private function define_woocommerce_hooks() {
		$woocommerce_data = new WooCommerce( $this->plugin_name, $this->version );

		$this->loader->add_action( 'woocommerce_store_api_checkout_order_processed', $woocommerce_data, 'add_job_post_data_to_order', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_status_completed', $woocommerce_data, 'create_job_post_from_order_after_payment_complete', 10, 1 );
	}

	/**
	 * Register custom GraphQL mutations.
	 *
	 * @return void
	 */
	private function define_graphql_mutations() {
		$plugin_gql_muts = new GraphQLMutations();

		$this->loader->add_action( 'graphql_register_types', $plugin_gql_muts, 'register_mutations' );

		// Register registerUser mutation extensions
		$this->loader->add_action( 'graphql_register_types', $plugin_gql_muts, 'add_fields_to_registerUser' );
		$this->loader->add_action( 'graphql_user_object_mutation_update_additional_data', $plugin_gql_muts, 'handle_registerUser_org_fields', 10, 5 );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_admin_hooks() {
		$plugin_data = new Admin( $this->plugin_name, $this->version );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_data, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_data, 'enqueue_scripts' );

		/**
		 * Login redirect
		 */
		$this->loader->add_filter( 'login_redirect', $plugin_data, 'redirect_crew_members_after_login', 10, 3 );

		/**
		 * TEC endpoints
		 */
		$this->loader->add_action( 'template_redirect', $plugin_data, 'block_non_network_partners_from_events_endpoints' );

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
		$plugin_public = new Frontend( $this->plugin_name, $this->version );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'retrieve_password_message', $plugin_public, 'filter_retrieve_password_message', 20, 3 );

		// Initialize shortcodes
		new Shortcodes();
	}

	/**
	 * Register all of the hooks related to the search functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_search_hooks() {
		$plugin_search = new Search();

		$this->loader->add_filter( 'pre_get_posts', $plugin_search, 'filter_job_posts_query' );
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
