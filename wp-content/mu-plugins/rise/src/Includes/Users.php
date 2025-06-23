<?php

	namespace RHD\Rise\Includes;

	/**
	 * The file that defines the custom user roles and capabilities.
	 *
	 * @package    RHD\Rise
	 * @subpackage RHD\Rise\Includes
	 *
	 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
	 *
	 * @since      0.1.0
	 */
	class Users {
		/**
		 * Add user roles with capabilities.
		 *
		 * @return void
		 */
		public function add_roles() {
			$caps = [
				'read'                            => true,
				'list_users'                      => true,
				'unfiltered_upload'               => true,
				'upload_files'                    => true,
				'edit_files'                      => true,
				'read_credits'                    => true,
				'publish_credits'                 => true,
				'edit_credits'                    => true,
				'edit_published_credits'          => true,
				'delete_published_credits'        => true,
				'read_saved_searches'             => true,
				'publish_saved_searches'          => true,
				'edit_saved_searches'             => true,
				'edit_published_saved_searches'   => true,
				'delete_published_saved_searches' => true,
			];

			$role = \get_role( 'crew-member' );

			if ( null === $role ) {
				\add_role( 'crew-member', 'Crew Member', $caps );
				$role = \get_role( 'crew-member' );
			}

			if ( $role ) {
				/**
				 * Credits
				 */
				$role->add_cap( 'read_credits' );
				$role->add_cap( 'publish_credits' );
				$role->add_cap( 'edit_credits' );
				$role->add_cap( 'edit_published_credits' );
				$role->add_cap( 'delete_credits' );
				$role->add_cap( 'delete_published_credits' );

				/**
				 * Saved Searches
				 */
				$role->add_cap( 'read_saved_searches' );
				$role->add_cap( 'publish_saved_searches' );
				$role->add_cap( 'edit_saved_searches' );
				$role->add_cap( 'edit_published_saved_searches' );
				$role->add_cap( 'delete_saved_searches' );
				$role->add_cap( 'delete_published_saved_searches' );
			}
		}

		/**
		 * Blocks access to the Dashboard for users with the 'crew-member' role.
		 *
		 * @return void
		 */
		public function redirect_crew_members_from_dashboard() {
			// Check if the user is logged in and has the 'crew-member' role
			if ( \is_user_logged_in() && \current_user_can( 'crew-member' ) ) {
				// Get the current screen object
				$current_screen = \get_current_screen();

				// Check if the user is on the dashboard
				if ( 'dashboard' === $current_screen->base || 'admin' === $current_screen->base ) {
					\wp_safe_redirect( 'https://work.risetheatre.org' );
					\wp_die();
				}
			}
		}

		/**
		 * Removes the admin bar for Crew Members.
		 *
		 * @return void
		 */
		public function remove_admin_bar_for_crew_members() {
			if ( \current_user_can( 'crew-member' ) ) {
				\show_admin_bar( false );
			}
		}

		/**
		 * Adds custom image sizes.
		 *
		 * @return void
		 */
		public function add_image_sizes() {
			\add_image_size( 'cover', 2400, 0, false );
			\add_image_size( 'partner-logo', 250, 250, true );
		}

		/**
		 * Registers the `gender_identity` taxonomy,
		 * for use with 'user'.
		 */
		public function gender_identity_init() {
			Taxonomies::register_taxonomy( ['user'], 'gender_identity', 'gender_identities', 'Gender Identity', 'Gender Identities' );
		}

		/**
		 * Add Gender Identity to User menu
		 *
		 * @return void
		 */
		public function add_gender_identity_to_user_menu() {
			Taxonomies::add_taxonomy_to_user_menu( \__( 'Gender Identity', 'rise' ), \__( 'Gender Identity', 'rise' ), 'gender_identity' );
		}

		/**
		 * Add Gender Identity field to user profile
		 *
		 * @param  \WP_User $user
		 * @return void
		 */
		public function add_gender_identity_to_user_profile( $user ) {
			$this->user_profile_taxonomy_term_checkboxes( $user, 'gender_identity', 'Gender Identity' );
		}

		/**
		 * Save Gender Identity on user profile update
		 *
		 * @param  int    $user_id
		 * @return void
		 */
		public function save_gender_identity_on_user_profile( $user_id ) {
			Taxonomies::save_taxonomy_terms_on_user_profile( $user_id, 'gender_identity' );
		}

		/**
		 * Registers the `racial_identity` taxonomy,
		 * for use with 'user'.
		 */
		public function racial_identity_init() {
			Taxonomies::register_taxonomy( ['user'], 'racial_identity', 'racial_identities', 'Racial Identity', 'Racial Identities' );
		}

		/**
		 * Add Racial Identity to User menu
		 *
		 * @return void
		 */
		public function add_racial_identity_to_user_menu() {
			Taxonomies::add_taxonomy_to_user_menu( \__( 'Racial Identity', 'rise' ), \__( 'Racial Identity', 'rise' ), 'racial_identity' );
		}

		/**
		 * Add Racial Identity field to user profile
		 *
		 * @param  \WP_User $user
		 * @return void
		 */
		public function add_racial_identity_to_user_profile( $user ) {
			$this->user_profile_taxonomy_term_checkboxes( $user, 'racial_identity', 'Racial Identity' );
		}

		/**
		 * Save Racial Identity on user profile update
		 *
		 * @param  int    $user_id
		 * @return void
		 */
		public function save_racial_identity_on_user_profile( $user_id ) {
			Taxonomies::save_taxonomy_terms_on_user_profile( $user_id, 'racial_identity' );
		}

		/**
		 * Registers the `personal_identity` taxonomy,
		 * for use with 'user'.
		 *
		 * @return void
		 */
		public function personal_identity_init() {
			Taxonomies::register_taxonomy( ['user'], 'personal_identity', 'personal_identities', 'Personal Identity', 'Personal Identities' );
		}

		/**
		 * Add Personal Identity to User menu
		 *
		 * @return void
		 */
		public function add_personal_identity_to_user_menu() {
			Taxonomies::add_taxonomy_to_user_menu( \__( 'Personal Identity', 'rise' ), \__( 'Personal Identity', 'rise' ), 'personal_identity' );
		}

		/**
		 * Add Personal Identity field to user profile
		 *
		 * @param  \WP_User $user
		 * @return void
		 */
		public function add_personal_identity_to_user_profile( $user ) {
			$this->user_profile_taxonomy_term_checkboxes( $user, 'personal_identity', 'Personal Identity' );
		}

		/**
		 * Save Personal Identity on user profile update
		 *
		 * @param  int    $user_id
		 * @return void
		 */
		public function save_personal_identity_on_user_profile( $user_id ) {
			Taxonomies::save_taxonomy_terms_on_user_profile( $user_id, 'personal_identity' );
		}

		/**
		 * Registers the `union` taxonomy,
		 * for use with 'user'.
		 */
		public function union_init() {
			Taxonomies::register_taxonomy( ['user'], 'union', 'unions', 'Union', 'Unions', false );
		}

		/**
		 * Add Unions to User menu
		 *
		 * @return void
		 */
		public function add_union_to_user_menu() {
			Taxonomies::add_taxonomy_to_user_menu( \__( 'Unions', 'rise' ), \__( 'Unions', 'rise' ), 'union' );
		}

		/**
		 * Add Unions field to user profile
		 *
		 * @param  \WP_User $user
		 * @return void
		 */
		public function add_union_to_user_profile( $user ) {
			$this->user_profile_taxonomy_term_checkboxes( $user, 'union', 'Unions' );
		}

		/**
		 * Save Union on user profile update
		 *
		 * @param  int    $user_id
		 * @return void
		 */
		public function save_union_on_user_profile( $user_id ) {
			Taxonomies::save_taxonomy_terms_on_user_profile( $user_id, 'union' );
		}

		/**
		 * Registers the `location` taxonomy,
		 * for use with 'user'.
		 */
		public function location_init() {
			Taxonomies::register_taxonomy( ['user'], 'location', 'locations', 'Location', 'Locations', false );
		}

		/**
		 * Add Locations to User menu
		 *
		 * @return void
		 */
		public function add_location_to_user_menu() {
			Taxonomies::add_taxonomy_to_user_menu( \__( 'Locations', 'rise' ), \__( 'Locations', 'rise' ), 'location' );
		}

		/**
		 * Add Locations field to user profile
		 *
		 * @param  \WP_User $user
		 * @return void
		 */
		public function add_location_to_user_profile( $user ) {
			$this->user_profile_taxonomy_term_checkboxes( $user, 'location', 'Locations' );
		}

		/**
		 * Save Location on user profile update
		 *
		 * @param  int    $user_id
		 * @return void
		 */
		public function save_location_on_user_profile( $user_id ) {
			Taxonomies::save_taxonomy_terms_on_user_profile( $user_id, 'location' );
		}

		/**
		 * Registers the `experience_level` taxonomy,
		 * for use with 'user'.
		 *
		 * @return void
		 */
		public function experience_level_init() {
			Taxonomies::register_taxonomy( ['user'], 'experience_level', 'experience_levels', 'Experience Level', 'Experience Levels', false );
		}

		/**
		 * Add Experience Levels to User menu
		 *
		 * @return void
		 */
		public function add_experience_level_to_user_menu() {
			Taxonomies::add_taxonomy_to_user_menu( \__( 'Experience Levels', 'rise' ), \__( 'Experience Levels', 'rise' ), 'experience_level' );
		}

		/**
		 * Add Experience Levels field to user profile
		 *
		 * @param  \WP_User $user
		 * @return void
		 */
		public function add_experience_level_to_user_profile( $user ) {
			$this->user_profile_taxonomy_term_checkboxes( $user, 'experience_level', 'Experience Levels' );
		}

		/**
		 * Save Experience Levels on user profile update
		 *
		 * @param  int    $user_id
		 * @return void
		 */
		public function save_experience_level_on_user_profile( $user_id ) {
			Taxonomies::save_taxonomy_terms_on_user_profile( $user_id, 'experience_level' );
		}

		/**
		 * Registers the `partner_directory` taxonomy,
		 * for use with 'user'.
		 */
		public function partner_directory_init() {
			Taxonomies::register_taxonomy( ['user'], 'partner_directory', 'partner_directories', 'Partner Directory', 'Partner Directories' );
		}

		/**
		 * Add Partner Directory to User menu
		 *
		 * @return void
		 */
		public function add_partner_directory_to_user_menu() {
			Taxonomies::add_taxonomy_to_user_menu( \__( 'Partner Directories', 'rise' ), \__( 'Partner Directory', 'rise' ), 'partner_directory' );
		}

		/**
		 * Add Partner Directory field to user profile
		 *
		 * @param  \WP_User $user
		 * @return void
		 */
		public function add_partner_directory_to_user_profile( $user ) {
			$this->user_profile_taxonomy_term_checkboxes( $user, 'partner_directory', 'Partner Directory' );
		}

		/**
		 * Save Partner Directory on user profile update
		 *
		 * @param  int    $user_id
		 * @return void
		 */
		public function save_partner_directory_on_user_profile( $user_id ) {
			Taxonomies::save_taxonomy_terms_on_user_profile( $user_id, 'partner_directory' );
		}

		/**
		 * Generates a checkbox list of terms for a given taxonomy.
		 *
		 * @param  \WP_User $user
		 * @param  string   $taxonomy
		 * @param  string   $name
		 * @return void
		 */
		private function user_profile_taxonomy_term_checkboxes( $user, $taxonomy, $name ) {
			// Get the currently selected terms for the user
			$terms = \wp_get_object_terms( $user->ID, $taxonomy );

			$selected_terms = $terms ? \wp_list_pluck( $terms, 'term_id' ) : [];

			$all_terms = \get_terms(
				[
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				]
			);

			\wp_nonce_field( 'save_' . $taxonomy, $taxonomy . '_nonce' );

			// TODO move this HTML to a template file.
		?>

		<!-- Add a new section to the user profile edit screen for the given taxonomy -->
		<h2><?php \__( $name, 'rise' ); ?></h2>
		<table class="form-table">
			<tr>
				<!-- Add a field for the taxonomy checkboxes -->
				<th><label><?php \__( 'Select ' . $name, 'rise' ); ?></label></th>
				<td>
					<?php foreach ( $all_terms as $term ): ?>
						<label>
							<input type="checkbox" name="<?php \printf( '%s', \esc_attr( $taxonomy ) ); ?>[]" value="<?php \printf( '%s', \esc_attr( $term->term_id ) ); ?>"<?php \checked( in_array( $term->term_id, $selected_terms, true ), true ); ?>>
							<?php \printf( '%s', \esc_html( $term->name ) ); ?>
						</label>
						<br>
					<?php endforeach; ?>
				</td>
			</tr>
		</table>

		<?php
			}

				/**
				 * Save a search to the user's search history.
				 *
				 * @param  int             $user_id       The user ID.
				 * @param  array           $search_params The search parameters.
				 * @return array|\WP_Error The updated search history or WP_Error if there was an error.
				 */
				public static function save_user_search_history( $user_id, $search_params ) {
					// Get user pod
					$pod = \pods( 'user', $user_id );

					// Get search_history pod field and convert to an array
					$search_history = json_decode( $pod->field( 'search_history' ) );

					// If search_history is empty, set it to an empty array
					if ( !$search_history ) {
						$search_history = [];
					}

					// If not already in the search_history array, add search params to the front of the search_history array.
					if ( !in_array( $search_params, $search_history, true ) ) {
						array_unshift( $search_history, $search_params );
					}

					// Ensure a limit of 5 items.
					if ( count( $search_history ) > 5 ) {
						array_pop( $search_history );
					}

					// Update the user pod with the new search_history
					$result = $pod->save( 'search_history', \wp_json_encode( $search_history ) );

					return $result ? $search_history : new \WP_Error( 'failed_to_save_search_history', 'Failed to save search history.' );
				}

				/**
				 * Save or update a saved search entry.
				 *
				 * @param  int           $user_id         The user ID.
				 * @param  array         $search_params   The search parameters.
				 * @param  string        $new_search_name The new saved search name (title)
				 * @param  int           $saved_search_id (default: 0) The saved search ID.
				 * @return int|\WP_Error The post ID on success, or WP_Error on failure.
				 */
				public static function update_saved_search( $user_id, $search_params, $new_search_name, $saved_search_id = 0 ) {
					$params = [
						'ID'           => $saved_search_id,
						'post_title'   => $new_search_name,
						'post_type'    => 'saved_search',
						'post_status'  => 'publish',
						'post_author'  => $user_id,
						'post_content' => \wp_json_encode( $search_params ),
					];

					return \wp_insert_post( $params, true, true );
				}

				/**
				 * Updates the conflict range for a user.
				 *
				 * @param  int            $user_id           The ID of the user.
				 * @param  string         $startDate         The start date of the conflict range.
				 * @param  string         $endDate           The end date of the conflict range.
				 * @param  int            $conflict_range_id The ID of the conflict range to update.
				 * @throws \WP_Error      When there is an error updating the date range.
				 * @return int|false|null The ID of the conflict range on success, false on failure, or null if there was an issue with the Pod itself.
				 */
				public static function update_conflict_range( $user_id, $startDate, $endDate, $conflict_range_id = 0 ) {
					$post_id = $conflict_range_id;

					if ( 0 === $post_id ) {
						$params = [
							'ID'          => $conflict_range_id,
							'post_type'   => 'conflict_range',
							'post_status' => 'publish',
							'post_author' => $user_id,
						];

						$post_id = \wp_insert_post( $params, true, true );
					}

					if ( \is_wp_error( $post_id ) ) {
						return new \WP_Error( 'update_conflict_range', 'There was an error updating the date range.' );
					}

					$pod = \pods( 'conflict_range', $post_id );

					return $pod->save( [
						'start_date' => $startDate,
						'end_date'   => $endDate,
					] );
				}

				/**
				 * Retrieve the user IDs for users with the given terms.
				 *
				 * @param  array    $terms           Array of terms ID arrays to query users for, keyed by taxonomy ID.
				 * @param  string[] $include_authors An array of user IDs to include in the query.
				 * @return int[]    The user IDs.
				 */
				public static function query_users( $terms, $authors = [] ) {
					if ( !$terms ) {
						$users    = self::get_public_profile_users( $authors );
						$user_ids = \wp_list_pluck( $users, 'ID' );
						shuffle( $user_ids );

						return $user_ids;
					}

					// Get the object IDs for the terms in the taxonomies
					$user_ids = [];
					foreach ( $terms as $taxonomy => $term_ids ) {
						// Get users with the selected terms
						$users_in_term = get_objects_in_term( $term_ids, $taxonomy );

						// If $user_ids is empty, set it to $users_in_term.
						// Otherwise, strip any users from $user_ids that are not also in $users_in_term.
						if ( !$user_ids ) {
							$user_ids = $users_in_term;
							continue;
						}

						$user_ids = array_intersect( $user_ids, $users_in_term );
					}

					// Filter out IDs from the $user_ids array that are not also in the $authors array
					if ( !empty( $authors ) ) {
						$user_ids = array_intersect( $user_ids, $authors );
					}

					// Remove users who are not in the 'crew-member' role
					$user_ids = array_filter( $user_ids, function ( $user_id ) {
						$user = get_userdata( $user_id );

						if ( !$user ) {
							return false;
						}

						return in_array( 'crew-member', $user->roles, true );
					} );

					// Remove duplicates from the object IDs array
					$user_ids = array_unique( $user_ids );

					if ( !$user_ids ) {
						return [];
					}

					// Remove any users with the 'disable_profile' pod meta set to true
					$users = self::get_public_profile_users( $user_ids );

					return wp_list_pluck( $users, 'ID' );
				}

				/**
				 * Retrieves public profile users based on the given user IDs.
				 *
				 * @param  int[]     $user_ids An array of user IDs.
				 * @return WP_User[] An array of user objects with public profiles.
				 */
				public static function get_public_profile_users( $user_ids ) {
					// Query users based on the object IDs
					$args = [
						'include' => $user_ids,
						'role'    => 'crew-member',
					];

					$users = get_users( $args );

					$filtered = array_filter( $users, function ( $user ) {
						$pod = pods( 'user', $user->ID );

						return $pod->field( 'disable_profile' ) !== '1' ? true : false;
					} );

					return $filtered;
				}

				/**
				 * Generates a default user slug based on the user's first and last name.
				 *
				 * @since 1.1.10
				 *
				 * @param  string $first_name The user's first name.
				 * @param  string $last_name  The user's last name.
				 * @return string The sanitized title of the user's full name.
				 */
				public static function generate_default_user_slug( $first_name = '', $last_name = '' ) {
					// Generate the full name based on the user's first and last name.
					return \sanitize_title( $first_name . ' ' . $last_name );
				}

				/**
				 * Pluck user IDs from a list of profiles, as retrieved from pods()->field().
				 *
				 * @param  array $profiles The profiles to pluck IDs from.
				 * @return array The profile IDs.
				 */
				public static function pluck_profile_ids( $profiles ) {
					if ( empty( $profiles ) ) {
						return [];
					}

					return \array_map( function ( $profile ) {
						return \absint( $profile['ID'] );
					}, $profiles );
				}

				/**
				 * Delete a post item authored by the current user.
				 *
				 * @param  int     $id      The post ID.
				 * @param  int     $user_id The requesting user's ID.
				 * @return boolean True if the post was deleted, false otherwise.
				 */
				public static function delete_own_allowed_post_item( $id, $user_id ) {
					$allowed_post_types = ['credit', 'saved_search', 'conflict_range'];

					$post_type = \get_post_type( $id );
					$author_id = \get_post_field( 'post_author', $id );

					if ( $author_id !== $user_id ) {
						return false;
					}

					if ( !\in_array( $post_type, $allowed_post_types, true ) ) {
						return false;
					}

					$result = \wp_delete_post( $id, false );

					if ( $result instanceof \WP_Post ) {
						return true;
					}

					return false;
				}

		}
