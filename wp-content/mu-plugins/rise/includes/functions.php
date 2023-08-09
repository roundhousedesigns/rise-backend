<?php
/**
 * Functions.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.2.0
 */

/**
 * Update a credit's display index.
 *
 * // TODO Maybe move this to a class method on Rise_Credit?
 *
 * @param  int $credit_id The credit's ID.
 * @param  int $index     The credit's new display index.
 * @return int The credit's ID.
 */
function rise_update_credit_index( $credit_id, $index ) {
	// Get the user's pod.
	$pod = pods( 'credit', $credit_id );

	// Update the credit's pod.
	$update_fields = [
		'index' => $index,
	];

	// TODO investigate error handling (does $pod->save() return 0 on failure?)
	return $pod->save( $update_fields );
}

/**
 * Filters out user profiles which don't have either a first or last name, or which have no contact information.
 *
 * Used to prevent incomplete profiles from appearing in search.
 *
 * @param  int     $author_id
 * @return boolean The result of the filter operation.
 */
function rise_remove_incomplete_profiles_from_search( $author_id ) {
	$meta = get_user_meta( $author_id );
	if ( !$meta['first_name'][0] && !$meta['last_name'][0] ) {
		return false;
	}

	$pod = pods( 'user', $author_id );

	// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
	// If email, phone, and website are all unset, ignore this user.
	// TODO determine if we actually want this search results check.
	// if ( !$pod->field( 'contact_email', true, true ) && !$pod->field( 'phone', true, true ) && !$pod->field( 'website_url', true, true ) ) {
	// 	return false;
	// }

	return true;
}

/**
 * Gets a newly uploaded file's attachment ID.
 *
 * @param  string $url The URL of the file.
 * @return int    The attachment ID.
 */
function rise_get_attachment_id_by_url( $url ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$file             = [];
	$file['name']     = basename( $url );
	$file['tmp_name'] = download_url( $url, 300, true );
	$file['error']    = '';
	$file['size']     = filesize( $file['tmp_name'] );

	$id = media_handle_sideload( $file, 0 );

	if ( is_wp_error( $id ) ) {
		unlink( $file['tmp_name'] );
		throw new WP_Error( 'attachment_processing_error', $id->get_error_message() );
	}

	return $id;
}

/**
 * Retrieve the user IDs for users with the given terms.
 *
 * @param  array $terms           The terms to query users for. Keys are taxonomies, values are arrays of term IDs.
 * @param  array $include_authors An array of user IDs to include in the query.
 * @return int[] The user IDs.
 */
function rise_query_users_with_terms( $terms, $include_authors = [] ) {
	$authors = $include_authors;

	if ( !$terms ) {
		shuffle( $authors );
		return $authors;
	}

	// Get the object IDs for the terms in the taxonomies
	$user_ids = [];
	foreach ( $terms as $taxonomy => $term_ids ) {
		$user_ids = array_merge( $user_ids, get_objects_in_term( $term_ids, $taxonomy ) );
	}

	// Filter out IDs from the $user_ids array that are not also in the $authors array
	if ( !empty( $authors ) ) {
		$user_ids = array_intersect( $user_ids, $authors );
	}

	// Remove duplicates from the object IDs array
	$user_ids = array_unique( $user_ids );

	if ( !$user_ids ) {
		return [];
	}

	// Query users based on the object IDs
	$args = [
		'include' => $user_ids,
		'role'    => 'crew-member',
	];

	// Retrieve users based on all of our querying and filtration.
	$users = get_users( $args );

	return wp_list_pluck( $users, 'ID' );
}

/**
 * Retrieve the user IDs for users with the given terms. Assumes unique slugs.
 *
 * @deprecated 1.0.8
 * @since 1.0.0
 *
 * @param  string    $slug   The user slug.
 * @param  bool      $single Whether to return a single result or an array of results (default: false).
 * @return array|int The user IDs, or a single user ID if $single is true.
 */
function get_rise_profile( $user_id ) {
	$pod        = pods( 'user', $user_id );
	$all_fields = $pod->export();

	if ( !$all_fields ) {
		return false;
	}

	$profile = [];

	foreach ( $all_fields as $field => $value ) {
		$profile[$field] = pods_serial_comma( $value, $field, $pod->fields );
	}

	return $profile;
}

/**
 * Get the site name.
 *
 * @source wp-graphql/src/Mutation/SendPasswordResetEmail.php Original source
 * @since 1.0.0
 *
 * @return string
 */
function rise_get_email_friendly_site_name() {
	if ( is_multisite() ) {
		$network = get_network();
		if ( isset( $network->site_name ) ) {
			return $network->site_name;
		}
	}

	/*
			* The blogname option is escaped with esc_html on the way into the database
			* in sanitize_option we want to reverse this for the plain text arena of emails.
		*/

	return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
}

/**
 * Determines if a user has at least one credit and at least a first or last name set.
 *
 * @deprecated 1.0.8
 * @since 1.0.0
 *
 * @param  int     $user_id
 * @return boolean True if the user is searchable, false otherwise.
 */
function user_is_searchable( $user_id ) {
	// TODO verify and implement this function
	$usermeta = get_user_meta( $user_id );

	// check if user is the author of at least one item of the `credit` post type
	$credit_query = new WP_Query( [
		'post_type'      => 'credit',
		'posts_per_page' => 1,
		'author'         => $user_id,
	] );

	// check if user has a first or last name set
	$first_name = isset( $usermeta['first_name'][0] ) ? $usermeta['first_name'][0] : '';
	$last_name  = isset( $usermeta['last_name'][0] ) ? $usermeta['last_name'][0] : '';

	return $credit_query->have_posts() && ( $first_name || $last_name );
}

/**
 * Query crew members based on the given arguments.
 *
 * Used by the frontend search filters, and by reporting functions.
 *
 * @param  array $args
 * @return int[] The user IDs.
 */
function rise_search_and_filter_crew_members( $args ) {
	// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$credit_filters = [
		'position' => isset( $args['positions'] ) ? $args['positions'] : '',
		'skill'    => isset( $args['skills'] ) ? $args['skills'] : '',
	];

	$user_filters = [
		'union'             => isset( $args['unions'] ) ? $args['unions'] : '',
		'location'          => isset( $args['locations'] ) ? $args['locations'] : '',
		'experience_level'  => isset( $args['experienceLevels'] ) ? $args['experienceLevels'] : '',
		'gender_identity'   => isset( $args['genderIdentities'] ) ? $args['genderIdentities'] : '',
		'personal_identity' => isset( $args['personalIdentities'] ) ? $args['personalIdentities'] : '',
		'racial_identity'   => isset( $args['racialIdentities'] ) ? $args['racialIdentities'] : '',
	];

	// Start building the Credit query args.
	$credit_args = [
		'post_type'      => 'credit',
		'tax_query'      => ['relation' => 'AND'],
		'posts_per_page' => -1, // TODO replace with pagination.
		'orderby'        => 'rand',
	];

	foreach ( $credit_filters as $taxonomy => $terms ) {
		if ( !empty( $terms ) ) {
			$credit_args['tax_query'][] = [
				'taxonomy'         => $taxonomy,
				'field'            => 'term_id',
				'terms'            => $terms,
				'include_children' => true,
			];
		}
	}

	// Query credits with the desired attributes.
	$credits = get_posts( $credit_args );

	// If no credits are found, return an empty array.
	if ( empty( $credits ) ) {
		return [];
	}

	// Collect the credit authors.
	$authors = [];
	foreach ( $credits as $credit ) {
		$authors[] = $credit->post_author;
	}

	// Filter out authors with no name set, or no contact info.
	$authors = array_filter( array_unique( $authors ), 'rise_remove_incomplete_profiles_from_search' );

	// Filter users by selected taxonomies.
	$user_taxonomy_terms = [];
	foreach ( $user_filters as $tax => $term_ids ) {
		if ( empty( $term_ids ) ) {
			continue;
		}

		$user_taxonomy_terms[$tax] = $term_ids;
	}

	// Filter users by taxonomy
	$filtered_authors = rise_query_users_with_terms( $user_taxonomy_terms, $authors );

	return $filtered_authors;

}
// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_tax_query

/**
 * Safe redirect with nocache headers to prevent caching of the redirect. Don't forget to call `exit`
 * after calling this function to prevent further execution.
 *
 * @since  1.0.4
 *
 * @param  string $location The path to redirect to.
 * @param  int    $status   The HTTP status code to use (default: 302)
 * @return void
 */
function rise_nocache_redirect( $location, $status = 302 ) {
	nocache_headers();
	wp_safe_redirect( esc_url_raw( $location ), $status, 'RISE' );
}

/**
 * Generate user slugs for all users with the 'crew-member' role.
 *
 * @since 1.0.4
 *
 * @return void
 */
function rise_generate_user_slugs() {
	// Get all users with the 'crew-member' role, with no limit.
	$users = get_users( [
		'role' => 'crew-member',
	] );

	foreach ( $users as $user ) {
		wp_update_user( [
			'ID'            => $user->ID,
			'user_nicename' => Rise_Users::generate_default_user_slug( $user->ID ),
		] );
	}
}

/**
 * Get all users with a given taxonomy term.
 *
 * @uses rise_search_and_filter_crew_members()
 *
 * @since 1.0.6
 *
 * @param  string $taxonomy_arg The taxonomy argument for use in rise_search_and_filter_crew_members().
 * @param  array  $term_ids     The term IDs to search for.
 * @return void
 */
function rise_get_all_users_with_taxonomy_terms( $taxonomy_arg, $term_ids ) {
	$user_ids = rise_search_and_filter_crew_members( [$taxonomy_arg => $term_ids] );
	$users    = get_users( ['include' => $user_ids] );

	$csv[] = 'Name,Email';

	foreach ( $users as $user ) {
		// get user's first and last names
		$usermeta   = get_user_meta( $user->ID );
		$first_name = isset( $usermeta['first_name'][0] ) ? $usermeta['first_name'][0] : '';
		$last_name  = isset( $usermeta['last_name'][0] ) ? $usermeta['last_name'][0] : '';

		$csv[] = sprintf( '"%s %s",%s', esc_textarea( $first_name ), esc_textarea( $last_name ), esc_html( $user->user_email ) );
	}

	printf( '<pre>%s</pre>', esc_textarea( implode( "\n", $csv ) ) );

	echo '<br /><br />';

	printf( 'Total: %d', count( $users ) );
}

/**
 * Pluck user IDs from a list of profiles, as retrieved from pods()->field().
 *
 * @param  array $profiles The profiles to pluck IDs from.
 * @return array The profile IDs.
 */
function rise_pluck_profile_ids( $profiles ) {
	if ( empty( $profiles ) ) {
		return [];
	}

	return array_map( function ( $profile ) {
		return absint( $profile['ID'] );
	}, $profiles );
}
