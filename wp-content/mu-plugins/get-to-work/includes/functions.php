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
		$object_ids = get_objects_in_term( $term_ids, $taxonomy );
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

/**
 * Update a credit's display index.
 *
 * // TODO Maybe move this to a class method on Get_To_Work_Credit?
 *
 * @param  int $credit_id The credit's ID.
 * @param  int $index     The credit's new display index.
 * @return int The credit's ID.
 */
function update_credit_index( $credit_id, $index ) {

	// Get the user's pod.
	$pod = pods( 'credit', $credit_id );

	// Update the credit's pod.
	$update_fields = [
		'index' => $index,
	];

	// TODO investigate error handling (does $pod->save() return 0 on failure?)
	return $pod->save( $update_fields );
}
