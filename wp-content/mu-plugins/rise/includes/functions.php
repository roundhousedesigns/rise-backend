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
 * @param  int            $credit_id The credit's ID.
 * @param  int            $index     The credit's new display index.
 * @return int|false|null The ID of the conflict range on success, false on failure, or null if there was an issue with the Pod itself.
 */
function rise_update_credit_index( $credit_id, $index ) {
	// Get the user's pod.
	$pod = pods( 'credit', $credit_id );

	// Update the credit's pod.
	$update_fields = [
		'index' => $index,
	];

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

	// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
	// If email, phone, and website are all unset, ignore this user.
	// TODO determine if we actually want this search results check.
	// $pod = pods( 'user', $author_id );
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

	$attachment_id = media_handle_sideload( $file, 0 );

	if ( is_wp_error( $attachment_id ) ) {
		unlink( $file['tmp_name'] );
		throw new WP_Error( 'attachment_processing_error', esc_html( $attachment_id->get_error_message() ) );
	}

	return $attachment_id;
}

/**
 * Retrieve the user IDs for users with the given terms.
 *
 * @param  array    $terms           Array of terms ID arrays to query users for, keyed by taxonomy ID.
 * @param  string[] $include_authors An array of user IDs to include in the query.
 * @return int[]    The user IDs.
 */
function rise_query_users( $terms, $authors = [] ) {
	if ( !$terms ) {
		$users    = get_public_profile_users( $authors );
		$user_ids = wp_list_pluck( $users, 'ID' );
		shuffle( $user_ids );

		return $user_ids;
	}

	// Get the object IDs for the terms in the taxonomies
	$user_ids = [];
	foreach ( $terms as $taxonomy => $term_ids ) {
		// Get users with the selected terms
		$users_in_term = get_objects_in_term( $term_ids, $taxonomy );

		// If $user_ids is empty, set it to $users_in_term.
		// Otherwise, strip any users from $user_ids that are not also in $users_in_term.
		if ( !$user_ids ) {
			$user_ids = $users_in_term;
			continue;
		}

		$user_ids = array_intersect( $user_ids, $users_in_term );
	}

	// Filter out IDs from the $user_ids array that are not also in the $authors array
	if ( !empty( $authors ) ) {
		$user_ids = array_intersect( $user_ids, $authors );
	}

	// Remove users who are not in the 'crew-member' role
	$user_ids = array_filter( $user_ids, function ( $user_id ) {
		$user = get_userdata( $user_id );

		if ( !$user ) {
			return false;
		}

		return in_array( 'crew-member', $user->roles, true );
	} );

	// Remove duplicates from the object IDs array
	$user_ids = array_unique( $user_ids );

	if ( !$user_ids ) {
		return [];
	}

	// Remove any users with the 'disable_profile' pod meta set to true
	$users = get_public_profile_users( $user_ids );

	return wp_list_pluck( $users, 'ID' );
}

/**
 * Retrieves public profile users based on the given user IDs.
 *
 * @param  int[]     $user_ids An array of user IDs.
 * @return WP_User[] An array of user objects with public profiles.
 */
function get_public_profile_users( $user_ids ) {
	// Query users based on the object IDs
	$args = [
		'include' => $user_ids,
		'role'    => 'crew-member',
	];

	$users = get_users( $args );

	$filtered = array_filter( $users, function ( $user ) {
		$pod = pods( 'user', $user->ID );

		return $pod->field( 'disable_profile' ) !== '1' ? true : false;
	} );

	return $filtered;
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
 * Translate the frontend search filters to backend query arguments.
 *
 * @param  array $args The frontend search filters.
 * @return array The arguments for use in a WP_Query.
 */
function rise_translate_taxonomy_filters( $args ) {
	return [
		'union'             => isset( $args['unions'] ) ? $args['unions'] : '',
		'location'          => isset( $args['locations'] ) ? $args['locations'] : '',
		'experience_level'  => isset( $args['experienceLevels'] ) ? $args['experienceLevels'] : '',
		'gender_identity'   => isset( $args['genderIdentities'] ) ? $args['genderIdentities'] : '',
		'personal_identity' => isset( $args['personalIdentities'] ) ? $args['personalIdentities'] : '',
		'racial_identity'   => isset( $args['racialIdentities'] ) ? $args['racialIdentities'] : '',
	];
}

/**
 * Query crew members based on the given arguments.
 *
 * Used by the frontend search filters, and by reporting functions.
 *
 * @param  array $args
 * @param  int   $user_id The user ID to exclude from the query (current user). Default 0.
 * @return int[] The user IDs.
 */
function rise_search_and_filter_crew_members( $args, $user_id = 0 ) {
	// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_tax_query

	// Save args for future recall
	if ( 0 !== $user_id ) {
		Rise_Users::save_user_search_history( $user_id, $args );
	}

	$credit_filters = [
		'position' => isset( $args['positions'] ) ? $args['positions'] : '',
		'skill'    => isset( $args['skills'] ) ? $args['skills'] : '',
	];

	$user_filters = rise_translate_taxonomy_filters( $args );

	// Start building the Credit query args.
	$credit_args = [
		'post_type'      => 'credit',
		'tax_query'      => ['relation' => 'AND'],
		'posts_per_page' => -1,
		'orderby'        => 'rand',
	];

	foreach ( $credit_filters as $taxonomy => $selected_terms ) {
		$terms = $selected_terms;

		if ( 'integer' === gettype( $terms ) ) {
			$terms = [$terms];
		}

		if ( !empty( $terms ) ) {
			if ( 'position' === $taxonomy ) {
				foreach ( $terms as $term_id ) {
					// Get 'also_search' terms add them to the query.
					$pod     = pods( 'position', $term_id );
					$related = $pod->field( 'also_search' );

					// No related terms.
					if ( empty( $related ) || !$related ) {
						continue;
					}

					// Add the related terms to the query. Scoring happens elsewhere, so it's not affected
					// by the query additions.
					foreach ( $related as $term ) {
						$terms[] = $term['term_id'];
					}
				}
			}

			$credit_args['tax_query'][] = [
				'taxonomy'         => $taxonomy,
				'field'            => 'term_id',
				'terms'            => array_unique( $terms ),
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
	$user_taxonomy_term_ids = [];
	foreach ( $user_filters as $tax => $term_ids ) {
		if ( empty( $term_ids ) ) {
			continue;
		}

		$user_taxonomy_term_ids[$tax] = $term_ids;
	}

	return rise_query_users( $user_taxonomy_term_ids, $authors );
}
// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_tax_query

/**
 * Safe redirect with nocache headers to prevent caching of the redirect. Don't forget to call `exit`
 * after calling this function to prevent further execution.
 *
 * @deprecated Not used.
 * @since  1.0.4
 *
 * @param  string $location The path to redirect to.
 * @param  int    $status   The HTTP status code to use (default: 302)
 * @return void
 */
function rise_nocache_redirect( $location, $status = 302 ) {
	nocache_headers();
	wp_safe_redirect( esc_url_raw( $location ), $status, 'RISE' );
	wp_die();
}

/**
 * Generates a default user slug based on the user's first and last name.
 *
 * @since 1.1.10
 *
 * @param  string $first_name The user's first name.
 * @param  string $last_name  The user's last name.
 * @return string The sanitized title of the user's full name.
 */
function rise_generate_default_user_slug( $first_name = '', $last_name = '' ) {
	// Generate the full name based on the user's first and last name.
	return sanitize_title( $first_name . ' ' . $last_name );
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

	printf( '<pre>%s</pre><br /><br />', esc_textarea( implode( "\n", $csv ) ) );
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

/**
 * Get the message body of the changed password alert email
 *
 * @since 1.0.3
 *
 * @param  WP_User $user_data User data
 * @return string  Message body
 */
function rise_get_password_change_email_message( $user_data ) {
	$first_name = get_user_meta( $user_data->ID, 'first_name', true );

	$message = __( 'Hi', 'rise' ) . ' ' . esc_html( $first_name ) . "\r\n\r\n";
	/* translators: %s: site name */
	$message .= sprintf( __( 'This notice confirms that your password was changed on: %s', 'rise' ), rise_get_email_friendly_site_name() ) . "\r\n\r\n";
	/* translators: %s: user login */
	$message .= sprintf( __( 'If you did not change your password, please contact us immediately at %s', 'rise' ), get_option( 'admin_email' ) ) . "\r\n\r\n";
	$message .= sprintf( __( 'This email has been sent to %s', 'rise' ), $user_data->user_email ) . "\r\n\r\n";
	$message .= __( 'Thanks,', 'rise' ) . "\r\n\r\n";
	$message .= rise_get_email_friendly_site_name() . "\r\n";
	$message .= RISE_FRONTEND_URL . "\r\n";

	return $message;
}

/**
 * Get the subject of the changed password email
 *
 * @since 1.0.3
 *
 * @return string
 */
function rise_get_password_change_email_subject() {
	/* translators: Password reset email subject. %s: Site name */
	return sprintf( __( '[%s] Password Changed', 'rise' ), rise_get_email_friendly_site_name() );
}

/**
 * Get the message body of the changed email address alert email
 *
 * @since 1.0.3
 *
 * @param  WP_User $user_data User data
 * @return string  Message body
 */
function rise_get_email_change_email_message( $user_data ) {
	$first_name = get_user_meta( $user_data->ID, 'first_name', true );

	$message = __( 'Hi', 'rise' ) . ' ' . esc_html( $first_name ) . "\r\n\r\n";
	/* translators: %s: site name */
	$message .= sprintf( __( 'This notice confirms that your email was updated on: %s', 'rise' ), rise_get_email_friendly_site_name() ) . "\r\n\r\n";
	/* translators: %s: user login */
	$message .= sprintf( __( 'If you did not change your email address, please contact us immediately at %s', 'rise' ), get_option( 'admin_email' ) ) . "\r\n\r\n";
	$message .= sprintf( __( 'This email has been sent to %s', 'rise' ), $user_data->user_email ) . "\r\n\r\n";
	$message .= __( 'Thanks,', 'rise' ) . "\r\n\r\n";
	$message .= rise_get_email_friendly_site_name() . "\r\n";
	$message .= RISE_FRONTEND_URL . "\r\n";

	return $message;
}

/**
 * Get the subject of the changed password email
 *
 * @since 1.0.3
 *
 * @return string
 */
function rise_get_email_change_email_subject() {
	/* translators: Password reset email subject. %s: Site name */
	return sprintf( __( '[%s] Email Changed', 'rise' ), rise_get_email_friendly_site_name() );
}

/**
 * Get the message body of the password reset email
 *
 * @source wp-graphql/src/Mutation/SendPasswordResetEmail.php Original source
 * @since 1.0.0
 *
 * @param  WP_User  $user_data User data
 * @param  string   $key       Password reset key
 * @return string
 */
function rise_get_password_reset_email_message( $user_data, $key ) {
	$message = __( 'Someone has requested a password reset for the following account:', 'rise' ) . "\r\n\r\n";
	/* translators: %s: site name */
	$message .= sprintf( __( 'Site Name: %s', 'rise' ), rise_get_email_friendly_site_name() ) . "\r\n\r\n";
	/* translators: %s: user login */
	$message .= sprintf( __( 'Username: %s', 'rise' ), $user_data->user_login ) . "\r\n\r\n";
	$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'rise' ) . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:', 'rise' ) . "\r\n\r\n";
	$message .= '<' . RISE_FRONTEND_URL . "?key={$key}&login=" . rawurlencode( $user_data->user_login ) . ">\r\n";

	/**
	 * Filters the message body of the password reset mail.
	 *
	 * If the filtered message is empty, the password reset email will not be sent.
	 *
	 * @param string  $message    Default mail message.
	 * @param string  $key        The activation key.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 */
	return apply_filters( 'retrieve_password_message', $message, $key, $user_data->user_login, $user_data );
}

/**
 * Get the subject of the password reset email
 *
 * @source wp-graphql/src/Mutation/SendPasswordResetEmail.php Original source
 * @since 1.0.0
 *
 * @param  WP_User  $user_data User data
 * @return string
 */
function rise_get_password_reset_email_subject( $user_data ) {
	/* translators: Password reset email subject. %s: Site name */
	$title = sprintf( __( '[%s] Password Reset', 'rise' ), rise_get_email_friendly_site_name() );

	/**
	 * Filters the subject of the password reset email.
	 *
	 * @param string  $title      Default email title.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 */
	return apply_filters( 'retrieve_password_title', $title, $user_data->user_login, $user_data );
}

/**
 * Delete a post item authored by the current user.
 *
 * @param  int     $id      The post ID.
 * @param  int     $user_id The requesting user's ID.
 * @return boolean True if the post was deleted, false otherwise.
 */
function rise_delete_own_allowed_post_item( $id, $user_id ) {
	$allowed_post_types = ['credit', 'saved_search', 'conflict_range'];

	$post_type = get_post_type( $id );
	$author_id = get_post_field( 'post_author', $id );

	if ( $author_id !== $user_id ) {
		return false;
	}

	if ( !in_array( $post_type, $allowed_post_types, true ) ) {
		return false;
	}

	$result = wp_delete_post( $id, false );

	if ( $result instanceof WP_Post ) {
		return true;
	}

	return false;
}
