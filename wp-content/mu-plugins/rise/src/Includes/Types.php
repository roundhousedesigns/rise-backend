<?php

namespace RHD\Rise\Includes;

/**
 * Registers custom post types.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Types {

	/**
	 * Registers the `credit` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 *
	 * @return void
	 */
	public function credit_init() {
		Taxonomies::register_post_type(
			'credit',
			'credits',
			'Credit',
			'Credits',
			'dashicons-star-half',
			[
				'supports'            => ['title', 'author', 'editor'],
				'public'              => true,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
				'taxonomies'          => ['position', 'skill'],
			]
		);
	}

	/**
	 * Sets the post updated messages for the `credit` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `credit` post type.
	 */
	public function credit_updated_messages( $messages ) {
		return Taxonomies::post_type_updated_messages( 'credit', 'credit', $messages );
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
		return Taxonomies::post_type_updated_messages( 'credit', 'credit', $bulk_messages, $bulk_counts );
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
			$credit_count = \count_user_posts( $user_id, 'credit' );

			if ( $credit_count > 0 ) {
				$edit_url = \add_query_arg( [
					'post_type' => 'credit',
					'author'    => $user_id,
				], \admin_url( 'edit.php' ) );
				return '<a href="' . \esc_url( $edit_url ) . '">' . $credit_count . '</a>';
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
		Taxonomies::register_taxonomy( ['credit', 'job_post'], 'position', 'positions', 'Production Position', 'Production Positions', true );
	}

	/**
	 * Sets the post updated messages for the `position` taxonomy.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `position` taxonomy.
	 */
	public function position_updated_messages( $messages ) {
		return Taxonomies::taxonomy_updated_messages( 'position', 'Production Position', 'Production Positions', $messages );
	}

	/**
	 * Registers the `skill` taxonomy,
	 * for use with 'credit'.
	 */
	public function skill_init() {
		return Taxonomies::register_taxonomy( ['credit', 'job_post'], 'skill', 'skills', 'Skill', 'Skills', false );
	}

	/**
	 * Sets the post updated messages for the `skill` taxonomy.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `skill` taxonomy.
	 */
	public function skill_updated_messages( $messages ) {
		return Taxonomies::taxonomy_updated_messages( 'skill', 'Skill', 'Skills', $messages );
	}

	/**
	 * Registers the `user_notice` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function user_notice_init() {
		Taxonomies::register_post_type(
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
		return Taxonomies::post_type_updated_messages( 'user_notice', 'user_notice', $messages );
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
		return Taxonomies::post_type_bulk_updated_messages( 'user_notice', 'User Notice', 'User Notices', $bulk_messages, $bulk_counts );
	}

	/**
	 * Registers the `saved_search` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function saved_search_init() {
		Taxonomies::register_post_type(
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
		return Taxonomies::post_type_updated_messages( 'saved_search', 'saved_search', $messages );
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
		return Taxonomies::post_type_bulk_updated_messages( 'saved_search', 'Saved Search', 'Saved Searches', $bulk_messages, $bulk_counts );
	}

	/**
	 * Disable the Block Editor for the `saved_search` post type.
	 *
	 * @param  string    $current_status
	 * @param  string    $post_type
	 * @return boolean
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
		if ( \get_post_type() === 'saved_search' ) {
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
		Taxonomies::register_post_type(
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
		return Taxonomies::post_type_updated_messages( 'conflict_range', 'conflict_range', $messages );
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
		return Taxonomies::post_type_bulk_updated_messages( 'conflict_range', 'Conflict Date Range', 'Conflict Date Ranges', $bulk_messages, $bulk_counts );
	}

	/**
	 * Disable the Block Editor for the `conflict_range` post type.
	 *
	 * @param  string    $current_status
	 * @param  string    $post_type
	 * @return boolean
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
		if ( \get_post_type() === 'conflict_range' ) {
			return false;
		}

		return $default;
	}

	/**
	 * Registers the `job_post` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function job_post_init() {
		Taxonomies::register_post_type(
			'job_post',
			'job_posts',
			'Job Post',
			'Job Posts',
			'dashicons-businessman',
			[
				'supports'           => ['title', 'author'],
				'public'             => true,
				'show_ui'            => true,
				'publicly_queryable' => true,
				'capability_type'    => 'post',
				'taxonomies'         => ['position', 'skill'],
			]
		);
	}

	/**
	 * Sets the post updated messages for the `job_post` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `job_post` post type.
	 */
	public function job_post_updated_messages( $messages ) {
		return Taxonomies::post_type_updated_messages( 'job_post', 'Job Post', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `job_post` post type.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `job_post` post type.
	 */
	public function job_post_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Taxonomies::post_type_bulk_updated_messages( 'job_post', 'Job Post', 'Job Posts', $bulk_messages, $bulk_counts );
	}

	/**
	 * Set the job post's expiration date on publication.
	 *
	 * @param  string $new_status The new status of the job post.
	 * @param  string $old_status The old status of the job post.
	 * @param  object $post       The job post object.
	 * @return void
	 */
	function set_job_post_expiration_on_publication( $new_status, $old_status, $post ) {
		if ( 'job_post' !== $post->post_type ) {
			return;
		}

		$default_expiration_length = 30;
		$expiration_length         = \get_option( 'rise_settings_job_post_expiration' );
		$length_string             = '+' . ( $expiration_length ? $expiration_length : $default_expiration_length ) . ' days';

		if ( 'pending' === $old_status && 'publish' === $new_status ) {
			$pod             = \pods( 'job_post', $post->ID );
			$expiration_date = \date( 'Y-m-d', \strtotime( $length_string ) );

			$pod->save( ['expiration_date' => $expiration_date] );
		}
	}

	/**
	 * Registers the `network_partner` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function network_partner_init() {
		Taxonomies::register_post_type(
			'network_partner',
			'network_partners',
			'Network Partner',
			'Network Partners',
			'dashicons-buddicons-friends',
			[
				'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
				'public'             => true,
				'publicly_queryable' => true,
				'has_archive'        => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => true,
				'capability_type'    => 'post',
				'taxonomies'         => ['network_partner_tag'],
				'rewrite'            => ['slug' => 'partner'],
			]
		);
	}

	/**
	 * Sets the post updated messages for the `network_partner` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `network_partner` post type.
	 */
	public function network_partner_updated_messages( $messages ) {
		return Taxonomies::post_type_updated_messages( 'network_partner', 'network_partner', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `network_partner` post type.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `network_partner` post type.
	 */
	public function network_partner_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Taxonomies::post_type_bulk_updated_messages( 'network_partner', 'Network Partner', 'Network Partners', $bulk_messages, $bulk_counts );
	}

	/**
	 * Disable the Block Editor for the `network_partner` post type.
	 *
	 * @param  string    $current_status
	 * @param  string    $post_type
	 * @return boolean
	 */
	public function network_partner_disable_block_editor( $current_status, $post_type ) {
		if ( 'network_partner' === $post_type ) {
			return false;
		}
		return $current_status;
	}

	/**
	 * Registers the `network_partner_tag` taxonomy,
	 * for use with 'network_partner'.
	 */
	public function network_partner_tag_init() {
		Taxonomies::register_taxonomy(
			['network_partner'],
			'network_partner_tag',
			'network_partner_tags',
			'Network Partner Tag',
			'Network Partner Tags',
			true
		);
	}

	/**
	 * Sets the term updated messages for the `network_partner_tag` taxonomy.
	 *
	 * @param  array $messages Term updated messages.
	 * @return array Messages for the `network_partner_tag` taxonomy.
	 */
	public function network_partner_tag_updated_messages( $messages ) {
		return Taxonomies::taxonomy_updated_messages(
			'network_partner_tag',
			'Network Partner Tag',
			'Network Partner Tags',
			$messages
		);
	}

	/**
	 * Registers the `profile_notification` post type.
	 *
	 * @access    private
	 * @since     0.1.0
	 */
	public function profile_notification_init() {
		Taxonomies::register_post_type(
			'profile_notification',
			'profile_notifications',
			'Profile Notification',
			'Profile Notifications',
			'dashicons-bell',
			[
				'supports'            => ['author', 'title'],
				'public'              => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_ui'             => true, // TODO: turn UI off after development
				'capability_type'     => 'post',
				'show_in_rest'        => true,
			]
		);
	}

	/**
	 * Sets the post updated messages for the `profile_notification` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `profile_notification` post type.
	 */
	public function profile_notification_updated_messages( $messages ) {
		return Taxonomies::post_type_updated_messages( 'profile_notification', 'Profile Notification', $messages );
	}

	/**
	 * Sets the bulk post updated messages for the `profile_notification` post type.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `profile_notification` post type.
	 */
	public function profile_notification_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		return Taxonomies::post_type_bulk_updated_messages( 'profile_notification', 'Profile Notification', 'Profile Notifications', $bulk_messages, $bulk_counts );
	}

	/**
	 * Create notifications for users who have starred the updated profile.
	 *
	 * @param  int    $user_id The user ID being updated.
	 * @return void
	 */
	public function create_notification_for_profile_starred_by( $user_id ) {
		global $wpdb;

		// Get the field_id for 'starred_profiles' in the user pod
		$pods_api = \pods_api();
		$field    = $pods_api->load_field( [
			'pod'  => 'user',
			'name' => 'starred_profiles',
		] );

		if ( !$field || !$field->get_id() ) {
			return;
		}

		$field_id = $field->get_id();

		// Query the podsrel table for all users who have $user_id in their starred_profiles
		$rel_table = $wpdb->prefix . 'podsrel';
		$sql       = $wpdb->prepare(
			"SELECT DISTINCT t.item_id
			 FROM $rel_table t
			 WHERE t.field_id = %d
			   AND t.related_item_id = %d",
			$field_id,
			$user_id
		);

		$user_ids = $wpdb->get_col( $sql );

		if ( empty( $user_ids ) ) {
			return;
		}

		$notification_pod = \pods( 'profile_notification' );
		if ( !$notification_pod ) {
			return;
		}

		foreach ( $user_ids as $current_user_id ) {
			// Don't notify the user about their own update
			if ( $current_user_id == $user_id ) {
				continue;
			}

			$notification_id = $notification_pod->add( [
				'notification_type' => 'starred_profile_updated',
				'value'             => $user_id,
				'author'            => $current_user_id,
			] );

			if ( $notification_id ) {
				$user = \get_user_by( 'id', $user_id );

				if ( $user ) {
					$notification_title = \sprintf( \__( '%s updated their profile.', 'rise' ), $user->display_name );
					\wp_update_post( [
						'ID'          => $notification_id,
						'post_status' => 'publish',
						'post_title'  => $notification_title,
					] );
				}
			}
		}
	}

	/**
	 * Delete duplicate profile notifications after a new one is created.
	 * Only notifications with exactly matching titles will be considered duplicates.
	 * The most recent notification will be kept.
	 *
	 * @param  int    $post_id The ID of the newly created post
	 * @return void
	 */
	public function delete_duplicate_profile_notifications( $post_id ) {
		// Verify this is a profile_notification post
		if ( \get_post_type( $post_id ) !== 'profile_notification' ) {
			return;
		}

		// Get the new notification's title
		$new_notification = \get_post( $post_id );
		if ( !$new_notification || !$new_notification->post_title ) {
			return;
		}

		$title  = $new_notification->post_title;
		$author = $new_notification->post_author;

		// Find any other notifications with matching title for this author
		$args = [
			'post_type'      => 'profile_notification',
			'post_status'    => 'publish',
			'title'          => $title,
			'author'         => $author,
			'post__not_in'   => [$post_id],
			'fields'         => 'ids',
			'posts_per_page' => -1,
		];

		$duplicate_notifications = \get_posts( $args );

		// Delete any duplicates found
		foreach ( $duplicate_notifications as $duplicate_id ) {
			\wp_delete_post( $duplicate_id, true );
		}
	}
}
