<?php
/**
 * Data Import utilities.
 *
 * @package    Rise
 * @subpackage Rise/Core
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.0.4
 */

namespace RHD\Rise\Core;

/**
 * Utility functions for the Rise plugin.
 */
class DataImport {
	/**
	 * Imports `position` and `skill` term data from a CSV file.
	 *
	 * Uses source @link https://docs.google.com/spreadsheets/d/1OmGwyvZCvKbWO3GU-AKES4WJ-_Xi4GgYiTfsQ9GTN-4/edit#gid=514651634
	 *
	 * @since 0.7
	 * @since 1.2 Updated to work with uploaded files and return status
	 *
	 * @param  string $file_path Path to the CSV file
	 * @return array Result array with success status, message, and imported counts
	 */
	public static function import_positions_and_skills_from_csv( $file_path ) {
		$result = [
			'success' => false,
			'message' => '',
			'data' => [
				'departments_created' => 0,
				'positions_created' => 0,
				'skills_created' => 0
			]
		];

		// Check if file exists and is readable
		if ( !file_exists( $file_path ) || !is_readable( $file_path ) ) {
			$result['message'] = 'File does not exist or is not readable.';
			return $result;
		}

		// Open the CSV file for reading
		$handle = fopen( $file_path, 'r' );
		
		if ( false === $handle ) {
			$result['message'] = 'Unable to open CSV file for reading.';
			return $result;
		}

		// Loop through each row of the CSV file
		$current_dept_id = 0;
		$data            = fgetcsv( $handle );
		$row_count = 0;

		try {
			while ( false !== $data ) {
				$row_count++;
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
								$result['data']['departments_created']++;
							} else {
								throw new \Exception( 'Error creating department: ' . $dept_id->get_error_message() );
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
									$result['data']['positions_created']++;
								} else {
									throw new \Exception( 'Error creating position: ' . $job_id->get_error_message() );
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
								throw new \Exception( 'Error creating skill: ' . $new_skill->get_error_message() );
							}

							$skill_id = $new_skill['term_id'];
							$result['data']['skills_created']++;
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
			
			// Get next row
			$data = fgetcsv( $handle );
		}
		
		// Close file handle
		fclose( $handle );
		
		// Set success message
		$result['success'] = true;
		$result['message'] = sprintf(
			'Import completed successfully! Created %d departments, %d positions, and %d skills from %d rows.',
			$result['data']['departments_created'],
			$result['data']['positions_created'],
			$result['data']['skills_created'],
			$row_count
		);
		
		} catch ( \Exception $e ) {
			// Close file handle if it's still open
			if ( is_resource( $handle ) ) {
				fclose( $handle );
			}
			
			$result['success'] = false;
			$result['message'] = 'Import failed: ' . $e->getMessage();
		}
		
		return $result;
	}

	/**
	 * Handle AJAX request for CSV upload and import.
	 *
	 * @since 1.2
	 * @return void
	 */
	public static function handle_csv_upload_ajax() {
		// Check nonce for security
		if ( !wp_verify_nonce( $_POST['nonce'], 'rise_csv_upload_nonce' ) ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'Security check failed.'
			] ) );
		}

		// Check user permissions
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'Insufficient permissions.'
			] ) );
		}

		// Check if file was uploaded
		if ( empty( $_FILES['csv_file'] ) ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'No file uploaded.'
			] ) );
		}

		$uploaded_file = $_FILES['csv_file'];

		// Check for upload errors
		if ( $uploaded_file['error'] !== UPLOAD_ERR_OK ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'File upload error: ' . $uploaded_file['error']
			] ) );
		}

		// Check file type
		$file_info = pathinfo( $uploaded_file['name'] );
		if ( !isset( $file_info['extension'] ) || strtolower( $file_info['extension'] ) !== 'csv' ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'Invalid file type. Please upload a CSV file.'
			] ) );
		}

		// Import the CSV
		$result = self::import_positions_and_skills_from_csv( $uploaded_file['tmp_name'] );

		// Return result as JSON
		wp_die( json_encode( $result ) );
	}
}
