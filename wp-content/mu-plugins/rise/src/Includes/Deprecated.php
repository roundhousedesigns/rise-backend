<?php

namespace RHD\Rise\Includes;

/**
 * Deprecated functions. Let's not forget our roots.
 *
 * @package    Rise
 * @subpackage RHD\Rise\Includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.0.6
 */

// phpcs:disable

use RHD\Rise\Includes\Search;

/**
 * Imports `position` and `skill` term data from a CSV file. DEPRECATED, NOT UPDATED TO WORK PAST 1.0.4.
 *
 * Uses source @link https://docs.google.com/spreadsheets/d/1OmGwyvZCvKbWO3GU-AKES4WJ-_Xi4GgYiTfsQ9GTN-4/edit#gid=514651634
 *
 * @deprecated 1.0.2
 * @since 0.7
 *
 * @param  string $file_path
 * @return void
 */
function import_positions_and_skills_from_csv( $file_path ) {
	// Open the CSV file for reading
	$handle = fopen( $file_path, 'r' );

	// Loop through each row of the CSV file
	$current_dept_id = 0;
	$data            = fgetcsv( $handle );

	while ( false !== $data ) {
		// Loop through each cell in the current row
		foreach ( $data as $index => $cell ) {
			if ( 0 === $index ) {
				error_log( 'CELL: ' . print_r( $cell, true ) );

				if ( strpos( $cell, 'DEPT:' ) === 0 ) {
					// This is a department cell, so add or update it in the position taxonomy
					$dept_name          = trim( str_replace( 'DEPT: ', '', $cell ) );
					$dept_parent_id     = 0; // top-level term
					$existing_dept_term = term_exists( $dept_name, 'position' );

					if ( $existing_dept_term ) {
						$dept_id = $existing_dept_term['term_id'];
						wp_update_term( $dept_id, 'position', [
							'name'   => $dept_name,
							'parent' => $dept_parent_id,
						] );
					} else {
						$dept_id = wp_insert_term( $dept_name, 'position', [
							'parent' => $dept_parent_id,
						] );

						if ( !is_wp_error( $dept_id ) ) {
							$dept_id = $dept_id['term_id'];
						} else {
							wp_die( esc_textarea( $dept_id->get_error_message() ) );
						}
					}

					$current_dept_id = $dept_id;
				} else {
					// This is a job cell, so add or update it in the position taxonomy
					$job_name          = trim( $cell );
					$existing_job_term = term_exists( $job_name, 'position', $current_dept_id );
					$job_id            = null;

					if ( $existing_job_term ) {
						$job_id = $existing_job_term['term_id'];

						wp_update_term( $job_id, 'position', [
							'name'   => $job_name,
							'parent' => $current_dept_id,
						] );
					} else {
						if ( $job_name ) {
							$job_id = wp_insert_term( $job_name, 'position', [
								'parent' => $current_dept_id,
							] );

							if ( !is_wp_error( $job_id ) ) {
								$job_id = $job_id['term_id'];
							}
						}
					}
				}

				// Loop through the remaining cells in the row, adding or updating each skill term
				$count = count( $data );
				for ( $i = $index + 1; $i < $count; $i++ ) {
					if ( !$data[$i] || !trim( $data[$i] ) ) {
						continue;
					}

					$skill_name          = trim( $data[$i] );
					$existing_skill_term = term_exists( $skill_name, 'skill' );
					$skill_id            = null;

					if ( $existing_skill_term ) {
						$skill_id = $existing_skill_term['term_id'];
					} else {
						$new_skill = wp_insert_term( $skill_name, 'skill' );

						if ( is_wp_error( $new_skill ) ) {
							wp_die( $new_skill->get_error_message() );
						}

						$skill_id = $new_skill['term_id'];
					}

					// Update the skill's `jobs` field to include the current job ID
					if ( $skill_id ) {
						$pod  = pods( 'skill', $skill_id );
						$jobs = $pod->field( 'jobs', true, true );

						$separator = ',';
						$jobs_arr  = $jobs ? explode( $separator, $jobs ) : [];

						$jobs_arr[] = $job_id;
						$jobs_save  = implode( $separator, array_unique( $jobs_arr ) );
						$pod->save( 'jobs', $jobs_save );
					}
				}
			}
		}
	}
}

/**
 * Migrate the `jobs` field on the `skill` taxonomy to a relationship field.
 *
 * @deprecated 1.0.5 We salute this run-once function and send it on its way.
 *
 * @return void
 */
function rise_migrate_skill_ids_to_relationships() {
	$old_field = 'jobs';
	$new_field = 'jobs_relationship';

	// Get all 'skill' terms
	$skills = get_terms( [
		'taxonomy'   => 'skill',
		'hide_empty' => false,
	] );

	// Loop through each skill and get the 'jobs' pod field.
	foreach ( $skills as $skill ) {
		$pod = pods( 'skill', $skill->term_id );

		$jobs_string = $pod->field( $old_field, true, true );
		$jobs        = explode( ',', $jobs_string );

		// Loop through each job ID and add it to the skill's 'jobs' relationship field.
		foreach ( $jobs as $job_id ) {
			$pod->add_to( $new_field, $job_id );
		}
	}
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
 * Determines if a user has at least one credit and at least a first or last name set.
 *
 * @deprecated 1.0.8
 * @since 1.0.0
 *
 * @param  int     $user_id
 * @return boolean True if the user is searchable, false otherwise.
 */
function user_is_searchable( $user_id ) {
	$usermeta = \get_user_meta( $user_id );

	// check if user is the author of at least one item of the `credit` post type
	$credit_query = new \WP_Query( [
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
	$user_ids = Search::search_and_filter_crew_members( [$taxonomy_arg => $term_ids] );
	$users    = get_users( ['include' => $user_ids] );

	$csv[] = 'Name,Email';

	foreach ( $users as $user ) {
		// get user's first and last names
		$usermeta   = get_user_meta( $user->ID );
		$first_name = isset( $usermeta['first_name'][0] ) ? $usermeta['first_name'][0] : '';
		$last_name  = isset( $usermeta['last_name'][0] ) ? $usermeta['last_name'][0] : '';

		$csv[] = sprintf( '"%s %s",%s', esc_textarea( $first_name ), esc_textarea( $last_name ), esc_html( $user->user_email ) );
	}

	printf( '<pre>%s</pre><br /><br />', esc_textarea( implode( "\n", $csv ) ) );
	printf( 'Total: %d', count( $users ) );
}