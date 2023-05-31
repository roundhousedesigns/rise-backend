<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Rise
 * @subpackage Rise/admin
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
 * @package    Rise
 * @subpackage Rise/admin
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 */
class Rise_Admin {

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
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rise_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rise_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rise-admin.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rise_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rise_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rise-admin.js', ['jquery'], $this->version, false );
	}

	/**
	 * Register the admin menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.2beta
	 */
	public function plugin_options_page() {
		add_options_page(
			'RISE Directory Options', // Page title
			'RISE Options', // Menu title
			'manage_options', // Capability required to access the page
			'rise-directory-options', // Menu slug
			[$this, 'plugin_options_page_callback']// Callback function to render the page content
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
		do_settings_sections( 'rise-directory-options' ); // Render the settings section(s)

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
			'rise-directory-options'
		);

		// Add a section for the settings
		// add_settings_section(
		// 	'rise_directory_settings_section',
		// 	'RISE Directory Settings',
		// 	[$this, 'rise_directory_settings_section_callback'],
		// 	'rise-directory-options'
		// );

		// Add a field for 'rise_frontend_url' in the section
		/**
		 * Sample setting: Frontend URL
		 */
		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// add_settings_field(
		// 	'rise_frontend_url',
		// 	'Frontend URL',
		// 	[$this, 'rise_frontend_url_callback'],
		// 	'rise-directory-options',
		// 	'rise_directory_settings_section'
		// );
	}

	/**
	 * Callback function to render the settings section.
	 *
	 * @return void
	 */
	public function rise_directory_settings_section_callback() {
		echo '<p>Customize the RISE Directory plugin settings.</p>';
	}

	/**
	 * Callback function to render the stats section.
	 */
	public function rise_directory_stats_section_callback() {
		self::section_html_start();

		echo wp_kses_post( self::crew_member_stats__basic() );

		echo wp_kses_post( self::crew_member_stats__detailed() );

		echo wp_kses_post( self::dev_info() );

		self::section_html_end();
	}

	/**
	 * Output the HTML for the start of a section.
	 *
	 * @return string HTML output.
	 */
	private static function section_html_start() {
		return "<div class='wrap'>\n<h1>RISE Directory Options</h1>\n<form method='post' action='options.php'>";
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
	 * Generate basic stats for
	 *
	 * @return string HTML output.
	 */
	private static function crew_member_stats__basic() {
		// Get all users with the 'crew-member' role. Then separate the users by those who are authors of at least one `credit` post type post, and those who are not.
		$crew_members = get_users( ['role' => 'crew-member'] );
		$authors      = [];
		$non_authors  = [];

		foreach ( $crew_members as $crew_member ) {
			$posts = get_posts( [
				'author'      => $crew_member->ID,
				'post_type'   => 'credit',
				'post_status' => 'publish',
			] );

			if ( count( $posts ) > 0 ) {
				$authors[] = $crew_member;
			} else {
				$non_authors[] = $crew_member;
			}
		}

		// Get all non-author email addresses, first names (user meta), and last names (user meta)
		$non_authors_data = "Email,First Name,Last Name\n";
		foreach ( $non_authors as $non_author ) {
			$data = [
				'email'      => '"' . $non_author->user_email . '"',
				'first_name' => '"' . get_user_meta( $non_author->ID, 'first_name', true ) . '"',
				'last_name'  => '"' . get_user_meta( $non_author->ID, 'last_name', true ) . '"',
			];

			$non_authors_data .= implode( ',', $data ) . "\n";
		}

		$output = '';
		$output .= sprintf( '<p>There are <strong>%s</strong> users registered on the site.</p>', count( $crew_members ) );
		$output .= sprintf( '<p>There are <strong>%s</strong> users who have at least one credit.</p>', count( $authors ) );
		$output .= sprintf( '<p>There are <strong>%s</strong> users who have <strong>no</strong> credits.</p>', count( $non_authors ) );

		// Output all users with no credits
		// TODO Make this a download link
		// $output .= sprintf( '<p>Users with no credits:</p><pre>%s</pre>', esc_textarea( $non_authors_data ) );

		return $output;
	}

	/**
	 * Generate detailed stats for crew members.
	 *
	 * @return string HTML output.
	 */
	private static function crew_member_stats__detailed() {
		// phpcs:disable Squiz.PHP.CommentedOutCode.Found

		// FOR NOW: Uncomment any of the following to get stats for that data point. This will be automated in the future.
		// Data points will appear in the order they're set up here, but they will all be part of the same data set.

		$data__credits = [
			'position__department' => 'position',
			// 'position__job'        => 'position',
			// 'skills'               => 'skill',
		];

		$data__user = [
			// 'locations'         => 'location',
			// 'racial_identities' => 'racial_identity',
			// 'gender_identities' => 'gender_identity',
		];

		$output = '';
		$data   = [];

		// Credit-only data
		if ( $data__credits ) {
			foreach ( $data__credits as $datapoint => $tax_slug ) {
				// Get all term IDs for the $datapoint taxonomy
				$taxonomy_plural = 'position__job' === $datapoint || 'position__department' === $datapoint ? 'positions' : $datapoint;

				$terms = get_terms( [
					'taxonomy'   => $tax_slug,
					'hide_empty' => false,
					// if $datapoint is 'position__department', only get top level terms. If it is 'position__job', only get child terms. Otherwise, get all terms.
					'parent'     => 'position__department' === $datapoint ? 0 : '',
					'childless'  => 'position__job' === $datapoint ? true : false,
				] );

				$terms_plucked = [];
				foreach ( $terms as $term ) {
					$terms_plucked[$term->name] = $term->term_id;
				}

				foreach ( $terms_plucked as $name => $id ) {
					$args    = [$taxonomy_plural => $id];
					$results = search_and_filter_crew_members( $args );

					$data[] = [
						'name'  => $name,
						'count' => count( $results ),
					];
				}
			}
		}

		// User-only data
		if ( $data__user ) {
			foreach ( $data__user as $datapoint => $tax_slug ) {
				// Get all term IDs for the $datapoint taxonomy
				$terms = get_terms( [
					'taxonomy'   => $tax_slug,
					'hide_empty' => false,
				] );

				$terms_plucked = [];
				foreach ( $terms as $term ) {
					$terms_plucked[$term->name] = $term->term_id;
				}

				foreach ( $terms_plucked as $name => $id ) {
					$args = [$tax_slug => $id];

					$results = query_users_with_terms( $args );

					$data[] = [
						'name'  => $name,
						'count' => count( $results ),
					];
				}
			}
		}

		$output .= '<p>USER BREAKDOWN (Uncomment datapoints in class-rise-admin.php):</p><pre>';
		$output .= 'Label,Count' . "\n";
		foreach ( $data as $datum ) {
			$output .= sprintf( '"%s",%s' . "\n", $datum['name'], $datum['count'] );
		}
		$output .= '</pre>';

		return $output;

		// phpcs:enable Squiz.PHP.CommentedOutCode.Found
	}

	/**
	 * Generate basic stats for
	 *
	 * @return string HTML output.
	 */
	private static function dev_info() {
		$info = [
			'PHP Version'       => phpversion(),
			'WP Version'        => get_bloginfo( 'version' ),
			'RISE_FRONTEND_URL' => defined( 'RISE_FRONTEND_URL' ) ? RISE_FRONTEND_URL : 'Not set',
		];

		$output = '<p>Development Info:</p><pre>';
		foreach ( $info as $key => $value ) {
			$output .= sprintf( "%s: %s\n", $key, $value );
		}
		$output .= '</pre>';

		return $output;
	}

	/**
	 * Callback function for the 'rise_frontend_url' field
	 *
	 * @return void
	 */
	public function rise_frontend_url_callback() {
		$value = get_option( 'rise_frontend_url' );
		echo '<input type="text" name="rise_frontend_url" value="' . esc_attr( $value ) . '" />';
	}
}
