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
 * Filters out user profiles which don't have either a first or last name, or which have no contact information.
 *
 * Used to prevent incomplete profiles from appearing in search.
 *
 * @param  int     $author_id
 * @return boolean The result of the filter operation.
 */
function remove_incomplete_profiles_from_search( $author_id ) {
	$meta = get_user_meta( $author_id );
	if ( !$meta['first_name'][0] && !$meta['last_name'][0] ) {
		return false;
	}

	$pod = pods( 'user', $author_id );

	// If email, phone, and website are all unset, ignore this user.
	if ( !$pod->field( 'contact_email', true, true ) && !$pod->field( 'phone', true, true ) && !$pod->field( 'website_url', true, true ) ) {
		return false;
	}

	return true;
}

/**
 * Imports `position` and `skill` term data from a CSV file.
 *
 * Uses source @link https://docs.google.com/spreadsheets/d/1OmGwyvZCvKbWO3GU-AKES4WJ-_Xi4GgYiTfsQ9GTN-4/edit#gid=514651634
 *
 * @deprecated 1.0.2beta
 *
 * @param  [type] $file_path
 * @return void
 */
// phpcs:disable
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
// phpcs:enable

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
		unlink( $file['tmp_name'] );
		return false;
	}

	$id = media_handle_sideload( $file, 0 );

	if ( is_wp_error( $id ) ) {
		unlink( $file['tmp_name'] );
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

/**
 * Retrieve the user IDs for users with the given terms.
 *
 * @param  array $terms           The terms to query users for. Keys are taxonomies, values are arrays of term IDs.
 * @param  array $include_authors An array of user IDs to include in the query.
 * @return int[] The user IDs.
 */
function query_users_with_terms( $terms, $include_authors = [] ) {
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
 * Checks whether the given reCAPTCHA response is valid.
 *
 * @param  string  $response The reCAPTCHA response.
 * @return boolean Whether the response is valid.
 */
function recaptcha_is_valid( $response ) {
	if ( !defined( 'RECAPTCHA_SECRET_KEY' ) ) {
		return false;
	}

	$url  = 'https://www.google.com/recaptcha/api/siteverify';
	$data = [
		'secret'   => RECAPTCHA_SECRET_KEY,
		'response' => $response,
	];

	$options = [
		'http' => [
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query( $data ),
		],
	];

	$context = stream_context_create( $options );
	// TODO use wp_remote_get() instead of file_get_contents()
	$result = file_get_contents( $url, false, $context );

	return json_decode( $result )->success;
}

/**
 * Get the site name.
 *
 * @source wp-graphql/src/Mutation/SendPasswordResetEmail.php Original source
 * @since 1.0.0beta
 *
 * @return string
 */
function get_email_friendly_site_name() {
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
function search_and_filter_crew_members( $args ) {
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

	// Filter out any excluded users.
	if ( isset( $args['exclude'] ) ) {
		$authors = array_diff( $authors, $args['exclude'] );
	}

	// Filter out authors with no name set, or no contact info.
	$authors = array_filter( array_unique( $authors ), 'remove_incomplete_profiles_from_search' );

	// Filter users by selected taxonomies.
	$user_taxonomy_terms = [];
	foreach ( $user_filters as $tax => $term_ids ) {
		if ( empty( $term_ids ) ) {
			continue;
		}

		$user_taxonomy_terms[$tax] = $term_ids;
	}

	// Filter users by taxonomy
	$filtered_authors = query_users_with_terms( $user_taxonomy_terms, $authors );

	return $filtered_authors;

}
// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_tax_query
