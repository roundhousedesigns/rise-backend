<?php
/**
 * Utilities.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.0.4
 */

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
 * @uses search_and_filter_crew_members()
 *
 * @since 1.0.6
 *
 * @param  string $taxonomy_arg The taxonomy argument for use in search_and_filter_crew_members().
 * @param  array  $term_ids     The term IDs to search for.
 * @return void
 */
function rise_get_all_users_with_taxonomy_terms( $taxonomy_arg, $term_ids ) {
	$user_ids = search_and_filter_crew_members( [$taxonomy_arg => $term_ids] );
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
