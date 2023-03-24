<?php
/**
 * Registers custom post types.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Get_To_Work_Types {
	/**
	 * Registers the `credit` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function credit_init() {
		Get_To_Work_Factory::register_post_type( 'credit', 'credits', 'Credit', 'Credits', 'dashicons-star-half' );
	}

	/**
	 * Sets the post updated messages for the `credit` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `credit` post type.
	 */
	public function credit_updated_messages( $messages ) {
		return Get_To_Work_Factory::post_type_updated_messages( 'credit', 'credit', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `credit` post type.
	 *
	 * Keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `credit` post type.
	 */
	public function credit_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Get_To_Work_Factory::post_type_updated_messages( 'credit', 'credit', $bulk_messages, $bulk_counts );
	}

	/**
	 * Registers the `position` taxonomy,
	 * for use with 'credit'.
	 */
	public function position_init() {
		Get_To_Work_Factory::register_taxonomy( ['credit'], 'position', 'positions', 'Production Position', 'Production Positions', true );
	}

	/**
	 * Sets the post updated messages for the `position` taxonomy.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `position` taxonomy.
	 */
	public function position_updated_messages( $messages ) {
		return Get_To_Work_Factory::taxonomy_updated_messages( 'position', 'Production Position', 'Production Positions', $messages );
	}

	/**
	 * Adds the `parents` (plural) argument to `position` taxonomy queries by filtering the SQL query string.
	 *
	 * @param  array $pieces     The pieces of the SQL query.
	 * @param  array $taxonomies The taxonomies being queried.
	 * @param  array $args       The arguments for the taxonomy query.
	 * @return array The filtered pieces of the SQL query.
	 */
	public function position_terms_plural_parents_query_param( $pieces, $taxonomies, $args ) {
		// Bail if we are not currently handling our specified taxonomy
		if ( ! in_array( 'position', $taxonomies, true ) ) {
			return $pieces;
		}

		// Check if our custom argument, 'parents' is set, if not, bail
		if ( ! isset( $args['parents'] )
			|| ! is_array( $args['parents'] )
		) {
			return $pieces;
		}

		// If 'parents' is set, make sure that 'parent' and 'child_of' is not set
		if ( $args['parent']
			|| $args['child_of']
		) {
			return $pieces;
		}

		// Validate the array as an array of integers
		$parents = array_map( 'intval', $args['parents'] );

		// Loop through $parents and set the WHERE clause accordingly
		$where = [];
		foreach ( $parents as $parent ) {
			// Make sure $parent is not 0, if so, skip and continue
			if ( 0 === $parent ) {
				continue;
			}

			$where[] = " tt.parent = '$parent'";
		}

		if ( ! $where ) {
			return $pieces;
		}

		$where_string = implode( ' OR ', $where );
		$pieces['where'] .= " AND ( $where_string ) ";

		return $pieces;
	}

	/**
	 * Registers the `skill` taxonomy,
	 * for use with 'credit'.
	 */
	public function skill_init() {
		return Get_To_Work_Factory::register_taxonomy( ['credit'], 'skill', 'skills', 'Skill', 'Skills', false );
	}

	/**
	 * Sets the post updated messages for the `skill` taxonomy.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `skill` taxonomy.
	 */
	public function skill_updated_messages( $messages ) {
		return Get_To_Work_Factory::taxonomy_updated_messages( 'skill', 'Skill', 'Skills', $messages );
	}

	/**
	 * Registers the `project` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function project_init() {
		Get_To_Work_Factory::register_post_type( 'project', 'projects', 'Project', 'Projects', 'dashicons-portfolio' );
	}

	/**
	 * Sets the post updated messages for the `project` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `project` post type.
	 */
	public function project_updated_messages( $messages ) {
		return Get_To_Work_Factory::post_type_updated_messages( 'project', 'project', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `project` post type.
	 *
	 * Keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `project` post type.
	 */
	public function project_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		Get_To_Work_Factory::post_type_bulk_updated_messages( 'project', 'Project', 'Projects', $bulk_messages, $bulk_counts );
	}

	/**
	 * Registers the `saved_search` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function saved_search_init() {
		Get_To_Work_Factory::register_post_type( 'saved_search', 'saved_searches', 'Saved Search', 'Saved Searches', 'dashicons-search' );
	}

	/**
	 * Sets the post updated messages for the `saved_search` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `saved_search` post type.
	 */
	public function saved_search_updated_messages( $messages ) {
		return Get_To_Work_Factory::post_type_updated_messages( 'saved_search', 'saved_search', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `saved_search` post type.
	 *
	 * Keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `saved_search` post type.
	 */
	public function saved_search_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Get_To_Work_Factory::post_type_bulk_updated_messages( 'saved_search', 'Saved Search', 'Saved Searches', $bulk_messages, $bulk_counts );
	}

	/**
	 * Blocks the user from accessing the admin area if they are not an administrator.
	 *
	 * @return void
	 */
	public function blockusers_init() {

		if ( is_admin() && ! current_user_can( 'administrator' ) &&
			! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}

	}

}
