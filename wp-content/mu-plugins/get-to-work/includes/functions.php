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

/**
 * Imports `position` and `skill` term data from a CSV file.
 *
 * Uses source @link https://docs.google.com/spreadsheets/d/1OmGwyvZCvKbWO3GU-AKES4WJ-_Xi4GgYiTfsQ9GTN-4/edit#gid=514651634
 *
 * @param  [type] $file_path
 * @return void
 */
function import_positions_and_skills_from_csv( $file_path ) {
	// Open the CSV file for reading
	$handle = fopen( $file_path, 'r' );

	// Loop through each row of the CSV file
	$current_dept_id = 0;

	while (  ( $data = fgetcsv( $handle ) ) !== FALSE ) {
		// Loop through each cell in the current row
		foreach ( $data as $index => $cell ) {
			if ( 0 === $index ) {

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

						if ( ! is_wp_error( $dept_id ) ) {
							$dept_id = $dept_id['term_id'];
						} else {
							error_log( 'error inserting dept: ' . $dept_id->get_error_message() );
							wp_die( $dept_id->get_error_message() );
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

						error_log( 'existing update: ' . $job_name . ' (' . $job_id . ')' . ' in ' . $current_dept_id );

						wp_update_term( $job_id, 'position', [
							'name'   => $job_name,
							'parent' => $current_dept_id,
						] );
					} else {
						if ( $job_name ) {
							$job_id = wp_insert_term( $job_name, 'position', [
								'parent' => $current_dept_id,
							] );

							if ( ! is_wp_error( $job_id ) ) {
								$job_id = $job_id['term_id'];
							}
						}
					}
				}

				// Loop through the remaining cells in the row, adding or updating each skill term
				for ( $i = $index + 1; $i < count( $data ); $i++ ) {
					if ( ! $data[$i] || ! trim( $data[$i] ) ) {
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
							error_log( 'error inserting skill: ' . $new_skill->get_error_message() );
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
 * Gets a newly uploaded file's attachment ID.
 *
 * @param  string $url The URL of the file.
 * @return int    The attachment ID.
 */
function get_attachment_id_by_url( $url ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$file             = [];
	$file['name']     = basename( $url );
	$file['tmp_name'] = download_url( $url );
	$file['error']    = '';
	$file['size']     = filesize( $file['tmp_name'] );

	if ( is_wp_error( $file['tmp_name'] ) ) {
		@unlink( $file['tmp_name'] );
		return false;
	}

	$id = media_handle_sideload( $file, 0 );

	if ( is_wp_error( $id ) ) {
		@unlink( $file['tmp_name'] );
		return false;
	}

	return $id;
}

/**
 * Converts a string from camelCase to underscore_notation.
 *
 * @param  string $string
 * @return string The converted string.
 */
function camel_case_to_underscore( $string ) {
	$string = preg_replace( '/(?!^)[[:upper:]]/', '_$0', $string );
	$string = preg_replace( '/([a-zA-Z])([0-9])/', '$1_$2', $string );
	$string = strtolower( $string );
	return $string;
}
