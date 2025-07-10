<?php

namespace RHD\Rise\Core;

use RHD\Rise\Includes\Search;
use RHD\Rise\Includes\Users;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
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
		\wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rise-admin.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		$current_screen = \get_current_screen();
		if ( $current_screen && 'toplevel_page_rise-admin' === $current_screen->id ) {
			\wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rise-table-sort.js', [], $this->version, false );
		}
	}

	/**
	 * Register the admin menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.2
	 */
	public function plugin_options_page() {
		\add_menu_page(
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

		\settings_fields( 'rise_directory_options' ); // Add the settings field group
		\do_settings_sections( 'rise-admin' ); // Render the settings section(s)

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
		// Add a section for the stats
		\add_settings_section(
			'rise_directory_stats_section',
			'RISE Directory Statistics',
			[$this, 'rise_directory_stats_section_callback'],
			'rise-admin'
		);
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
		\wp_add_dashboard_widget(
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
		printf( '%s', wp_kses_post( self::crew_member_stats__basic() ) );

		printf( '<p><a href="%s">Details statistics</a></p>', esc_url( admin_url( 'admin.php?page=rise-admin' ) ) );
	}

	/**
	 * Remove dashboard widgets.
	 *
	 * @return void
	 */
	public function remove_dashboard_widgets() {
		global $wp_meta_boxes;

		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
	}

	/**
	 * Remove pages from the admin menu (but leave them accessible by URL).
	 *
	 * @return void
	 */
	public function remove_menu_pages() {
		// Posts
		\remove_menu_page( 'edit.php' );

		// Comments
		\remove_menu_page( 'edit-comments.php' );

		// Plugins
		// remove_menu_page( 'plugins.php' );

		// Appearance
		// remove_menu_page( 'themes.php' );
	}

	/**
	 * Redirect crew members to the frontend URL after login from wp-login.php.
	 *
	 * @param  string           $redirect_to The redirect destination URL.
	 * @param  string           $request     The requested redirect destination URL passed as a parameter.
	 * @param  WP_User|WP_Error $user        WP_User object if login was successful, WP_Error object otherwise.
	 * @return string           The redirect URL.
	 */
	public function redirect_crew_members_after_login( $redirect_to, $request, $user ) {
		// Check if we have a valid user object
		if ( !is_wp_error( $user ) && is_object( $user ) && isset( $user->ID ) ) {
			// Check if user has crew-member role
			if ( in_array( 'crew-member', $user->roles ) ) {
				// Check if this appears to be from the main WordPress login
				// (typically redirects to admin area by default)
				$admin_url         = admin_url();
				$is_admin_redirect = strpos( $redirect_to, $admin_url ) === 0;

				if ( $is_admin_redirect && defined( 'RISE_FRONTEND_URL' ) ) {
					return \RISE_FRONTEND_URL;
				}
			}
		}

		return $redirect_to;
	}

	/**
	 * Generate basic stats for the user base.
	 *
	 * @return string HTML output.
	 */
	private static function crew_member_stats__basic() {
		// Get all users with the 'crew-member' role. Then separate the users by those who are authors of at least one `credit` post type post, and those who are not.
		$crew_members = \get_users( ['role' => 'crew-member'] );
		$authors      = [];
		$non_authors  = [];
		$output       = '<h2>Base Stats</h2><div class="data-feature">';

		foreach ( $crew_members as $crew_member ) {
			$posts = \get_posts( [
				'author'      => $crew_member->ID,
				'post_type'   => 'credit',
				'post_status' => 'publish',
			] );

			if ( \count( $posts ) > 0 ) {
				$authors[] = $crew_member;
				continue;
			}

			$non_authors[] = $crew_member;
		}

		/**
		 * Get all non-author email addresses, first names (user meta), and last names (user meta)
		 */
		// $non_authors_data = "Email,First Name,Last Name\n";
		// foreach ( $non_authors as $non_author ) {
		// 	$data = [
		// 		'email'      => '"' . $non_author->user_email . '"',
		// 		'first_name' => '"' . get_user_meta( $non_author->ID, 'first_name', true ) . '"',
		// 		'last_name'  => '"' . get_user_meta( $non_author->ID, 'last_name', true ) . '"',
		// 	];

		// 	// $non_authors_data .= implode( ',', $data ) . "\n";
		// }

		// TODO Make this a download link
		// Output all users with no credits
		// $output .= sprintf( '<p>Users with no credits:</p><pre>%s</pre>', esc_textarea( $non_authors_data ) );

		// Use the find() method to query users
		$disabled_profiles = \pods(
			'user',
			[
				'where' => 'd.disable_profile = 1',
			]
		);

		$output .= sprintf( '<p>Users registered on the site: <strong>%s</strong></p>', \count( $crew_members ) );
		$output .= sprintf( '<p>Users with at least one credit: <strong>%s</strong></p>', \count( $authors ) );
		$output .= sprintf( '<p>Users with <strong>no</strong> credits: <strong>%s</strong></p>', \count( $non_authors ) );
		$output .= sprintf( '<p>Users with hidden profiles ("search only"): <strong>%s</strong></p>', $disabled_profiles->total_found() );

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

				$output .= self::generate_table_open( $label );

				// Get all term IDs for the `$datapoint` taxonomy
				$taxonomy_plural = 'skills';
				if ( 'position__job' === $datapoint || 'position__department' === $datapoint ) {
					$taxonomy_plural = 'positions';
				}

				$terms = \get_terms( [
					'taxonomy'   => $slug,
					'hide_empty' => false,
					'parent'     => 'position__department' === $datapoint ? 0 : '',
					'childless'  => 'position__job' === $datapoint ? true : false,
				] );

				$data = [];

				foreach ( $terms as $term ) {
					$args    = [$taxonomy_plural => $term->term_id];
					$results = Search::search_and_filter_crew_members( $args );

					$data[] = [
						'name'  => $term->name,
						'count' => \count( $results ),
					];
				}

				$output .= self::generate_table_close( $data );
			}
		}

		// User-only data
		if ( $data__user ) {
			foreach ( $data__user as $datapoint => $data ) {
				$label = $data['label'];
				$slug  = $data['slug'];

				$output .= self::generate_table_open( $label );

				$terms = \get_terms( [
					'taxonomy'   => $slug,
					'hide_empty' => false,
				] );

				$data = [];

				foreach ( $terms as $term ) {
					$args    = [$slug => $term->term_id];
					$results = Users::query_users( $args );

					$data[] = [
						'name'  => $term->name,
						'count' => \count( $results ),
					];
				}

				$output .= self::generate_table_close( $data );
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
			'PHP Version' => phpversion(),
			'WP Version'  => \get_bloginfo( 'version' ),
		];

		$output = '<p>Development Info:</p><pre>';
		foreach ( $info as $key => $value ) {
			$output .= sprintf( "%s: %s\n", $key, $value );
		}
		$output .= '</pre>';

		return $output;
	}

	/**
	 * Generate the opening HTML for the stats tables.
	 *
	 * @param  string $label
	 * @return void
	 */
	private static function generate_table_open( $label ) {
		$output = '<table>';
		$output .= '<caption>' . $label . '</caption>';
		$output .= '<thead><tr><th style="text-align: left;">Label</th><th class="sort" data-sort-dir="desc">Count</th></tr></thead>';
		$output .= '<tbody>';

		return $output;
	}

	/**
	 * Generate the closing HTML for the stats tables.
	 *
	 * @param  array  $data The data to be output in the table.
	 * @return void
	 */
	private static function generate_table_close( $data ) {
		// Sort the data array by the "count" value in descending order by default
		usort( $data, function ( $a, $b ) {
			return $b['count'] - $a['count'];
		} );

		$output = '';

		foreach ( $data as $datum ) {
			$output .= sprintf(
				'<tr><td>%s</td><td>%s</td></tr>',
				$datum['name'],
				$datum['count']
			);
		}

		$output .= '</tbody>';
		$output .= '</table>';

		return $output;
	}

	/**
	 * Add "Expired" view to job posts admin screen
	 *
	 * @param  array $views   The array of views
	 * @return array Modified views array
	 */
	public function add_admin_job_posts_views( $views ) {
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

		// TODO Determine if 'pending' args are needed
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
}
