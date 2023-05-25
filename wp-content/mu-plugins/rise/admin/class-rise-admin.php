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
	 * @return void
	 */
	public function plugin_options_page_callback() {
		?>
		<div class="wrap">
			<h1>RISE Directory Options</h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'rise_directory_options' ); // Add the settings field group
					do_settings_sections( 'rise-directory-options' ); // Render the settings section(s)
					submit_button(); // Add the submit button
				?>
			</form>
		</div>
		<?php
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
		add_settings_section(
			'rise_directory_settings_section',
			'RISE Directory Settings',
			[$this, 'rise_directory_settings_section_callback'],
			'rise-directory-options'
		);

		// Add a field for 'rise_frontend_url' in the section
		/**
		 * Sample setting: Frontend URL
		 */
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

		printf( '<p>There are <strong>%s</strong> users registered on the site.</p>', count( $crew_members ) );
		printf( '<p>There are <strong>%s</strong> users who have at least one credit.</p>', count( $authors ) );
		printf( '<p>There are <strong>%s</strong> users who have <strong>no</strong> credits.</p>', count( $non_authors ) );
		printf( '<p>Users with no credits:</p><pre>%s</pre>', esc_textarea( $non_authors_data ) );
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
