<?php

namespace RHD\Rise\Includes;

/**
 * Registers user taxonomies.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Taxonomies {

	public function __construct() {
		// No initialization needed
	}

	/**
	 * Register a post type.
	 *
	 * @param  string $post_type        The post type slug.
	 * @param  string $post_type_plural The plural post type slug (for GraphQL).
	 * @param  string $singular         The singular post type name.
	 * @param  string $plural           The plural post type name.
	 * @param  string $icon             (default: '') https://developer.wordpress.org/resource/dashicons/
	 * @param  array  $args_override    (default: []) A custom post type args array. Overwrites defaults if present.
	 * @return void
	 */
	public static function register_post_type( $post_type, $post_type_plural, $singular, $plural, $icon = '', $args_override = [] ) {
		// Equalize names.
		$name_singular = ucwords( $singular );
		$name_plural   = ucwords( $plural );

		$args = [
			'labels'                => [
				'name'                  => __( $name_plural, 'rise' ),
				'singular_name'         => __( $name_singular, 'rise' ),
				'all_items'             => __( 'All ' . strtolower( $name_plural ), 'rise' ),
				'archives'              => __( $name_singular . ' Archives', 'rise' ),
				'attributes'            => __( $name_singular . ' Attributes', 'rise' ),
				'insert_into_item'      => __( 'Insert into ', 'rise' ),
				'uploaded_to_this_item' => __( 'Uploaded to this ' . strtolower( $name_singular ), 'rise' ),
				'featured_image'        => _x( 'Featured Image', $post_type, 'rise' ),
				'set_featured_image'    => _x( 'Set featured image', $post_type, 'rise' ),
				'remove_featured_image' => _x( 'Remove featured image', $post_type, 'rise' ),
				'use_featured_image'    => _x( 'Use as featured image', $post_type, 'rise' ),
				'filter_items_list'     => __( 'Filter ' . $name_plural . ' list', 'rise' ),
				'items_list_navigation' => __( $name_plural . ' list navigation', 'rise' ),
				'items_list'            => __( $name_plural . ' list', 'rise' ),
				'new_item'              => __( 'New ' . $name_singular, 'rise' ),
				'add_new'               => __( 'Add New', 'rise' ),
				'add_new_item'          => __( 'Add New ' . $name_singular, 'rise' ),
				'edit_item'             => __( 'Edit ' . $name_singular, 'rise' ),
				'view_item'             => __( 'View ' . $name_singular, 'rise' ),
				'view_items'            => __( 'View ' . $name_plural, 'rise' ),
				'search_items'          => __( 'Search ' . strtolower( $name_plural ), 'rise' ),
				'not_found'             => __( 'No ' . strtolower( $name_plural ) . ' found', 'rise' ),
				'not_found_in_trash'    => __( 'No ' . strtolower( $name_plural ) . ' found in trash', 'rise' ),
				'parent_item_colon'     => __( 'Parent ' . $name_singular . ':', 'rise' ),
				'menu_name'             => __( $name_plural, 'rise' ),
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
			'menu_position'         => 2,
			'menu_icon'             => $icon,
			'show_in_graphql'       => true,
			'graphql_single_name'   => $post_type,
			'graphql_plural_name'   => $post_type_plural,
			'capability_type'       => [$post_type, $post_type_plural],
			'show_in_rest'          => true,
			'rest_base'             => $post_type,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		];

		// Merge $args with $args_override. $args_override overwrites $args if present.
		$args = array_merge( $args, $args_override );

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
	 * @param  string       $rewrite_slug    The slug to use for the rewrite.
	 * @return void
	 */
	public static function register_taxonomy( $object_type, $taxonomy, $taxonomy_plural, $singular, $plural, $hierarchical = false, $rewrite_slug = null ) {
		$args = [
			'hierarchical'          => $hierarchical,
			'public'                => true,
			'show_in_nav_menus'     => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'rewrite'               => $rewrite_slug ? ['slug' => $rewrite_slug] : true,
			'labels'                => [
				'name'                       => __( $plural, 'rise' ),
				'singular_name'              => _x( $singular, 'taxonomy general name', 'rise' ),
				'search_items'               => __( 'Search ' . $plural, 'rise' ),
				'popular_items'              => __( 'Popular ' . $plural, 'rise' ),
				'all_items'                  => __( 'All ' . $plural, 'rise' ),
				'parent_item'                => __( 'Parent ' . $singular, 'rise' ),
				'parent_item_colon'          => __( 'Parent ' . $singular . ':', 'rise' ),
				'edit_item'                  => __( 'Edit ' . $singular, 'rise' ),
				'update_item'                => __( 'Update ' . $singular, 'rise' ),
				'view_item'                  => __( 'View ' . $singular, 'rise' ),
				'add_new_item'               => __( 'Add New ' . $singular, 'rise' ),
				'new_item_name'              => __( 'New ' . $singular, 'rise' ),
				'separate_items_with_commas' => __( 'Separate ' . $plural . ' with commas', 'rise' ),
				'add_or_remove_items'        => __( 'Add or remove ' . $plural, 'rise' ),
				'choose_from_most_used'      => __( 'Choose from the most used ' . $plural, 'rise' ),
				'not_found'                  => __( 'No ' . $plural . ' found.', 'rise' ),
				'no_terms'                   => __( 'No ' . $plural, 'rise' ),
				'menu_name'                  => __( $plural, 'rise' ),
				'items_list_navigation'      => __( $plural . ' list navigation', 'rise' ),
				'items_list'                 => __( $plural . ' list', 'rise' ),
				'most_used'                  => _x( 'Most Used', $taxonomy, 'rise' ),
				'back_to_items'              => __( '&larr; Back to ' . $plural, 'rise' ),
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
		$date          = is_a( $post, 'WP_Post' ) ? date_i18n( __( 'M j, Y @ G:i', 'rise' ), strtotime( $post->post_date ) ) : '';

		$messages[$post_type] = [
			// Unused. Messages start at index 1.
			0  => '',
			/* translators: %s: post permalink */
			1  => sprintf( __( $title . ' updated. <a target="_blank" href="%s">View ' . $name_singular . '</a>', 'rise' ), esc_url( $permalink ) ),
			2  => __( 'Custom field updated.', 'rise' ),
			3  => __( 'Custom field deleted.', 'rise' ),
			4  => __( $title . ' updated.', 'rise' ),

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ? sprintf( __( $title . ' restored to revision from %s', 'rise' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s: post permalink */
			6  => sprintf( __( $title . ' published. <a href="%s">View ' . $name_singular . '</a>', 'rise' ), esc_url( $permalink ) ),
			7  => __( $title . ' saved.', 'rise' ),
			/* translators: %s: post permalink */
			8  => sprintf( __( $title . ' submitted. <a target="_blank" href="%s">Preview ' . $name_singular . '</a>', 'rise' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			9  => sprintf( __( $title . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $name_singular . '</a>', 'rise' ), $date, esc_url( $permalink ) ),
			/* translators: %s: post permalink */
			10 => sprintf( __( $title . ' draft updated. <a target="_blank" href="%s">Preview ' . $name_singular . '</a>', 'rise' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
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
			'updated'   => _n( '%s ' . $name_singular . ' updated.', '%s ' . $name_plural . ' updated.', $bulk_counts['updated'], 'rise' ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 ' . $name_singular . ' not updated, somebody is editing it.', 'rise' ) :
			/* translators: %s: Number of items */
			_n( '%s ' . $name_singular . ' not updated, somebody is editing it.', '%s ' . $name_plural . ' not updated, somebody is editing them.', $bulk_counts['locked'], 'rise' ),
			/* translators: %s: Number of items */
			'deleted'   => _n( '%s ' . $name_singular . ' permanently deleted.', '%s ' . $name_plural . ' permanently deleted.', $bulk_counts['deleted'], 'rise' ),
			/* translators: %s: Number of items */
			'trashed'   => _n( '%s ' . $name_singular . ' moved to the Trash.', '%s ' . $name_plural . ' moved to the Trash.', $bulk_counts['trashed'], 'rise' ),
			/* translators: %s: Number of items */
			'untrashed' => _n( '%s ' . $name_singular . ' restored from the Trash.', '%s ' . $name_plural . ' restored from the Trash.', $bulk_counts['untrashed'], 'rise' ),
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
			1 => __( $name_singular . ' added.', 'rise' ),
			2 => __( $name_singular . ' deleted.', 'rise' ),
			3 => __( $name_singular . ' updated.', 'rise' ),
			4 => __( $name_singular . ' not added.', 'rise' ),
			5 => __( $name_singular . ' not updated.', 'rise' ),
			6 => __( $name_plural . ' deleted.', 'rise' ),
		];

		return $messages;
	}

	/**
	 * Add the taxonomy to the user menu.
	 *
	 * @return void
	 */
	public static function add_taxonomy_to_user_menu( $page_title, $menu_title, $menu_slug ) {
		add_users_page(
			$page_title,
			$menu_title,
			'edit_users',
			'edit-tags.php?taxonomy=' . $menu_slug
		);
	}

	/**
	 * Save the taxonomy terms on the user profile.
	 *
	 * @param  int    $user_id  The user ID
	 * @param  string $taxonomy The taxonomy slug
	 * @return void
	 */
	// TODO Maybe move this outside this file.
	public static function save_taxonomy_terms_on_user_profile( $user_id, $taxonomy ) {
		if ( !current_user_can( 'edit_user', $user_id ) && !current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( !isset( $_POST[$taxonomy . '_nonce'] ) || !wp_verify_nonce( wp_unslash( $_POST[$taxonomy . '_nonce'] ), 'save_' . $taxonomy ) ) {
			return;
		}

		if ( isset( $_POST[$taxonomy] ) ) {
			$terms = array_map( 'intval', (array) $_POST[$taxonomy] );
			wp_set_object_terms( $user_id, $terms, $taxonomy );
			return;
		}

		wp_set_object_terms( $user_id, [], $taxonomy );
	}

	public function add_role_taxonomy() {}
}