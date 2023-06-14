<?php
/**
 * Deprecated functions. Let's not forget our roots.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.0.6
 */

// phpcs:disable

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
							error_log( 'error inserting dept: ' . $dept_id->get_error_message() );
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
