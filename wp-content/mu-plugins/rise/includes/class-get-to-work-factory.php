<?php
/**
 * Registers user taxonomies.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Get_To_Work_Factory {
	/**
	 * Regsiter a post type.
	 *
	 * @param  string $post_type        - The post type slug.
	 * @param  string $post_type_plural - The plural post type slug.
	 * @param  string $singular         - The singular post type name.
	 * @param  string $plural           - The plural post type name.
	 * @param  string $icon             (default: '') - https://developer.wordpress.org/resource/dashicons/
	 * @return void
	 */
	public static function register_post_type( $post_type, $post_type_plural, $singular, $plural, $icon = '' ) {
		// Equalize names.
		$name_singular = ucwords( $singular );
		$name_plural   = ucwords( $plural );

		$args = [
			'labels'                => [
				'name'                  => __( $name_plural, 'gtw' ),
				'singular_name'         => __( $name_singular, 'gtw' ),
				'all_items'             => __( 'All ' . strtolower( $name_plural ), 'gtw' ),
				'archives'              => __( $name_singular . ' Archives', 'gtw' ),
				'attributes'            => __( $name_singular . ' Attributes', 'gtw' ),
				'insert_into_item'      => __( 'Insert into ', 'gtw' ),
				'uploaded_to_this_item' => __( 'Uploaded to this ' . strtolower( $name_singular ), 'gtw' ),
				'featured_image'        => _x( 'Featured Image', $post_type, 'gtw' ),
				'set_featured_image'    => _x( 'Set featured image', $post_type, 'gtw' ),
				'remove_featured_image' => _x( 'Remove featured image', $post_type, 'gtw' ),
				'use_featured_image'    => _x( 'Use as featured image', $post_type, 'gtw' ),
				'filter_items_list'     => __( 'Filter ' . $name_plural . ' list', 'gtw' ),
				'items_list_navigation' => __( $name_plural . ' list navigation', 'gtw' ),
				'items_list'            => __( $name_plural . ' list', 'gtw' ),
				'new_item'              => __( 'New ' . $name_singular, 'gtw' ),
				'add_new'               => __( 'Add New', 'gtw' ),
				'add_new_item'          => __( 'Add New ' . $name_singular, 'gtw' ),
				'edit_item'             => __( 'Edit ' . $name_singular, 'gtw' ),
				'view_item'             => __( 'View ' . $name_singular, 'gtw' ),
				'view_items'            => __( 'View ' . $name_plural, 'gtw' ),
				'search_items'          => __( 'Search ' . strtolower( $name_plural ), 'gtw' ),
				'not_found'             => __( 'No ' . strtolower( $name_plural ) . ' found', 'gtw' ),
				'not_found_in_trash'    => __( 'No ' . strtolower( $name_plural ) . ' found in trash', 'gtw' ),
				'parent_item_colon'     => __( 'Parent ' . $name_singular . ':', 'gtw' ),
				'menu_name'             => __( $name_plural, 'gtw' ),
			],
			'public'                => true,
			'publicly_queryable'    => false,
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => ['title', 'author'],
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'menu_position'         => null,
			'menu_icon'             => $icon,
			'show_in_graphql'       => true,
			'graphql_single_name'   => $post_type,
			'graphql_plural_name'   => $post_type_plural,
			'show_in_rest'          => true,
			'rest_base'             => $post_type,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		];

		register_post_type(
			$post_type,
			$args
		);
	}

	/**
	 * Register a taxonomy.
	 *
	 * @param  string       $object_type     The object type.
	 * @param  string|array $taxonomy        The taxonomy slug.
	 * @param  string       $taxonomy_plural The plural taxonomy slug (for GraphQL).
	 * @param  string       $singular        The singular name of the taxonomy.
	 * @param  string       $plural          The plural name of the taxonomy.
	 * @param  bool         $hierarchical    Whether the taxonomy is hierarchical.
	 * @return void
	 */
	public static function register_taxonomy( $object_type, $taxonomy, $taxonomy_plural, $singular, $plural, $hierarchical = false ) {
		$args = [
			'hierarchical'          => $hierarchical,
			'public'                => true,
			'show_in_nav_menus'     => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'rewrite'               => true,
			// 'capabilities'          => [
			// 	'manage_terms' => 'edit_posts',
			// 	'edit_terms'   => 'edit_posts',
			// 	'delete_terms' => 'edit_posts',
			// 	'assign_terms' => 'edit_posts',
			// ],
			'labels'                => [
				'name'                       => __( $plural, 'gtw' ),
				'singular_name'              => _x( $singular, 'taxonomy general name', 'gtw' ),
				'search_items'               => __( 'Search ' . $plural, 'gtw' ),
				'popular_items'              => __( 'Popular ' . $plural, 'gtw' ),
				'all_items'                  => __( 'All ' . $plural, 'gtw' ),
				'parent_item'                => __( 'Parent ' . $singular, 'gtw' ),
				'parent_item_colon'          => __( 'Parent ' . $singular . ':', 'gtw' ),
				'edit_item'                  => __( 'Edit ' . $singular, 'gtw' ),
				'update_item'                => __( 'Update ' . $singular, 'gtw' ),
				'view_item'                  => __( 'View ' . $singular, 'gtw' ),
				'add_new_item'               => __( 'Add New ' . $singular, 'gtw' ),
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
			$object_type,
			$args
		);
	}

	/**
	 * Sets the post updated messages for a post type.
	 *
	 * @since 0.0.1
	 *
	 * @param  string $post_type The post type.
	 * @param  string $singular  The post type name.
	 * @param  array  $messages  Post updated messages.
	 * @return array  Messages for the post type.
	 */
	public static function post_type_updated_messages( $post_type, $singular, $messages ) {
		global $post;

		$permalink     = get_permalink( $post );
		$name_singular = strtolower( $singular );
		$title         = ucwords( $singular );

		$messages[$post_type] = [
			// Unused. Messages start at index 1.
			0  => '',
			/* translators: %s: post permalink */
			1  => sprintf( __( $title . ' updated. <a target="_blank" href="%s">View ' . $name_singular . '</a>', 'gtw' ), esc_url( $permalink ) ),
			2  => __( 'Custom field updated.', 'gtw' ),
			3  => __( 'Custom field deleted.', 'gtw' ),
			4  => __( $title . ' updated.', 'gtw' ),

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ? sprintf( __( $title . ' restored to revision from %s', 'gtw' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s: post permalink */
			6  => sprintf( __( $title . ' published. <a href="%s">View ' . $name_singular . '</a>', 'gtw' ), esc_url( $permalink ) ),
			7  => __( $title . ' saved.', 'gtw' ),
			/* translators: %s: post permalink */
			8  => sprintf( __( $title . ' submitted. <a target="_blank" href="%s">Preview ' . $name_singular . '</a>', 'gtw' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			9  => sprintf( __( $title . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $name_singular . '</a>', 'gtw' ), date_i18n( __( 'M j, Y @ G:i', 'gtw' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			/* translators: %s: post permalink */
			10 => sprintf( __( $title . ' draft updated. <a target="_blank" href="%s">Preview ' . $name_singular . '</a>', 'gtw' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		];

		return $messages;
	}

	/**
	 * Sets the bulk post updated messages for a post type.
	 *
	 * Keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 *
	 * @since 0.0.1
	 *
	 * @param  string $post_type     The post type.
	 * @param  string $singular      The post type name.
	 * @param  string $plural        The post type plural name.
	 * @param  array  $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 * @param  int[]  $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array  Bulk messages for the post type.
	 */
	public static function post_type_bulk_updated_messages( $post_type, $singular, $plural, $bulk_messages, $bulk_counts ) {
		$name_singular = strtolower( $singular );
		$name_plural   = strtolower( $plural );

		$bulk_messages[$post_type] = [
			/* translators: %s: Number of items */
			'updated'   => _n( '%s ' . $name_singular . ' updated.', '%s ' . $name_plural . ' updated.', $bulk_counts['updated'], 'gtw' ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 ' . $name_singular . ' not updated, somebody is editing it.', 'gtw' ) :
			/* translators: %s: Number of items */
			_n( '%s ' . $name_singular . ' not updated, somebody is editing it.', '%s ' . $name_plural . ' not updated, somebody is editing them.', $bulk_counts['locked'], 'gtw' ),
			/* translators: %s: Number of items */
			'deleted'   => _n( '%s ' . $name_singular . ' permanently deleted.', '%s ' . $name_plural . ' permanently deleted.', $bulk_counts['deleted'], 'gtw' ),
			/* translators: %s: Number of items */
			'trashed'   => _n( '%s ' . $name_singular . ' moved to the Trash.', '%s ' . $name_plural . ' moved to the Trash.', $bulk_counts['trashed'], 'gtw' ),
			/* translators: %s: Number of items */
			'untrashed' => _n( '%s ' . $name_singular . ' restored from the Trash.', '%s ' . $name_plural . ' restored from the Trash.', $bulk_counts['untrashed'], 'gtw' ),
		];

		return $bulk_messages;
	}

	/**
	 * Sets the post updated messages for a taxonomy.
	 *
	 * @since 0.0.1
	 *
	 * @param  string $post_type The post type.
	 * @param  string $singular  The post type name.
	 * @param  string $plural    The post type plural name.
	 * @param  array  $messages  Post updated messages.
	 * @return array  Messages for the taxonomy.
	 */
	public static function taxonomy_updated_messages( $post_type, $singular, $plural, $messages ) {
		$name_singular = ucwords( $singular );
		$name_plural   = ucwords( $plural );

		$messages[$post_type] = [
			0 => '', // Unused. Messages start at index 1.
			1 => __( $name_singular . ' added.', 'gtw' ),
			2 => __( $name_singular . ' deleted.', 'gtw' ),
			3 => __( $name_singular . ' updated.', 'gtw' ),
			4 => __( $name_singular . ' not added.', 'gtw' ),
			5 => __( $name_singular . ' not updated.', 'gtw' ),
			6 => __( $name_plural . ' deleted.', 'gtw' ),
		];

		return $messages;
	}
}
