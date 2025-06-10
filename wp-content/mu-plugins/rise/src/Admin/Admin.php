<?php

namespace RHD\Rise\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Admin
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Admin
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var string $plugin_name The ID of this plugin.
	 * @since    0.1.0
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var string $version The current version of this plugin.
	 * @since    0.1.0
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rise-admin.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();
		if ( $current_screen && 'toplevel_page_rise-admin' === $current_screen->id ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rise-table-sort.js', [], $this->version, false );
		}
	}

	/**
	 * Register the admin menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.2
	 */
	public function plugin_options_page() {
		add_menu_page(
			'RISE Administration', // Page title
			'RISE Admin', // Menu title
			'read', // Capability/role required to access the page
			'rise-admin', // Menu slug
			[$this, 'plugin_options_page_callback'], // Callback function to render the page content
			'dashicons-chart-area', // Menu icon
			'2.2' // Menu position
		);
	}

	/**
	 * Callback function to render the options page.
	 *
	 * @return string HTML output.
	 */
	public function plugin_options_page_callback() {
		self::section_html_start();

		settings_fields( 'rise_directory_options' ); // Add the settings field group
		do_settings_sections( 'rise-admin' ); // Render the settings section(s)

		// Not currently in use
		// submit_button(); // Add the submit button

		self::section_html_end();
	}

	/**
	 * Register the settings for this plugin into the WordPress Dashboard menu.
	 *
	 * @return void
	 */
	public function plugin_settings_init() {
		// Register a setting field for 'rise_frontend_url'
		register_setting( 'rise_directory_options', 'rise_frontend_url', 'esc_url' );

		// Add a section for the stats
		add_settings_section(
			'rise_directory_stats_section',
			'RISE Directory Statistics',
			[$this, 'rise_directory_stats_section_callback'],
			'rise-admin'
		);

		// Example: Add a section for the settings
		// add_settings_section(
		// 	'rise_directory_settings_section',
		// 	'RISE Directory Settings',
		// 	[$this, 'rise_directory_settings_section_callback'],
		// 	'rise-admin'
		// );

		// Example: Add a field for 'rise_frontend_url' in the section
		/**
		 * Sample setting: Frontend URL
		 */
		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// add_settings_field(
		// 	'rise_frontend_url',
		// 	'Frontend URL',
		// 	[$this, 'rise_frontend_url_callback'],
		// 	'rise-admin',
		// 	'rise_directory_settings_section'
		// );
	}

	/**
	 * Callback function to render the settings section.
	 *
	 * @return void
	 */
	public function rise_directory_settings_section_callback() {
		printf( wp_kses( '<p>Customize the RISE Directory plugin settings.</p>', ['p' => []] ) );
	}

	/**
	 * Callback function to render the stats section.
	 */
	public function rise_directory_stats_section_callback() {
		printf( '%s', wp_kses_post( self::section_html_start() ) );
		printf( '%s', wp_kses_post( self::crew_member_stats__basic() ) );
		printf( '%s', wp_kses_post( self::crew_member_stats__detailed() ) );
		printf( '%s', wp_kses_post( self::dev_info() ) );
		printf( '%s', wp_kses_post( self::section_html_end() ) );
	}

	/**
	 * Output the HTML for the start of a section.
	 *
	 * @return string HTML output.
	 */
	private static function section_html_start() {
		return "<div class='wrap rise-admin'>\n<h1 class='wp-heading-inline'>RISE Directory Administration</h1>\n<form method='post' action='options.php'>";
	}

	/**
	 * Output the HTML for the end of a section.
	 *
	 * @return string HTML output.
	 */
	private static function section_html_end() {
		return "</form>\n</div>";
	}

	/**
	 * Register the Basic Stats dashboard widget.
	 *
	 * @return void
	 */
	public function register_rise_basic_stats_widget() {
		wp_add_dashboard_widget(
			'crew_member_stats_widget',
			'RISE Member Stats',
			[$this, 'render_rise_stats_widget_content']
		);
	}

	/**
	 * Render the Basic Stats dashboard widget content.
	 *
	 * @return void
	 */
	public function render_rise_stats_widget_content() {
		echo wp_kses_post( self::crew_member_stats__basic() );
	}

	/**
	 * Remove core dashboard widgets.
	 *
	 * @return void
	 */
	public function remove_dashboard_widgets() {
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	}

	/**
	 * Remove core menu pages.
	 *
	 * @return void
	 */
	public function remove_menu_pages() {
		if ( !current_user_can( 'administrator' ) ) {
			remove_menu_page( 'edit.php' );
			remove_menu_page( 'edit.php?post_type=page' );
			remove_menu_page( 'edit-comments.php' );
			remove_menu_page( 'tools.php' );
		}
	}

	/**
	 * Crew member stats - basic.
	 *
	 * @return string HTML for the stats table.
	 */
	private static function crew_member_stats__basic() {
		$user_count_active            = count_users()['total_users'];
		$user_count_basic             = \rise_get_users_by_subscription_status( 'basic' );
		$user_count_partner           = \rise_get_users_by_subscription_status( 'partner' );
		$user_count_career_member     = \rise_get_users_by_subscription_status( 'career_member' );
		$user_count_associate         = \rise_get_users_by_subscription_status( 'associate' );
		$user_count_pro               = \rise_get_users_by_subscription_status( 'pro' );
		$user_count_partner_user      = \rise_get_users_with_non_empty_meta( 'partner_member_organizations' );
		$user_count_no_status         = \rise_get_users_by_subscription_status( 'none' );
		$user_count_no_status_counted = $user_count_active - $user_count_basic - $user_count_partner - $user_count_career_member - $user_count_associate - $user_count_pro;

		$credit_count_active = wp_count_posts( 'credit' )->publish;

		$job_count_active = wp_count_posts( 'job_post' )->publish;

		$data = [
			['Active RISE Members', $user_count_active],
			['Members With Basic Subscription', $user_count_basic],
			['Members With Partner Subscription', $user_count_partner],
			['Members With Career Member Subscription', $user_count_career_member],
			['Members With Associate Subscription', $user_count_associate],
			['Members With Pro Subscription', $user_count_pro],
			['Partner Organization Members', $user_count_partner_user],
			['Members With Undefined Status', $user_count_no_status_counted],
			['', ''],
			['Active Credits', $credit_count_active],
			['', ''],
			['Active Job Posts', $job_count_active],
		];

		$output = self::generate_table_open( 'RISE Stats (Basic)' );
		$output .= self::generate_table_close( $data );

		return $output;
	}

	/**
	 * Crew member stats - detailed.
	 *
	 * @return string HTML for the stats table.
	 */
	private static function crew_member_stats__detailed() {
		$user_query_args = [
			'meta_query' => [
				'relation' => 'OR',
				[
					'key'     => 'subscription_status',
					'value'   => 'basic',
					'compare' => '=',
				],
				[
					'key'     => 'subscription_status',
					'value'   => 'partner',
					'compare' => '=',
				],
				[
					'key'     => 'subscription_status',
					'value'   => 'career_member',
					'compare' => '=',
				],
				[
					'key'     => 'subscription_status',
					'value'   => 'associate',
					'compare' => '=',
				],
				[
					'key'     => 'subscription_status',
					'value'   => 'pro',
					'compare' => '=',
				],
			],
		];
		$user_count_paid_subscriptions = count( get_users( $user_query_args ) );

		$user_count_profile_complete   = \rise_get_users_by_profile_completion_status( 'complete' );
		$user_count_profile_incomplete = \rise_get_users_by_profile_completion_status( 'incomplete' );

		$user_count_experience_entry  = \rise_get_users_by_experience_level( 'entry' );
		$user_count_experience_junior = \rise_get_users_by_experience_level( 'junior' );
		$user_count_experience_mid    = \rise_get_users_by_experience_level( 'mid' );
		$user_count_experience_senior = \rise_get_users_by_experience_level( 'senior' );

		$user_count_gender_male        = \rise_get_users_by_gender( 'male' );
		$user_count_gender_female      = \rise_get_users_by_gender( 'female' );
		$user_count_gender_nonbinary   = \rise_get_users_by_gender( 'nonbinary' );
		$user_count_gender_no_response = \rise_get_users_by_gender( 'no_response' );

		$data = [
			['Members w/ Paid Subscriptions', $user_count_paid_subscriptions],
			['', ''],
			['Members w/ Complete Profiles', $user_count_profile_complete],
			['Members w/ Incomplete Profiles', $user_count_profile_incomplete],
			['', ''],
			['Members w/ Entry Experience', $user_count_experience_entry],
			['Members w/ Junior Experience', $user_count_experience_junior],
			['Members w/ Mid Experience', $user_count_experience_mid],
			['Members w/ Senior Experience', $user_count_experience_senior],
			['', ''],
			['Male-Identifying Members', $user_count_gender_male],
			['Female-Identifying Members', $user_count_gender_female],
			['Non-Binary Identifying Members', $user_count_gender_nonbinary],
			['No Response Gender Members', $user_count_gender_no_response],
		];

		$output = self::generate_table_open( 'RISE Stats (Detailed)' );
		$output .= self::generate_table_close( $data );

		return $output;
	}

	/**
	 * Developer info.
	 *
	 * @return string HTML for the dev info table.
	 */
	private static function dev_info() {
		global $wp_version;
		$theme_info = wp_get_theme();

		$data = [
			['Site URL', get_site_url()],
			['WordPress Version', $wp_version],
			['Theme', $theme_info->get( 'Name' ) . ' ' . $theme_info->get( 'Version' )],
			['Rise Plugin Version', RISE_VERSION],
		];

		$output = self::generate_table_open( 'Dev Info' );
		$output .= self::generate_table_close( $data );

		return $output;
	}

	/**
	 * Callback function to render the frontend URL setting field.
	 *
	 * @return void
	 */
	public function rise_frontend_url_callback() {
		$frontend_url = get_option( 'rise_frontend_url', '' );
		echo '<input type="url" id="rise_frontend_url" name="rise_frontend_url" value="' . esc_attr( $frontend_url ) . '" />';
		echo '<p class="description">Enter the URL of the frontend application.</p>';
	}

	/**
	 * Generate the opening HTML for a table.
	 *
	 * @param  string $label Table label.
	 * @return string HTML for the table opening.
	 */
	private static function generate_table_open( $label ) {
		$output = "<h3>{$label}</h3>";
		$output .= '<table class="wp-list-table widefat fixed striped">';
		$output .= '<thead><tr><th>Item</th><th>Count</th></tr></thead>';
		$output .= '<tbody>';

		return $output;
	}

	/**
	 * Generate the closing HTML for a table.
	 *
	 * @param  array $data Table data.
	 * @return string HTML for the table closing.
	 */
	private static function generate_table_close( $data ) {
		$output = '';

		foreach ( $data as $row ) {
			$output .= '<tr>';
			$output .= '<td>' . $row[0] . '</td>';
			$output .= '<td>' . $row[1] . '</td>';
			$output .= '</tr>';
		}

		$output .= '</tbody>';
		$output .= '</table>';

		return $output;
	}
} 