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
	 * Treats all CSV rows as second-level positions under a specified parent department.
	 *
	 * @since 0.7
	 * @since 1.2 Updated to work with uploaded files and return status
	 * @since 1.3 Simplified to treat all rows as second-level positions
	 *
	 * @param  string $file_path Path to the CSV file
	 * @param  int    $parent_dept_id ID of the parent department (0 to create new)
	 * @param  string $new_dept_name Name for new department (if parent_dept_id is 0)
	 * @return array  Result array with success status, message, and imported counts
	 */
	public static function import_positions_and_skills_from_csv( $file_path, $parent_dept_id = 0, $new_dept_name = '' ) {
		$result = [
			'success' => false,
			'message' => '',
			'data'    => [
				'departments_created' => 0,
				'positions_created'   => 0,
				'skills_created'      => 0,
			],
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

		// Determine the parent department ID
		$current_dept_id = $parent_dept_id;
		
		// Create new department if needed
		if ( 0 === $parent_dept_id && !empty( $new_dept_name ) ) {
			$dept_name          = trim( $new_dept_name );
			$existing_dept_term = term_exists( $dept_name, 'position' );
			
			if ( $existing_dept_term ) {
				$current_dept_id = $existing_dept_term['term_id'];
			} else {
				$dept_id = wp_insert_term( $dept_name, 'position', [
					'parent' => 0, // top-level term
				] );
				
				if ( !is_wp_error( $dept_id ) ) {
					$current_dept_id = $dept_id['term_id'];
					$result['data']['departments_created']++;
				} else {
					throw new \Exception( 'Error creating department: ' . $dept_id->get_error_message() );
				}
			}
		} elseif ( 0 === $parent_dept_id ) {
			throw new \Exception( 'Parent department ID is required, or provide a new department name.' );
		}

		// Loop through each row of the CSV file
		$data      = fgetcsv( $handle );
		$row_count = 0;

		try {
			while ( false !== $data ) {
				$row_count++;
				
				// Skip empty rows
				if ( empty( array_filter( $data ) ) ) {
					$data = fgetcsv( $handle );
					continue;
				}

				// First column is the position name
				$position_name = trim( $data[0] );
				
				if ( empty( $position_name ) ) {
					$data = fgetcsv( $handle );
					continue;
				}

				// Create the position as a child of the current department
				$existing_position_term = term_exists( $position_name, 'position', $current_dept_id );
				$position_id            = null;

				if ( $existing_position_term ) {
					// Use existing position term without updating it to preserve existing data
					$position_id = $existing_position_term['term_id'];
				} else {
					$position_id = wp_insert_term( $position_name, 'position', [
						'parent' => $current_dept_id,
					] );

					if ( !is_wp_error( $position_id ) ) {
						$position_id = $position_id['term_id'];
						$result['data']['positions_created']++;
					} else {
						throw new \Exception( 'Error creating position: ' . $position_id->get_error_message() );
					}
				}

				// Collect all skills for this position
				$position_skills = [];
				$count = count( $data );
				for ( $i = 1; $i < $count; $i++ ) {
					if ( !isset( $data[$i] ) || !trim( $data[$i] ) ) {
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

					if ( $skill_id ) {
						$position_skills[] = $skill_id;
					}
				}

				// Associate all skills with the position
				if ( $position_id && !empty( $position_skills ) ) {
					$pod = pods( 'position', $position_id );
					
					// Get existing skills for this position to avoid overwriting
					$existing_skills = $pod->field( 'skills', true, true );
					$existing_skill_ids = [];
					
					if ( is_array( $existing_skills ) ) {
						$existing_skill_ids = wp_list_pluck( $existing_skills, 'term_id' );
					}
					
					// Merge existing and new skills, remove duplicates
					$all_skills = array_unique( array_merge( $existing_skill_ids, $position_skills ) );
					
					// Save the skills to the position
					$pod->save( 'skills', $all_skills );
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
	 *
	 * @return void
	 */
	public static function handle_csv_upload_ajax() {
		// Check nonce for security
		if ( !wp_verify_nonce( $_POST['nonce'], 'rise_csv_upload_nonce' ) ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'Security check failed.',
			] ) );
		}

		// Check user permissions
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'Insufficient permissions.',
			] ) );
		}

		// Check if file was uploaded
		if ( empty( $_FILES['csv_file'] ) ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'No file uploaded.',
			] ) );
		}

		$uploaded_file = $_FILES['csv_file'];

		// Check for upload errors
		if ( UPLOAD_ERR_OK !== $uploaded_file['error'] ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'File upload error: ' . $uploaded_file['error'],
			] ) );
		}

		// Check file type
		$file_info = pathinfo( $uploaded_file['name'] );
		if ( !isset( $file_info['extension'] ) || strtolower( $file_info['extension'] ) !== 'csv' ) {
			wp_die( json_encode( [
				'success' => false,
				'message' => 'Invalid file type. Please upload a CSV file.',
			] ) );
		}

		// Get parent department and new department name from POST data
		$parent_dept_id = isset( $_POST['parent_department'] ) ? absint( $_POST['parent_department'] ) : 0;
		$new_dept_name  = isset( $_POST['new_department_name'] ) ? sanitize_text_field( $_POST['new_department_name'] ) : '';

		// Import the CSV
		$result = self::import_positions_and_skills_from_csv( $uploaded_file['tmp_name'], $parent_dept_id, $new_dept_name );

		// Return result as JSON
		wp_die( json_encode( $result ) );
	}
}
