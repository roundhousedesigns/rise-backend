<?php
/**
 * Registers user taxonomies and data.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Rise_Users {
	/**
	 * Add user roles with capabilities.
	 *
	 * @return void
	 */
	public function add_roles() {
		$role = get_role( 'crew-member' );
		$role->add_cap( 'edit_credits' );
		$role->add_cap( 'read_credits' );
		$role->add_cap( 'delete_credits' );
		$role->add_cap( 'edit_published_credits' );
		$role->add_cap( 'publish_credits' );
		$role->add_cap( 'delete_published_credits' );

		$roles = [
			'crew-member' => [
				'read'                     => true,
				'list_users'               => true,
				'upload_files'             => true,
				'unfiltered_upload'        => true,
				'edit_files'               => true,
				'edit_credits'             => true,
				'read_credits'             => true,
				'edit_published_credits'   => true,
				'publish_credits'          => true,
				'delete_published_credits' => true,
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
		Rise_Factory::register_taxonomy( ['user'], 'gender_identity', 'gender_identities', 'Gender Identity', 'Gender Identities' );
	}

	/**
	 * Add Gender Identity to User menu
	 *
	 * @return void
	 */
	public function add_gender_identity_to_user_menu() {
		Rise_Factory::add_taxonomy_to_user_menu( __( 'Gender Identity', 'rise' ), __( 'Gender Identity', 'rise' ), 'gender_identity' );
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
		Rise_Factory::save_taxonomy_terms_on_user_profile( $user_id, 'gender_identity' );
	}

	/**
	 * Registers the `racial_identity` taxonomy,
	 * for use with 'user'.
	 */
	public function racial_identity_init() {
		Rise_Factory::register_taxonomy( ['user'], 'racial_identity', 'racial_identities', 'Racial Identity', 'Racial Identities' );
	}

	/**
	 * Add Racial Identity to User menu
	 *
	 * @return void
	 */
	public function add_racial_identity_to_user_menu() {
		Rise_Factory::add_taxonomy_to_user_menu( __( 'Racial Identity', 'rise' ), __( 'Racial Identity', 'rise' ), 'racial_identity' );
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
		Rise_Factory::save_taxonomy_terms_on_user_profile( $user_id, 'racial_identity' );
	}

	/**
	 * Registers the `personal_identity` taxonomy,
	 * for use with 'user'.
	 *
	 * @return void
	 */
	public function personal_identity_init() {
		Rise_Factory::register_taxonomy( ['user'], 'personal_identity', 'personal_identities', 'Personal Identity', 'Personal Identities' );
	}

	/**
	 * Add Personal Identity to User menu
	 *
	 * @return void
	 */
	public function add_personal_identity_to_user_menu() {
		Rise_Factory::add_taxonomy_to_user_menu( __( 'Personal Identity', 'rise' ), __( 'Personal Identity', 'rise' ), 'personal_identity' );
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
		Rise_Factory::save_taxonomy_terms_on_user_profile( $user_id, 'personal_identity' );
	}

	/**
	 * Registers the `union` taxonomy,
	 * for use with 'user'.
	 *
	 * @return void
	 */
	public function union_init() {
		Rise_Factory::register_taxonomy( ['user'], 'union', 'unions', 'Union', 'Unions', false );
	}

	/**
	 * Add Unions to User menu
	 *
	 * @return void
	 */
	public function add_union_to_user_menu() {
		Rise_Factory::add_taxonomy_to_user_menu( __( 'Unions', 'rise' ), __( 'Unions', 'rise' ), 'union' );
	}

	/**
	 * Add Unions field to user profile
	 *
	 * @param  WP_User $user
	 * @return void
	 */
	public function add_union_to_user_profile( $user ) {
		self::user_profile_taxonomy_term_checkboxes( $user, 'union', 'Unions' );
	}

	/**
	 * Save Unions on user profile update
	 *
	 * @param  int    $user_id
	 * @return void
	 */
	public function save_union_on_user_profile( $user_id ) {
		Rise_Factory::save_taxonomy_terms_on_user_profile( $user_id, 'union' );
	}

	/**
	 * Registers the `location` taxonomy,
	 * for use with 'user'.
	 *
	 * @return void
	 */
	public function location_init() {
		Rise_Factory::register_taxonomy( ['user'], 'location', 'locations', 'Location', 'Locations', false );
	}

	/**
	 * Add Locations to User menu
	 *
	 * @return void
	 */
	public function add_location_to_user_menu() {
		Rise_Factory::add_taxonomy_to_user_menu( __( 'Locations', 'rise' ), __( 'Locations', 'rise' ), 'location' );
	}

	/**
	 * Add Locations field to user profile
	 *
	 * @param  WP_User $user
	 * @return void
	 */
	public function add_location_to_user_profile( $user ) {
		self::user_profile_taxonomy_term_checkboxes( $user, 'location', 'Locations' );
	}

	/**
	 * Save Locations on user profile update
	 *
	 * @param  int    $user_id
	 * @return void
	 */
	public function save_location_on_user_profile( $user_id ) {
		Rise_Factory::save_taxonomy_terms_on_user_profile( $user_id, 'location' );
	}
	/**
	 * Registers the `experience_level` taxonomy,
	 * for use with 'user'.
	 *
	 * @return void
	 */
	public function experience_level_init() {
		Rise_Factory::register_taxonomy( ['user'], 'experience_level', 'experience_levels', 'Experience Level', 'Experience Levels', false );
	}

	/**
	 * Add Experience Levels to User menu
	 *
	 * @return void
	 */
	public function add_experience_level_to_user_menu() {
		Rise_Factory::add_taxonomy_to_user_menu( __( 'Experience Levels', 'rise' ), __( 'Experience Levels', 'rise' ), 'experience_level' );
	}

	/**
	 * Add Experience Levels field to user profile
	 *
	 * @param  WP_User $user
	 * @return void
	 */
	public function add_experience_level_to_user_profile( $user ) {
		self::user_profile_taxonomy_term_checkboxes( $user, 'experience_level', 'Experience Levels' );
	}

	/**
	 * Save Experience Levels on user profile update
	 *
	 * @param  int    $user_id
	 * @return void
	 */
	public function save_experience_level_on_user_profile( $user_id ) {
		Rise_Factory::save_taxonomy_terms_on_user_profile( $user_id, 'experience_level' );
	}

	/**
	 * Registers the `partner_directory` taxonomy,
	 * for use with 'user'.
	 */
	public function partner_directory_init() {
		Rise_Factory::register_taxonomy( ['user'], 'partner_directory', 'partner_directories', 'Partner Directory', 'Partner Directories' );
	}

	/**
	 * Add Partner Directory to User menu
	 *
	 * @return void
	 */
	public function add_partner_directory_to_user_menu() {
		Rise_Factory::add_taxonomy_to_user_menu( __( 'Partner Directories', 'rise' ), __( 'Partner Directory', 'rise' ), 'partner_directory' );
	}

	/**
	 * Add Partner Directory field to user profile
	 *
	 * @param  WP_User $user
	 * @return void
	 */
	public function add_partner_directory_to_user_profile( $user ) {
		self::user_profile_taxonomy_term_checkboxes( $user, 'partner_directory', 'Partner Directory' );
	}

	/**
	 * Save Partner Directory on user profile update
	 *
	 * @param  int    $user_id
	 * @return void
	 */
	public function save_partner_directory_on_user_profile( $user_id ) {
		Rise_Factory::save_taxonomy_terms_on_user_profile( $user_id, 'partner_directory' );
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
		$terms = wp_get_object_terms( $user->ID, $taxonomy );

		$selected_terms = $terms ? wp_list_pluck( $terms, 'term_id' ) : [];

		$all_terms = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			]
		);

		wp_nonce_field( 'save_' . $taxonomy, $taxonomy . '_nonce' );
		?>

		<!-- Add a new section to the user profile edit screen for the given taxonomy -->
		<h2><?php esc_html_e( $name, 'rise' ); ?></h2>
		<table class="form-table">
			<tr>
				<!-- Add a field for the taxonomy checkboxes -->
				<th><label><?php esc_html_e( 'Select ' . $name, 'rise' ); ?></label></th>
				<td>
					<?php foreach ( $all_terms as $term ) : ?>
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
}
