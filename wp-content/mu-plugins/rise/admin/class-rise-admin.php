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
			'manage_options', // Capability required to access the page
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

		// Add a section for the settings
		// add_settings_section(
		// 	'rise_directory_settings_section',
		// 	'RISE Directory Settings',
		// 	[$this, 'rise_directory_settings_section_callback'],
		// 	'rise-admin'
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
		echo '<p>Customize the RISE Directory plugin settings.</p>';
	}

	/**
	 * Callback function to render the stats section.
	 */
	public function rise_directory_stats_section_callback() {
		echo wp_kses_post( self::section_html_start() );

		echo wp_kses_post( self::crew_member_stats__basic() );

		echo wp_kses_post( self::crew_member_stats__detailed() );

		echo wp_kses_post( self::dev_info() );

		echo wp_kses_post( self::section_html_end() );
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
	 * Generate basic stats for
	 *
	 * @return string HTML output.
	 */
	private static function crew_member_stats__basic() {
		// Get all users with the 'crew-member' role. Then separate the users by those who are authors of at least one `credit` post type post, and those who are not.
		$crew_members = get_users( ['role' => 'crew-member'] );
		$authors      = [];
		$non_authors  = [];
		$output       = '<h2>Base Stats</h2><div class="data-feature">';

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

		$output .= sprintf( '<p>There are <strong>%s</strong> users registered on the site.</p>', count( $crew_members ) );
		$output .= sprintf( '<p>There are <strong>%s</strong> users who have at least one credit.</p>', count( $authors ) );
		$output .= sprintf( '<p>There are <strong>%s</strong> users who have <strong>no</strong> credits.</p>', count( $non_authors ) );

		// Output all users with no credits
		// TODO Make this a download link
		// $output .= sprintf( '<p>Users with no credits:</p><pre>%s</pre>', esc_textarea( $non_authors_data ) );

		$output .= '</div>';

		return $output;
	}

	/**
	 * Print some more detailed user statistics.
	 *
	 * @return void
	 */
	private static function crew_member_stats__detailed() {
		$data__credits = [
			'position__department' => [
				'label' => 'Departments',
				'slug'  => 'position',
			],
			'position__job'        => [
				'label' => 'Jobs',
				'slug'  => 'position',
			],
			'skills'               => [
				'label' => 'Job Skills',
				'slug'  => 'skill',
			],
		];

		$data__user = [
			'locations'           => [
				'label' => 'Locations',
				'slug'  => 'location',
			],
			'racial_identities'   => [
				'label' => 'Racial Identifiers',
				'slug'  => 'racial_identity',
			],
			'gender_identities'   => [
				'label' => 'Gender Identifiers',
				'slug'  => 'gender_identity',
			],
			'personal_identities' => [
				'label' => 'Additional Identifiers',
				'slug'  => 'personal_identity',
			],
		];

		$output = '<div class="stats-tables">';

		// Credit-only data
		if ( $data__credits ) {
			foreach ( $data__credits as $datapoint => $data ) {
				$label = $data['label'];
				$slug  = $data['slug'];

				$output .= '<table>';
				$output .= '<caption>' . $label . '</caption>';
				$output .= '<thead><tr><th style="text-align: left;">Label</th><th class="sort" data-sort-dir="desc">Count</th></tr></thead>';
				$output .= '<tbody>';

				// Get all term IDs for the `$datapoint` taxonomy
				$taxonomy_plural = 'skills';
				if ( 'position__job' === $datapoint || 'position__department' === $datapoint ) {
					$taxonomy_plural = 'positions';
				}

				$terms = get_terms( [
					'taxonomy'   => $slug,
					'hide_empty' => false,
					'parent'     => 'position__department' === $datapoint ? 0 : '',
					'childless'  => 'position__job' === $datapoint ? true : false,
				] );

				$data = [];

				foreach ( $terms as $term ) {
					$args    = [$taxonomy_plural => $term->term_id];
					$results = search_and_filter_crew_members( $args );

					$data[] = [
						'name'  => $term->name,
						'count' => count( $results ),
					];
				}

				// Sort the data array by the "count" value in descending order by default
				usort( $data, function ( $a, $b ) {
					return $b['count'] - $a['count'];
				} );

				foreach ( $data as $datum ) {
					$output .= sprintf(
						'<tr><td>%s</td><td>%s</td></tr>',
						$datum['name'],
						$datum['count']
					);
				}

				$output .= '</tbody>';
				$output .= '</table>';
			}
		}

		// User-only data
		if ( $data__user ) {
			foreach ( $data__user as $datapoint => $data ) {
				$label = $data['label'];
				$slug  = $data['slug'];

				$output .= '<table>';
				$output .= '<caption>' . $label . '</caption>';
				$output .= '<thead><tr><th style="text-align: left;">Label</th><th class="sort" data-sort-dir="desc">Count</th></tr></thead>';
				$output .= '<tbody>';

				$terms = get_terms( [
					'taxonomy'   => $slug,
					'hide_empty' => false,
				] );

				$data = [];

				foreach ( $terms as $term ) {
					$args    = [$slug => $term->term_id];
					$results = query_users_with_terms( $args );

					$data[] = [
						'name'  => $term->name,
						'count' => count( $results ),
					];
				}

				// Sort the data array by the "count" value in descending order by default
				usort( $data, function ( $a, $b ) {
					return $b['count'] - $a['count'];
				} );

				foreach ( $data as $datum ) {
					$output .= sprintf(
						'<tr><td>%s</td><td>%s</td></tr>',
						$datum['name'],
						$datum['count']
					);
				}

				$output .= '</tbody>';
				$output .= '</table>';
			}
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Generate some developer info.
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
