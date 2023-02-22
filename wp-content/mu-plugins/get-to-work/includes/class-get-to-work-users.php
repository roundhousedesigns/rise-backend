<?php
/**
 * Registers user taxonomies and data.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Get_To_Work_Users {
	/**
	 * Add user roles with capabilities.
	 *
	 * @return void
	 */
	public function add_roles() {
		$roles = [
			'crew-member' => [
				'read'         => true,
				'list_users'   => true,
				'create_posts' => true,
				'edit_posts'   => true,
				'delete_posts' => true,
			],
		];

		foreach ( $roles as $role => $caps ) {
			add_role( $role, $caps );
		}

	}

	/**
	 * Registers the `gender_identity` taxonomy,
	 * for use with 'user'.
	 */
	public function gender_identity_init() {
		self::register_user_taxonomy( 'gender_identity', 'gender_identities', 'Gender Identity', 'Gender Identities' );
	}

	/**
	 * Add Gender Identity to User menu
	 *
	 * @return void
	 */
	public function add_gender_identity_to_user_menu() {
		self::add_taxonomy_to_user_menu( __( 'Gender Identity', 'gtw' ), __( 'Gender Identity', 'gtw' ), 'gender_identity' );
	}

	/**
	 * Add Gender Identity field to user profile
	 *
	 * @param  WP_User $user
	 * @return void
	 */
	public function add_gender_identity_to_user_profile( $user ) {
		self::user_profile_taxonomy_term_checkboxes( $user, 'gender_identity', 'Gender Identity' );
	}

	/**
	 * Save Gender Identity on user profile update
	 *
	 * @param  int    $user_id
	 * @return void
	 */
	public function save_gender_identity_on_user_profile( $user_id ) {
		self::save_taxonomy_terms_on_user_profile( $user_id, 'gender_identity' );
	}

	/**
	 * Registers the `racial_identity` taxonomy,
	 * for use with 'user'.
	 */
	public function racial_identity_init() {
		self::register_user_taxonomy( 'racial_identity', 'racial_identities', 'Racial Identity', 'Racial Identities' );
	}

	/**
	 * Add Racial Identity to User menu
	 *
	 * @return void
	 */
	public function add_racial_identity_to_user_menu() {
		self::add_taxonomy_to_user_menu( __( 'Racial Identity', 'gtw' ), __( 'Racial Identity', 'gtw' ), 'racial_identity' );
	}

	/**
	 * Add Racial Identity field to user profile
	 *
	 * @param  WP_User $user
	 * @return void
	 */
	public function add_racial_identity_to_user_profile( $user ) {
		self::user_profile_taxonomy_term_checkboxes( $user, 'racial_identity', 'Racial Identity' );
	}

	/**
	 * Save Racial Identity on user profile update
	 *
	 * @param  int    $user_id
	 * @return void
	 */
	public function save_racial_identity_on_user_profile( $user_id ) {
		self::save_taxonomy_terms_on_user_profile( $user_id, 'racial_identity' );
	}

	/**
	 * Registers the `personal_identity` taxonomy,
	 * for use with 'user'.
	 */
	public function personal_identity_init() {
		self::register_user_taxonomy( 'personal_identity', 'personal_identities', 'Personal Identity', 'Personal Identities' );
	}

	/**
	 * Add Personal Identity to User menu
	 *
	 * @return void
	 */
	public function add_personal_identity_to_user_menu() {
		self::add_taxonomy_to_user_menu( __( 'Personal Identity', 'gtw' ), __( 'Personal Identity', 'gtw' ), 'personal_identity' );
	}

	/**
	 * Add Personal Identity field to user profile
	 *
	 * @param  WP_User $user
	 * @return void
	 */
	public function add_personal_identity_to_user_profile( $user ) {
		self::user_profile_taxonomy_term_checkboxes( $user, 'personal_identity', 'Personal Identity' );
	}

	/**
	 * Save Personal Identity on user profile update
	 *
	 * @param  int    $user_id
	 * @return void
	 */
	public function save_personal_identity_on_user_profile( $user_id ) {
		self::save_taxonomy_terms_on_user_profile( $user_id, 'personal_identity' );
	}

	/**
	 * Register a taxonomy for use with `user`.
	 *
	 * @param  string $taxonomy        The taxonomy slug.
	 * @param  string $taxonomy_plural The plural taxonomy slug (for GraphQL).
	 * @param  string $singular        The singular name of the taxonomy.
	 * @param  string $plural          The plural name of the taxonomy.
	 * @param  bool   $hierarchical    Whether the taxonomy is hierarchical.
	 * @return void
	 */
	private function register_user_taxonomy( $taxonomy, $taxonomy_plural, $singular, $plural, $hierarchical = false ) {
		$args = [
			'hierarchical'          => $hierarchical,
			'public'                => true,
			'show_in_nav_menus'     => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'rewrite'               => true,
			'capabilities'          => [
				'manage_terms' => 'edit_posts',
				'edit_terms'   => 'edit_posts',
				'delete_terms' => 'edit_posts',
				'assign_terms' => 'edit_posts',
			],
			'labels'                => [
				'name'                       => __( $plural, 'gtw' ),
				'singular_name'              => _x( $singular, 'taxonomy general name', 'gtw' ),
				'search_items'               => __( 'Search ' . $plural, 'gtw' ),
				'popular_items'              => __( 'Popular ' . $plural, 'gtw' ),
				'all_items'                  => __( 'All ' . $plural, 'gtw' ),
				'parent_item'                => __( 'Parent ' . $singular, 'gtw' ),
				'parent_item_colon'          => __( 'Parent Personal Identity:', 'gtw' ),
				'edit_item'                  => __( 'Edit ' . $singular, 'gtw' ),
				'update_item'                => __( 'Update ' . $singular, 'gtw' ),
				'view_item'                  => __( 'View ' . $singular, 'gtw' ),
				'add_new_item'               => __( 'Add New Personal Identity', 'gtw' ),
				'new_item_name'              => __( 'New ' . $singular, 'gtw' ),
				'separate_items_with_commas' => __( 'Separate ' . $plural . ' with commas', 'gtw' ),
				'add_or_remove_items'        => __( 'Add or remove ' . $plural, 'gtw' ),
				'choose_from_most_used'      => __( 'Choose from the most used ' . $plural, 'gtw' ),
				'not_found'                  => __( 'No ' . $plural . ' found.', 'gtw' ),
				'no_terms'                   => __( 'No ' . $plural, 'gtw' ),
				'menu_name'                  => __( $plural, 'gtw' ),
				'items_list_navigation'      => __( $plural . ' list navigation', 'gtw' ),
				'items_list'                 => __( $plural . ' list', 'gtw' ),
				'most_used'                  => _x( 'Most Used', $taxonomy, 'gtw' ),
				'back_to_items'              => __( '&larr; Back to ' . $plural, 'gtw' ),
			],
			'show_in_rest'          => true,
			'show_in_graphql'       => true,
			'graphql_single_name'   => $taxonomy,
			'graphql_plural_name'   => $taxonomy_plural,
			'rest_base'             => $taxonomy,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		];

		register_taxonomy(
			$taxonomy,
			['user'],
			$args
		);
	}

	/**
	 * Add the taxonomy to the user menu.
	 *
	 * @return void
	 */
	private function add_taxonomy_to_user_menu( $page_title, $menu_title, $menu_slug ) {
		add_users_page(
			$page_title,
			$menu_title,
			'edit_users',
			'edit-tags.php?taxonomy=' . $menu_slug
		);
	}

	/**
	 * Generates a checkbox list of terms for a given taxonomy.
	 *
	 * @param  [type] $user
	 * @param  [type] $taxonomy
	 * @param  [type] $name
	 * @return void
	 */
	private function user_profile_taxonomy_term_checkboxes( $user, $taxonomy, $name ) {
		// Get the currently selected terms for the user
		$terms          = get_the_terms( $user->ID, $taxonomy );
		$selected_terms = $terms ? wp_list_pluck( $terms, 'term_id' ) : [];
		$terms          = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		] );

		wp_nonce_field( 'save_' . $taxonomy, $taxonomy . '_nonce' );
		?>

		<!-- Add a new section to the user profile edit screen for the given taxonomy -->
		<h2><?php esc_html_e( $name, 'gtw' ); ?></h2>
		<table class="form-table">
			<tr>
				<!-- Add a field for the taxonomy checkboxes -->
				<th><label><?php esc_html_e( 'Select ' . $name, 'gtw' ); ?></label></th>
				<td>
					<?php foreach ( $terms as $term ) : ?>
						<label>
								<input type="checkbox" name="<?php echo esc_attr( $taxonomy ); ?>[]" value="<?php echo esc_attr( $term->term_id ); ?>"<?php checked( in_array( $term->term_id, $selected_terms, true ), true ); ?>>
								<?php echo esc_html( $term->name ); ?>
						</label>
						<br>
					<?php endforeach; ?>
				</td>
			</tr>
		</table>

		<?php
	}

	/**
	 * Save the taxonomy terms on the user profile.
	 *
	 * @param  int    $user_id  The user ID
	 * @param  string $taxonomy The taxonomy slug
	 * @return void
	 */
	private function save_taxonomy_terms_on_user_profile( $user_id, $taxonomy ) {

		if ( ! current_user_can( 'edit_user', $user_id ) && ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST[$taxonomy . '_nonce'] ) || ! wp_verify_nonce( $_POST[$taxonomy . '_nonce'], 'save_' . $taxonomy ) ) {
			return;
		}

		if ( isset( $_POST[$taxonomy] ) ) {
			$terms = array_map( 'intval', (array) $_POST[$taxonomy] );
			wp_set_object_terms( $user_id, $terms, $taxonomy );
		} else {
			wp_set_object_terms( $user_id, [], $taxonomy );
		}

	}

}
