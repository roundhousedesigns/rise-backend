<?php
/**
 * Registers custom post types.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Rise_Types {
	/**
	 * Registers the `credit` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function credit_init() {
		Rise_Taxonomies::register_post_type( 'credit', 'credits', 'Credit', 'Credits', 'dashicons-star-half' );
	}

	/**
	 * Sets the post updated messages for the `credit` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `credit` post type.
	 */
	public function credit_updated_messages( $messages ) {
		return Rise_Taxonomies::post_type_updated_messages( 'credit', 'credit', $messages );
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
		return Rise_Taxonomies::post_type_updated_messages( 'credit', 'credit', $bulk_messages, $bulk_counts );
	}

	/**
	 * Registers the `position` taxonomy,
	 * for use with 'credit'.
	 */
	public function position_init() {
		Rise_Taxonomies::register_taxonomy( ['credit'], 'position', 'positions', 'Production Position', 'Production Positions', true );
	}

	/**
	 * Sets the post updated messages for the `position` taxonomy.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `position` taxonomy.
	 */
	public function position_updated_messages( $messages ) {
		return Rise_Taxonomies::taxonomy_updated_messages( 'position', 'Production Position', 'Production Positions', $messages );
	}

	/**
	 * Registers the `skill` taxonomy,
	 * for use with 'credit'.
	 */
	public function skill_init() {
		return Rise_Taxonomies::register_taxonomy( ['credit'], 'skill', 'skills', 'Skill', 'Skills', false );
	}

	/**
	 * Sets the post updated messages for the `skill` taxonomy.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `skill` taxonomy.
	 */
	public function skill_updated_messages( $messages ) {
		return Rise_Taxonomies::taxonomy_updated_messages( 'skill', 'Skill', 'Skills', $messages );
	}

	/**
	 * Registers the `project` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function project_init() {
		Rise_Taxonomies::register_post_type( 'project', 'projects', 'Project', 'Projects', 'dashicons-portfolio' );
	}

	/**
	 * Sets the post updated messages for the `project` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `project` post type.
	 */
	public function project_updated_messages( $messages ) {
		return Rise_Taxonomies::post_type_updated_messages( 'project', 'project', $messages );
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
		Rise_Taxonomies::post_type_bulk_updated_messages( 'project', 'Project', 'Projects', $bulk_messages, $bulk_counts );
	}

	/**
	 * Registers the `saved_search` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function saved_search_init() {
		Rise_Taxonomies::register_post_type( 'saved_search', 'saved_searches', 'Saved Search', 'Saved Searches', 'dashicons-search' );
	}

	/**
	 * Sets the post updated messages for the `saved_search` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `saved_search` post type.
	 */
	public function saved_search_updated_messages( $messages ) {
		return Rise_Taxonomies::post_type_updated_messages( 'saved_search', 'saved_search', $messages );
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
		return Rise_Taxonomies::post_type_bulk_updated_messages( 'saved_search', 'Saved Search', 'Saved Searches', $bulk_messages, $bulk_counts );
	}
}
