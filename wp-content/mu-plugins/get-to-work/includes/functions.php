<?php
/**
 * Functions.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.2.0
 */

/**
 * Retrieve the user IDs for users with the given terms.
 *
 * @param  array $terms           The terms to query users for. Keys are taxonomies, values are arrays of term IDs.
 * @param  array $include_authors An array of user IDs to include in the query.
 * @return int[] The user IDs.
 */
function query_users_with_terms( $terms, $include_authors = [] ) {
	// Get the object IDs for the terms in the taxonomies
	$object_ids = [];
	foreach ( $terms as $taxonomy => $term_ids ) {
		$object_ids = array_merge( $include_authors, get_objects_in_term( $term_ids, $taxonomy ) );
	}

	// Remove duplicates from the object IDs array
	$object_ids = array_unique( $object_ids );

	// Query users based on the object IDs
	$args = [
		'include' => $object_ids,
		'orderby' => 'include',
	];

	$users = get_users( $args );

	return wp_list_pluck( $users, 'ID' );
}
