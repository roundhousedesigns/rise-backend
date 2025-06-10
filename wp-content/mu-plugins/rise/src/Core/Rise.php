<?php

namespace RHD\Rise\Core;

use RHD\Rise\Core\Loader;
use RHD\Rise\Core\I18n;
use RHD\Rise\Core\Activator;
use RHD\Rise\Core\Deactivator;
use RHD\Rise\Includes\Init;
use RHD\Rise\Includes\Taxonomies;
use RHD\Rise\Includes\UserProfile;
use RHD\Rise\Includes\Credit;
use RHD\Rise\Includes\JobPost;
use RHD\Rise\Includes\ProfileNotification;
use RHD\Rise\Includes\Types;
use RHD\Rise\Includes\Users;
use RHD\Rise\Includes\GraphQLTypes;
use RHD\Rise\Includes\GraphQLQueries;
use RHD\Rise\Includes\GraphQLMutations;
use RHD\Rise\Includes\Cron;
use RHD\Rise\Includes\WooCommerce;
use RHD\Rise\Admin\Admin;
use RHD\Rise\Public\PublicFacing;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
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
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
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
	 */
	private function set_locale() {
		$plugin_i18n = new I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to plugin initialization
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_init_hooks() {
		$plugin_init = new Init( $this->get_plugin_name(), $this->get_version() );
		$plugin_taxonomies = new Taxonomies( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_init, 'register_post_types' );
		$this->loader->add_action( 'init', $plugin_init, 'add_rewrite_rules' );
		$this->loader->add_action( 'init', $plugin_init, 'add_custom_image_sizes' );
		$this->loader->add_action( 'init', $plugin_init, 'add_editor_styles' );
		$this->loader->add_action( 'init', $plugin_taxonomies, 'add_role_taxonomy' );

		$this->loader->add_action( 'rest_api_init', $plugin_init, 'add_meta_to_rest' );
		$this->loader->add_action( 'acf/init', $plugin_init, 'initialize_acf_fields' );
		$this->loader->add_action( 'acf/init', $plugin_init, 'initialize_acf_options' );

		$this->loader->add_filter( 'upload_mimes', $plugin_init, 'allow_custom_upload_mimes' );
	}

	/**
	 * Register all of the hooks related to user functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_user_hooks() {
		$plugin_users = new Users( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_users, 'create_roles' );
		$this->loader->add_action( 'init', $plugin_users, 'add_role_caps' );

		$this->loader->add_action( 'user_register', $plugin_users, 'user_register' );
		$this->loader->add_action( 'profile_update', $plugin_users, 'profile_update' );
		$this->loader->add_action( 'delete_user', $plugin_users, 'user_delete' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_filtered_users', $plugin_users, 'get_filtered_users' );
		$this->loader->add_action( 'wp_ajax_get_filtered_users', $plugin_users, 'get_filtered_users' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_user_profile_for_directory', $plugin_users, 'get_user_profile_for_directory' );
		$this->loader->add_action( 'wp_ajax_get_user_profile_for_directory', $plugin_users, 'get_user_profile_for_directory' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_user_profile_for_editing', $plugin_users, 'get_user_profile_for_editing' );
		$this->loader->add_action( 'wp_ajax_get_user_profile_for_editing', $plugin_users, 'get_user_profile_for_editing' );

		$this->loader->add_action( 'wp_ajax_nopriv_update_user_profile', $plugin_users, 'update_user_profile' );
		$this->loader->add_action( 'wp_ajax_update_user_profile', $plugin_users, 'update_user_profile' );

		$this->loader->add_action( 'wp_ajax_nopriv_register_user', $plugin_users, 'register_user' );
		$this->loader->add_action( 'wp_ajax_register_user', $plugin_users, 'register_user' );

		$this->loader->add_action( 'wp_ajax_nopriv_user_login', $plugin_users, 'user_login' );
		$this->loader->add_action( 'wp_ajax_user_login', $plugin_users, 'user_login' );

		$this->loader->add_action( 'wp_ajax_nopriv_reset_password', $plugin_users, 'reset_password' );
		$this->loader->add_action( 'wp_ajax_reset_password', $plugin_users, 'reset_password' );

		$this->loader->add_action( 'wp_ajax_nopriv_set_new_password', $plugin_users, 'set_new_password' );
		$this->loader->add_action( 'wp_ajax_set_new_password', $plugin_users, 'set_new_password' );

		$this->loader->add_action( 'wp_ajax_nopriv_upload_user_files', $plugin_users, 'upload_user_files' );
		$this->loader->add_action( 'wp_ajax_upload_user_files', $plugin_users, 'upload_user_files' );

		$this->loader->add_action( 'wp_ajax_nopriv_delete_user_files', $plugin_users, 'delete_user_files' );
		$this->loader->add_action( 'wp_ajax_delete_user_files', $plugin_users, 'delete_user_files' );

		$this->loader->add_action( 'wp_ajax_nopriv_update_user_subscription_status', $plugin_users, 'update_user_subscription_status' );
		$this->loader->add_action( 'wp_ajax_update_user_subscription_status', $plugin_users, 'update_user_subscription_status' );

		$this->loader->add_action( 'wp_ajax_nopriv_create_profile_notification', $plugin_users, 'create_profile_notification' );
		$this->loader->add_action( 'wp_ajax_create_profile_notification', $plugin_users, 'create_profile_notification' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_profile_notifications', $plugin_users, 'get_profile_notifications' );
		$this->loader->add_action( 'wp_ajax_get_profile_notifications', $plugin_users, 'get_profile_notifications' );

		$this->loader->add_action( 'wp_ajax_nopriv_mark_profile_notification_read', $plugin_users, 'mark_profile_notification_read' );
		$this->loader->add_action( 'wp_ajax_mark_profile_notification_read', $plugin_users, 'mark_profile_notification_read' );

		$this->loader->add_action( 'wp_ajax_nopriv_delete_profile_notification', $plugin_users, 'delete_profile_notification' );
		$this->loader->add_action( 'wp_ajax_delete_profile_notification', $plugin_users, 'delete_profile_notification' );

		$this->loader->add_action( 'wp_ajax_nopriv_toggle_credit_favorite', $plugin_users, 'toggle_credit_favorite' );
		$this->loader->add_action( 'wp_ajax_toggle_credit_favorite', $plugin_users, 'toggle_credit_favorite' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_user_favorite_credits', $plugin_users, 'get_user_favorite_credits' );
		$this->loader->add_action( 'wp_ajax_get_user_favorite_credits', $plugin_users, 'get_user_favorite_credits' );

		$this->loader->add_action( 'wp_ajax_nopriv_toggle_conflict_of_interest', $plugin_users, 'toggle_conflict_of_interest' );
		$this->loader->add_action( 'wp_ajax_toggle_conflict_of_interest', $plugin_users, 'toggle_conflict_of_interest' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_user_conflicts_of_interest', $plugin_users, 'get_user_conflicts_of_interest' );
		$this->loader->add_action( 'wp_ajax_get_user_conflicts_of_interest', $plugin_users, 'get_user_conflicts_of_interest' );

		$this->loader->add_action( 'wp_ajax_nopriv_toggle_partner_member', $plugin_users, 'toggle_partner_member' );
		$this->loader->add_action( 'wp_ajax_toggle_partner_member', $plugin_users, 'toggle_partner_member' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_partner_member_info', $plugin_users, 'get_partner_member_info' );
		$this->loader->add_action( 'wp_ajax_get_partner_member_info', $plugin_users, 'get_partner_member_info' );

		$this->loader->add_action( 'wp_ajax_nopriv_remove_partner_member', $plugin_users, 'remove_partner_member' );
		$this->loader->add_action( 'wp_ajax_remove_partner_member', $plugin_users, 'remove_partner_member' );

		$this->loader->add_filter( 'wp_new_user_notification_email_admin', $plugin_users, 'disable_admin_new_user_notification', 10, 3 );
	}

	/**
	 * Register all of the hooks related to post type functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_post_type_hooks() {
		$plugin_userprofile = new UserProfile( $this->get_plugin_name(), $this->get_version() );
		$plugin_credit = new Credit( $this->get_plugin_name(), $this->get_version() );
		$plugin_job_post = new JobPost( $this->get_plugin_name(), $this->get_version() );
		$plugin_profile_notification = new ProfileNotification( $this->get_plugin_name(), $this->get_version() );
		$plugin_types = new Types( $this->get_plugin_name(), $this->get_version() );

		// UserProfile hooks.
		$this->loader->add_action( 'wp_ajax_nopriv_get_userprofiles', $plugin_userprofile, 'get_userprofiles' );
		$this->loader->add_action( 'wp_ajax_get_userprofiles', $plugin_userprofile, 'get_userprofiles' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_userprofile', $plugin_userprofile, 'get_userprofile' );
		$this->loader->add_action( 'wp_ajax_get_userprofile', $plugin_userprofile, 'get_userprofile' );

		$this->loader->add_action( 'wp_ajax_nopriv_save_userprofile', $plugin_userprofile, 'save_userprofile' );
		$this->loader->add_action( 'wp_ajax_save_userprofile', $plugin_userprofile, 'save_userprofile' );

		$this->loader->add_action( 'wp_ajax_nopriv_delete_userprofile', $plugin_userprofile, 'delete_userprofile' );
		$this->loader->add_action( 'wp_ajax_delete_userprofile', $plugin_userprofile, 'delete_userprofile' );

		// Credit hooks.
		$this->loader->add_action( 'wp_ajax_nopriv_get_credits', $plugin_credit, 'get_credits' );
		$this->loader->add_action( 'wp_ajax_get_credits', $plugin_credit, 'get_credits' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_credit', $plugin_credit, 'get_credit' );
		$this->loader->add_action( 'wp_ajax_get_credit', $plugin_credit, 'get_credit' );

		$this->loader->add_action( 'wp_ajax_nopriv_save_credit', $plugin_credit, 'save_credit' );
		$this->loader->add_action( 'wp_ajax_save_credit', $plugin_credit, 'save_credit' );

		$this->loader->add_action( 'wp_ajax_nopriv_delete_credit', $plugin_credit, 'delete_credit' );
		$this->loader->add_action( 'wp_ajax_delete_credit', $plugin_credit, 'delete_credit' );

		$this->loader->add_action( 'save_post_credit', $plugin_credit, 'on_credit_save' );
		$this->loader->add_action( 'before_delete_post', $plugin_credit, 'on_credit_delete' );

		// Job Post hooks.
		$this->loader->add_action( 'wp_ajax_nopriv_get_job_posts', $plugin_job_post, 'get_job_posts' );
		$this->loader->add_action( 'wp_ajax_get_job_posts', $plugin_job_post, 'get_job_posts' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_job_post', $plugin_job_post, 'get_job_post' );
		$this->loader->add_action( 'wp_ajax_get_job_post', $plugin_job_post, 'get_job_post' );

		$this->loader->add_action( 'wp_ajax_nopriv_save_job_post', $plugin_job_post, 'save_job_post' );
		$this->loader->add_action( 'wp_ajax_save_job_post', $plugin_job_post, 'save_job_post' );

		$this->loader->add_action( 'wp_ajax_nopriv_delete_job_post', $plugin_job_post, 'delete_job_post' );
		$this->loader->add_action( 'wp_ajax_delete_job_post', $plugin_job_post, 'delete_job_post' );

		$this->loader->add_action( 'save_post_job_post', $plugin_job_post, 'on_job_post_save' );

		// Profile Notification hooks.
		$this->loader->add_action( 'wp_ajax_nopriv_get_profile_notifications', $plugin_profile_notification, 'get_profile_notifications' );
		$this->loader->add_action( 'wp_ajax_get_profile_notifications', $plugin_profile_notification, 'get_profile_notifications' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_profile_notification', $plugin_profile_notification, 'get_profile_notification' );
		$this->loader->add_action( 'wp_ajax_get_profile_notification', $plugin_profile_notification, 'get_profile_notification' );

		$this->loader->add_action( 'wp_ajax_nopriv_save_profile_notification', $plugin_profile_notification, 'save_profile_notification' );
		$this->loader->add_action( 'wp_ajax_save_profile_notification', $plugin_profile_notification, 'save_profile_notification' );

		$this->loader->add_action( 'wp_ajax_nopriv_delete_profile_notification', $plugin_profile_notification, 'delete_profile_notification' );
		$this->loader->add_action( 'wp_ajax_delete_profile_notification', $plugin_profile_notification, 'delete_profile_notification' );

		// Types hooks.
		$this->loader->add_action( 'wp_ajax_nopriv_get_skills', $plugin_types, 'get_skills' );
		$this->loader->add_action( 'wp_ajax_get_skills', $plugin_types, 'get_skills' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_positions', $plugin_types, 'get_positions' );
		$this->loader->add_action( 'wp_ajax_get_positions', $plugin_types, 'get_positions' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_locations', $plugin_types, 'get_locations' );
		$this->loader->add_action( 'wp_ajax_get_locations', $plugin_types, 'get_locations' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_production_types', $plugin_types, 'get_production_types' );
		$this->loader->add_action( 'wp_ajax_get_production_types', $plugin_types, 'get_production_types' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_employment_types', $plugin_types, 'get_employment_types' );
		$this->loader->add_action( 'wp_ajax_get_employment_types', $plugin_types, 'get_employment_types' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_union_statuses', $plugin_types, 'get_union_statuses' );
		$this->loader->add_action( 'wp_ajax_get_union_statuses', $plugin_types, 'get_union_statuses' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_experience_levels', $plugin_types, 'get_experience_levels' );
		$this->loader->add_action( 'wp_ajax_get_experience_levels', $plugin_types, 'get_experience_levels' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_genders', $plugin_types, 'get_genders' );
		$this->loader->add_action( 'wp_ajax_get_genders', $plugin_types, 'get_genders' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_ethnicities', $plugin_types, 'get_ethnicities' );
		$this->loader->add_action( 'wp_ajax_get_ethnicities', $plugin_types, 'get_ethnicities' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_age_ranges', $plugin_types, 'get_age_ranges' );
		$this->loader->add_action( 'wp_ajax_get_age_ranges', $plugin_types, 'get_age_ranges' );

		$this->loader->add_action( 'wp_ajax_nopriv_get_partner_organizations', $plugin_types, 'get_partner_organizations' );
		$this->loader->add_action( 'wp_ajax_get_partner_organizations', $plugin_types, 'get_partner_organizations' );
	}

	/**
	 * Register all of the hooks related to GraphQL type definitions.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_graphql_types() {
		$plugin_graphql_types = new GraphQLTypes( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'graphql_register_types', $plugin_graphql_types, 'register_types' );
	}

	/**
	 * Register all of the hooks related to cron jobs.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_cron_jobs() {
		$plugin_cron = new Cron( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'rise_expire_job_posts', $plugin_cron, 'expire_job_posts' );
	}

	/**
	 * Register all of the hooks related to GraphQL queries.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_graphql_queries() {
		$plugin_graphql_queries = new GraphQLQueries( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'graphql_register_types', $plugin_graphql_queries, 'register_queries' );
		$this->loader->add_action( 'graphql_register_types', $plugin_graphql_queries, 'register_query_filters' );
		$this->loader->add_filter( 'graphql_connection_query_args', $plugin_graphql_queries, 'modify_user_connection_query_args', 10, 5 );
		$this->loader->add_filter( 'graphql_map_input_fields_to_wp_user_query', $plugin_graphql_queries, 'map_user_meta_query_args', 10, 2 );
	}

	/**
	 * Register all of the hooks related to WooCommerce functionality.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_woocommerce_hooks() {
		$plugin_woocommerce = new WooCommerce( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_woocommerce, 'on_woocommerce_order_complete' );
	}

	/**
	 * Register all of the hooks related to GraphQL mutations.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_graphql_mutations() {
		$plugin_graphql_mutations = new GraphQLMutations( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'graphql_register_types', $plugin_graphql_mutations, 'register_mutations' );
		$this->loader->add_filter( 'graphql_user_object_mutation_data_is_valid', $plugin_graphql_mutations, 'validate_user_data', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'plugin_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_settings_init' );
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'register_rise_basic_stats_widget' );
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'remove_dashboard_widgets' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'remove_menu_pages', 999 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @access   private
	 * @since    0.1.0
	 */
	private function define_public_hooks() {
		$plugin_public = new PublicFacing( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
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
	 * @return string The name of the plugin.
	 * @since     0.1.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return Loader Orchestrates the hooks of the plugin.
	 * @since     0.1.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string The version number of the plugin.
	 * @since     0.1.0
	 */
	public function get_version() {
		return $this->version;
	}

} 