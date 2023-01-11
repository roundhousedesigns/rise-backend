<?php
/**
 * Registers custom post types and taxonomies.
 *
 * @since      0.1.0
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 */

class Get_To_Work_Data {
	/**
	 * Registers the `credit` post type.
	 *
	 * @since     0.1.0
	 * @access    private
	 */
	public function credit_init() {
		register_post_type(
			'credit',
			[
				'labels'                => [
					'name'                  => __( 'Credits', 'gtw' ),
					'singular_name'         => __( 'Credit', 'gtw' ),
					'all_items'             => __( 'All Credits', 'gtw' ),
					'archives'              => __( 'Credit Archives', 'gtw' ),
					'attributes'            => __( 'Credit Attributes', 'gtw' ),
					'insert_into_item'      => __( 'Insert into credit', 'gtw' ),
					'uploaded_to_this_item' => __( 'Uploaded to this credit', 'gtw' ),
					'featured_image'        => _x( 'Featured Image', 'credit', 'gtw' ),
					'set_featured_image'    => _x( 'Set featured image', 'credit', 'gtw' ),
					'remove_featured_image' => _x( 'Remove featured image', 'credit', 'gtw' ),
					'use_featured_image'    => _x( 'Use as featured image', 'credit', 'gtw' ),
					'filter_items_list'     => __( 'Filter credits list', 'gtw' ),
					'items_list_navigation' => __( 'Credits list navigation', 'gtw' ),
					'items_list'            => __( 'Credits list', 'gtw' ),
					'new_item'              => __( 'New Credit', 'gtw' ),
					'add_new'               => __( 'Add New', 'gtw' ),
					'add_new_item'          => __( 'Add New Credit', 'gtw' ),
					'edit_item'             => __( 'Edit Credit', 'gtw' ),
					'view_item'             => __( 'View Credit', 'gtw' ),
					'view_items'            => __( 'View Credits', 'gtw' ),
					'search_items'          => __( 'Search credits', 'gtw' ),
					'not_found'             => __( 'No credits found', 'gtw' ),
					'not_found_in_trash'    => __( 'No credits found in trash', 'gtw' ),
					'parent_item_colon'     => __( 'Parent Credit:', 'gtw' ),
					'menu_name'             => __( 'Credits', 'gtw' ),
				],
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_nav_menus'     => true,
				'supports'              => ['title', 'author'],
				'has_archive'           => true,
				'rewrite'               => true,
				'query_var'             => true,
				'menu_position'         => null,
				'menu_icon'             => 'dashicons-star-half',
				'show_in_rest'          => true,
				'rest_base'             => 'credit',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
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
		global $post;

		$permalink = get_permalink( $post );

		$messages['credit'] = [
			// Unused. Messages start at index 1.
			0  => '',
			/* translators: %s: post permalink */
			1  => sprintf( __( 'Credit updated. <a target="_blank" href="%s">View credit</a>', 'gtw' ), esc_url( $permalink ) ),
			2  => __( 'Custom field updated.', 'gtw' ),
			3  => __( 'Custom field deleted.', 'gtw' ),
			4  => __( 'Credit updated.', 'gtw' ),
			/* translators: %s: date and time of the revision */
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Credit restored to revision from %s', 'gtw' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s: post permalink */
			6  => sprintf( __( 'Credit published. <a href="%s">View credit</a>', 'gtw' ), esc_url( $permalink ) ),
			7  => __( 'Credit saved.', 'gtw' ),
			/* translators: %s: post permalink */
			8  => sprintf( __( 'Credit submitted. <a target="_blank" href="%s">Preview credit</a>', 'gtw' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			9  => sprintf( __( 'Credit scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview credit</a>', 'gtw' ), date_i18n( __( 'M j, Y @ G:i', 'gtw' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			/* translators: %s: post permalink */
			10 => sprintf( __( 'Credit draft updated. <a target="_blank" href="%s">Preview credit</a>', 'gtw' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		];

		return $messages;
	}

	/**
	 * Sets the bulk post updated messages for the `credit` post type.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `credit` post type.
	 */
	public function credit_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		global $post;

		$bulk_messages['credit'] = [
			/* translators: %s: Number of credits. */
			'updated'   => _n( '%s credit updated.', '%s credits updated.', $bulk_counts['updated'], 'gtw' ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 credit not updated, somebody is editing it.', 'gtw' ) :
			/* translators: %s: Number of credits. */
			_n( '%s credit not updated, somebody is editing it.', '%s credits not updated, somebody is editing them.', $bulk_counts['locked'], 'gtw' ),
			/* translators: %s: Number of credits. */
			'deleted'   => _n( '%s credit permanently deleted.', '%s credits permanently deleted.', $bulk_counts['deleted'], 'gtw' ),
			/* translators: %s: Number of credits. */
			'trashed'   => _n( '%s credit moved to the Trash.', '%s credits moved to the Trash.', $bulk_counts['trashed'], 'gtw' ),
			/* translators: %s: Number of credits. */
			'untrashed' => _n( '%s credit restored from the Trash.', '%s credits restored from the Trash.', $bulk_counts['untrashed'], 'gtw' ),
		];

		return $bulk_messages;
	}

	/**
	 * Registers the `department` taxonomy,
	 * for use with 'credit'.
	 */
	public function department_init() {
		register_taxonomy( 'department', ['credit'], [
			'hierarchical'          => true,
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
				'name'                       => __( 'Production Departments', 'gtw' ),
				'singular_name'              => _x( 'Production Department', 'taxonomy general name', 'gtw' ),
				'search_items'               => __( 'Search Production Departments', 'gtw' ),
				'popular_items'              => __( 'Popular Production Departments', 'gtw' ),
				'all_items'                  => __( 'All Production Departments', 'gtw' ),
				'parent_item'                => __( 'Parent Production Department', 'gtw' ),
				'parent_item_colon'          => __( 'Parent Production Department:', 'gtw' ),
				'edit_item'                  => __( 'Edit Production Department', 'gtw' ),
				'update_item'                => __( 'Update Production Department', 'gtw' ),
				'view_item'                  => __( 'View Production Department', 'gtw' ),
				'add_new_item'               => __( 'Add New Production Department', 'gtw' ),
				'new_item_name'              => __( 'New Production Department', 'gtw' ),
				'separate_items_with_commas' => __( 'Separate Production Departments with commas', 'gtw' ),
				'add_or_remove_items'        => __( 'Add or remove Production Departments', 'gtw' ),
				'choose_from_most_used'      => __( 'Choose from the most used Production Departments', 'gtw' ),
				'not_found'                  => __( 'No Production Departments found.', 'gtw' ),
				'no_terms'                   => __( 'No Production Departments', 'gtw' ),
				'menu_name'                  => __( 'Production Departments', 'gtw' ),
				'items_list_navigation'      => __( 'Production Departments list navigation', 'gtw' ),
				'items_list'                 => __( 'Production Departments list', 'gtw' ),
				'most_used'                  => _x( 'Most Used', 'department', 'gtw' ),
				'back_to_items'              => __( '&larr; Back to Production Departments', 'gtw' ),
			],
			'show_in_rest'          => true,
			'rest_base'             => 'department',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		] );
	}

	/**
	 * Sets the post updated messages for the `department` taxonomy.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `department` taxonomy.
	 */
	public function department_updated_messages( $messages ) {
		$messages['department'] = [
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Production Department added.', 'gtw' ),
			2 => __( 'Production Department deleted.', 'gtw' ),
			3 => __( 'Production Department updated.', 'gtw' ),
			4 => __( 'Production Department not added.', 'gtw' ),
			5 => __( 'Production Department not updated.', 'gtw' ),
			6 => __( 'Production Departments deleted.', 'gtw' ),
		];

		return $messages;
	}
}
