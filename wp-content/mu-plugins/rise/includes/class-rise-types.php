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
	 *
	 * @return void
	 */
	public function credit_init() {
		Rise_Taxonomies::register_post_type( 'credit', 'credits', 'Credit', 'Credits', 'dashicons-star-half',
			[
				'supports'            => ['title', 'author', 'editor'],
				'public'              => true,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
			] );
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
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `credit` post type.
	 */
	public function credit_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Rise_Taxonomies::post_type_updated_messages( 'credit', 'credit', $bulk_messages, $bulk_counts );
	}

	/**
	 * Add custom `credit` column to Users list.
	 *
	 * @since 1.0.5
	 *
	 * @param  array $columns The existing columns.
	 * @return array The modified columns.
	 */
	public function add_credit_posts_column( $columns ) {
		$columns['credit_posts'] = 'Credit Posts';
		return $columns;
	}

	/**
	 * Populate custom `credit` Users column with data.
	 *
	 * @param  string $value       The existing column value.
	 * @param  string $column_name The column name.
	 * @param  int    $user_id     The user ID.
	 * @return string The modified column value.
	 */
	public function display_credit_posts_column( $value, $column_name, $user_id ) {
		if ( 'credit_posts' === $column_name ) {
			$credit_count = count_user_posts( $user_id, 'credit' );

			if ( $credit_count > 0 ) {
				$edit_url = add_query_arg( [
					'post_type' => 'credit',
					'author'    => $user_id,
				], admin_url( 'edit.php' ) );
				return '<a href="' . esc_url( $edit_url ) . '">' . $credit_count . '</a>';
			}

			return '0';
		}

		return $value;
	}

	/**
	 * Make custom `credit` Users column sortable.
	 *
	 * @param  array $columns The existing columns.
	 * @return array The modified columns.
	 */
	public function make_credit_posts_column_sortable( $columns ) {
		$columns['credit_posts'] = 'credit_posts';
		return $columns;
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
	 * NOT IMPLEMENTED.
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
	 * NOT IMPLEMENTED.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `project` post type.
	 */
	public function project_updated_messages( $messages ) {
		return Rise_Taxonomies::post_type_updated_messages( 'project', 'project', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `project` post type.
	 * Keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 *
	 * NOT IMPLEMENTED.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `project` post type.
	 */
	public function project_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		Rise_Taxonomies::post_type_bulk_updated_messages( 'project', 'Project', 'Projects', $bulk_messages, $bulk_counts );
	}

	/**
	 * Registers the `user_notice` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function user_notice_init() {
		Rise_Taxonomies::register_post_type(
			'user_notice',
			'user_notices',
			'Dashboard Notice',
			'Dashboard Notices',
			'dashicons-flag',
			[
				'supports'            => ['title', 'author', 'editor'],
				'public'              => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'capability_type'     => 'post',
			]
		);
	}

	/**
	 * Sets the post updated messages for the `user_notice` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `user_notice` post type.
	 */
	public function user_notice_updated_messages( $messages ) {
		return Rise_Taxonomies::post_type_updated_messages( 'user_notice', 'user_notice', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `user_notice` post type.
	 *
	 * Keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `user_notice` post type.
	 */
	public function user_notice_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Rise_Taxonomies::post_type_bulk_updated_messages( 'user_notice', 'User Notice', 'User Notices', $bulk_messages, $bulk_counts );
	}

	/**
	 * Registers the `saved_search` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function saved_search_init() {
		Rise_Taxonomies::register_post_type(
			'saved_search',
			'saved_searches',
			'Saved Search',
			'Saved Searches',
			'dashicons-search',
			[
				'public'              => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'supports'            => ['title', 'author', 'editor'],
				'show_ui'             => false,
			]
		);
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

	/**
	 * Disable the Block Editor for the `saved_search` post type.
	 *
	 * @param  string $current_status
	 * @param  string $post_type
	 * @return void
	 */
	public function saved_search_disable_block_editor( $current_status, $post_type ) {
		if ( 'saved_search' === $post_type ) {
			return false;
		}

		return $current_status;
	}

	/**
	 * Disable the WYSIWYG Editor for the `saved_search` post type.
	 *
	 * @param  boolean $default
	 * @return boolean True to enable the editor, false to disable.
	 */
	public function saved_search_remove_visual_editor( $default ) {
		if ( get_post_type() === 'saved_search' ) {
			return false;
		}

		return $default;
	}

	/**
	 * Registers the `conflict_range` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function conflict_range_init() {
		Rise_Taxonomies::register_post_type(
			'conflict_range',
			'conflict_ranges',
			'Conflict Date Range',
			'Conflict Date Ranges',
			'dashicons-calendar',
			[
				'public'              => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'supports'            => ['author'],
				'show_ui'             => true,
			]
		);
	}

	/**
	 * Sets the post updated messages for the `conflict_range` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `conflict_range` post type.
	 */
	public function conflict_range_updated_messages( $messages ) {
		return Rise_Taxonomies::post_type_updated_messages( 'conflict_range', 'conflict_range', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `conflict_range` post type.
	 *
	 * Keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `conflict_range` post type.
	 */
	public function conflict_range_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Rise_Taxonomies::post_type_bulk_updated_messages( 'conflict_range', 'Conflict Date Range', 'Conflict Date Ranges', $bulk_messages, $bulk_counts );
	}

	/**
	 * Disable the Block Editor for the `conflict_range` post type.
	 *
	 * @param  string $current_status
	 * @param  string $post_type
	 * @return void
	 */
	public function conflict_range_disable_block_editor( $current_status, $post_type ) {
		if ( 'conflict_range' === $post_type ) {
			return false;
		}

		return $current_status;
	}

	/**
	 * Disable the WYSIWYG Editor for the `conflict_range` post type.
	 *
	 * @param  boolean $default
	 * @return boolean True to enable the editor, false to disable.
	 */
	public function conflict_range_remove_visual_editor( $default ) {
		if ( get_post_type() === 'conflict_range' ) {
			return false;
		}

		return $default;
	}

	/**
	 * Registers the `job` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function job_init() {
		Rise_Taxonomies::register_post_type(
			'job',
			'jobs',
			'Job',
			'Jobs',
			'dashicons-businessman',
			[
				'supports'           => ['title', 'editor', 'author'],
				'public'             => true,
				'show_ui'            => true,
				'publicly_queryable' => true,
				'capability_type'    => 'post',
			]
		);
	}

	/**
	 * Sets the post updated messages for the `job` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `job` post type.
	 */
	public function job_updated_messages( $messages ) {
		return Rise_Taxonomies::post_type_updated_messages( 'job', 'Job', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `job` post type.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `job` post type.
	 */
	public function job_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Rise_Taxonomies::post_type_bulk_updated_messages( 'job', 'Job', 'Jobs', $bulk_messages, $bulk_counts );
	}
}
