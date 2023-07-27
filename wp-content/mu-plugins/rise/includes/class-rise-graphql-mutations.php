<?php
/**
 * Registers GraphQL mutations.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Data\UserMutation;

class Rise_GraphQL_Mutations {
	/**
	 * Run the registrations.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_mutations() {
		$this->register_profile_mutations();
	}

	/**
	 * Register user mutations.
	 *
	 * @return int The user ID on success, `0` on failure.
	 */
	protected function register_profile_mutations() {
		/**
		 * Create a user with reCAPTCHA verification.
		 */
		register_graphql_mutation(
			'registerUserWithReCaptcha',
			[
				'inputFields'         => [
					'username'       => [
						'type'        => [
							'non_null' => 'String',
						],
						// translators: the placeholder is the name of the type of post object being updated
						'description' => __( 'A string that contains the user\'s username for logging in.', 'rise' ),
					],
					'email'          => [
						'type'        => 'String',
						'description' => __( 'A string containing the user\'s email address.', 'rise' ),
					],
					'reCaptchaToken' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'A string that contains the reCAPTCHA response token.', 'rise' ),
					],
					'firstName'      => [
						'type'        => 'String',
						'description' => __( '	The user\'s first name.', 'rise' ),
					],
					'lastName'       => [
						'type'        => 'String',
						'description' => __( 'The user\'s last name.', 'rise' ),
					],
					'password'       => [
						'type'        => 'String',
						'description' => __( 'A string that contains the plain text password for the user.', 'rise' ),
					],
				],
				'outputFields'        => [
					'user' => [
						'type'        => 'User',
						'description' => __( 'The User object mutation type.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input, $context, $info ) {
					// TODO Refactor and abstract this into a function

					if ( !isset( $input['reCaptchaToken'] ) || !$input['reCaptchaToken'] ) {
						throw new UserError( __( 'No reCAPTCHA response token was provided.', 'rise' ) );
					}

					/**
					 * Check the reCAPTCHA response
					 */
					if ( !recaptcha_is_valid( $input['reCaptchaToken'] ) ) {
						throw new UserError( __( 'The reCAPTCHA response was invalid.', 'rise' ) );
					}

					// Set the user's slug (user_nicename)
					$input_data = $input;
					$input_data = array_merge( $input_data, ['nicename' => Rise_Users::generate_default_user_slug( $input['firstName'], $input['lastName'] )] );

					/**
					 * Map all of the args from GQL to WP friendly
					 */
					$user_args = UserMutation::prepare_user_object( $input_data, 'registerUserWithReCaptcha' );

					/**
					 * Create the new user
					 */
					$user_id = wp_insert_user( $user_args );

					/**
					 * Throw an exception if the user failed to create
					 */
					if ( is_wp_error( $user_id ) ) {
						$error_code = $user_id->get_error_code();

						if ( !empty( $error_code ) ) {
							throw new UserError( $error_code );
						} else {
							throw new UserError( __( 'The object failed to create but no error was provided', 'rise' ) );
						}
					}

					/**
					 * If the $post_id is empty, we should throw an exception
					 */
					if ( empty( $user_id ) ) {
						throw new UserError( __( 'The object failed to create', 'rise' ) );
					}

					/**
					 * Update additional user data
					 */
					UserMutation::update_additional_user_object_data( $user_id, $input, 'registerUserWithReCaptcha', $context, $info );

					/**
					 * Return the new user ID
					 */
					return [
						'id'   => $user_id,
						'user' => $context->get_loader( 'user' )->load_deferred( $user_id ),
					];
				},
			]
		);

		register_graphql_mutation(
			'loginWithCookiesAndReCaptcha',
			[
				'inputFields'         => [
					'login'          => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'Input your user/e-mail.', 'wp-graphql-cors' ),
					],
					'password'       => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'Input your password.', 'wp-graphql-cors' ),
					],
					'reCaptchaToken' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'A string that contains the reCAPTCHA response token.', 'rise' ),
					],
					'rememberMe'     => [
						'type'        => 'Boolean',
						'description' => __(
							'Whether to "remember" the user. Increases the time that the cookie will be kept. Default false.',
							'wp-graphql-cors'
						),
					],
				],
				'outputFields'        => [
					'status' => [
						'type'        => 'String',
						'description' => 'Login operation status',
						'resolve'     => function ( $payload ) {
							return $payload['status'];
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					if ( !isset( $input['reCaptchaToken'] ) || !$input['reCaptchaToken'] ) {
						throw new UserError( __( 'No reCAPTCHA response token was provided.', 'rise' ) );
					}

					/**
					 * Check the reCAPTCHA response
					 */
					if ( !recaptcha_is_valid( $input['reCaptchaToken'] ) ) {
						throw new UserError( __( 'The reCAPTCHA response was invalid.', 'rise' ) );
					}

					// Prepare credentials.
					$credential_keys = [
						'login'      => 'user_login',
						'password'   => 'user_password',
						'rememberMe' => 'remember',
					];
					$credentials = [];
					foreach ( $input as $key => $value ) {
						if ( in_array( $key, array_keys( $credential_keys ), true ) ) {
							$credentials[$credential_keys[$key]] = $value;
						}
					}
					// Authenticate User.
					$user = wpgraphql_cors_signon( $credentials );

					if ( is_wp_error( $user ) ) {
						throw new UserError( !empty( $user->get_error_code() ) ? $user->get_error_code() : 'Login error' );
					}

					return ['status' => 'SUCCESS'];
				},
			]
		);

		/**
		 * Send a password reset email to a user.
		 */
		register_graphql_mutation(
			'sendPasswordResetEmailWithReCaptcha',
			[
				'description'         => __( 'Send password reset email to user', 'rise' ),
				'inputFields'         => [
					'username'       => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'A string that contains the user\'s username or email address.', 'rise' ),
					],
					'reCaptchaToken' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'A string that contains the reCAPTCHA response token.', 'rise' ),
					],
				],
				'outputFields'        => [
					'user'    => [
						'type'              => 'User',
						'description'       => __( 'The user that the password reset email was sent to', 'rise' ),
						'deprecationReason' => __( 'This field will be removed in a future version of WPGraphQL', 'rise' ),
						'resolve'           => function ( $payload, $args, AppContext $context ) {
							return !empty( $payload['id'] ) ? $context->get_loader( 'user' )->load_deferred( $payload['id'] ) : null;
						},
					],
					'success' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the mutation completed successfully. This does NOT necessarily mean that an email was sent.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					if ( !self::was_username_provided( $input ) ) {
						throw new UserError( __( 'Enter a username or email address.', 'rise' ) );
					}

					if ( !isset( $input['reCaptchaToken'] ) || !$input['reCaptchaToken'] ) {
						throw new UserError( __( 'No reCAPTCHA response token was provided.', 'rise' ) );
					}

					/**
					 * Check the reCAPTCHA response
					 */
					if ( !recaptcha_is_valid( $input['reCaptchaToken'] ) ) {
						throw new UserError( __( 'The reCAPTCHA response was invalid.', 'rise' ) );
					}

					// We obsfucate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'success' => false,
						'id'      => null,
					];

					$user_data = get_user_by( 'email', $input['username'] );

					if ( !$user_data ) {
						graphql_debug( __( 'There is no user registered with that email address.', 'rise' ) );

						return $payload;
					}

					// Get the password reset key.
					$key = get_password_reset_key( $user_data );
					if ( is_wp_error( $key ) ) {
						graphql_debug( __( 'Unable to generate a password reset key.', 'rise' ) );

						return $payload;
					}

					// Mail the reset key.
					$subject = self::get_password_reset_email_subject( $user_data );
					$message = self::get_password_reset_email_message( $user_data, $key );

					$email_sent = wp_mail(  // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
						$user_data->user_email,
						wp_specialchars_decode( $subject ),
						$message
					);

					// wp_mail can return a wp_error, but the docblock for it in WP Core is incorrect.
					// phpstan should ignore this check.
					// @phpstan-ignore-next-line
					if ( is_wp_error( $email_sent ) ) {
						graphql_debug( __( 'The email could not be sent.', 'rise' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'rise' ) );

						return $payload;
					}

					/**
					 * Return the ID of the user
					 */
					return [
						'id'      => $user_data->ID,
						'success' => true,
					];
				},
			]
		);

		/**
		 * Change a user's password
		 */
		register_graphql_mutation(
			'changePassword',
			[
				'inputFields'         => [
					'username'        => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The user\'s username or email address', 'rise' ),
					],
					'currentPassword' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The user\'s current password', 'rise' ),
					],
					'newPassword'     => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The user\'s new password', 'rise' ),
					],
				],
				'outputFields'        => [
					'success' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the mutation completed successfully. This does NOT necessarily mean that an email was sent.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// We obsfucate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'success' => false,
					];

					if ( !$input['username'] || !$input['currentPassword'] || !$input['newPassword'] ) {
						// TODO throw error here?
						return $payload;
					}

					// Authenticate the user with their current password.
					$user = wp_authenticate( $input['username'], $input['currentPassword'] );

					if ( is_wp_error( $user ) ) {
						throw new UserError( $user->get_error_code() );
					}

					// Update the user's password.
					wp_set_password( $input['newPassword'], $user->ID );

					// Send the confirmation email
					$message = self::get_password_change_email_message( $user );
					$subject = self::get_password_change_email_subject();

					// TODO verify that change password notices are sending
					$email_sent = wp_mail(  // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
						$user->user_email,
						wp_specialchars_decode( $subject ),
						$message
					);

					// wp_mail can return a wp_error, but the docblock for it in WP Core is incorrect.
					// phpstan should ignore this check.
					// @phpstan-ignore-next-line
					if ( is_wp_error( $email_sent ) ) {
						graphql_debug( __( 'The email could not be sent.', 'rise' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'rise' ) );

						return $payload;
					}

					return [
						'success' => true,
					];
				},
			]
		);

		/**
		 * Change a user's password
		 */
		register_graphql_mutation(
			'changeProfileSlug',
			[
				'inputFields'         => [
					'userId'  => [
						'type'        => ['non_null' => 'Int'],
						'description' => __( 'The user\'s ID', 'rise' ),
					],
					'newSlug' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The desired profile slug', 'rise' ),
					],
				],
				'outputFields'        => [
					'success' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the mutation completed successfully. This does NOT necessarily mean that an email was sent.', 'rise' ),
					],
					'slug'    => [
						'type'        => 'String',
						'description' => __( 'The user\'s new slug', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					$user = get_user_by( 'id', absint( $input['userId'] ) );

					if ( !$user ) {
						throw new UserError( 'user_not_found' );
					}

					// We obsfucate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'success' => false,
						'slug'    => $user->user_nicename,
					];

					if ( !$input['userId'] || !$input['newSlug'] ) {
						// TODO throw error here?
						return $payload;
					}

					// Check if the user is the current logged in user
					if ( get_current_user_id() !== $user->ID ) {
						throw new UserError( 'user_not_authorized' );
					}

					// If the new slug matches the current slug, just return success.
					if ( $user->user_nicename === $input['newSlug'] ) {
						$payload['success'] = true;

						return $payload;
					}

					// Make sure the new slug is unique
					$existing_slug_user = get_user_by( 'slug', $input['newSlug'] );

					if ( $existing_slug_user ) {
						throw new UserError( 'user_slug_not_unique' );
					}

					// Update the user's slug
					$user->user_nicename = sanitize_title( $input['newSlug'] );
					$result              = wp_update_user( $user );

					if ( is_wp_error( $result ) ) {
						throw new UserError( $result->get_error_code() );
					}

					return [
						'success' => true,
						'slug'    => $user->user_nicename,
					];
				},
			]
		);

		/**
		 * Update a user's profile.
		 */
		register_graphql_mutation(
			'updateProfile',
			[
				'inputFields'         => [
					'profile' => [
						'type'        => 'UserProfileInput',
						'description' => __( 'Profile data to update.', 'rise' ),
					],
				],
				'outputFields'        => [
					'result' => [
						'type'        => 'Int',
						'description' => 'The result of the get_user_meta() call.',
						'resolve'     => function ( $payload ) {
							return $payload['result'];
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// TODO Security check. Check if user is logged in.
					if ( !isset( $input['profile']['id'] ) ) {
						return [
							'result' => new \WP_Error( 'no_id', __( 'No ID provided.', 'rise' ) ),
						];
					}

					$user = new Rise_UserProfile( $input['profile'] );

					$result = $user->update_user_profile();

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'result' => !is_wp_error( $result ) ? $result : 0,
					];
				},
			],
		);

		/**
		 * Clear a user's specified profile field set up in Pods.
		 */
		register_graphql_mutation(
			'clearProfileField',
			[
				'inputFields'         => [
					'userId'    => [
						'type'        => 'Int',
						'description' => __( 'The ID of the user to update.', 'rise' ),
					],
					'fieldName' => [
						'type'        => 'String',
						'description' => __( 'The name of the file field to clear.', 'rise' ),
					],
				],
				'outputFields'        => [
					'result' => [
						'type'        => 'Int',
						'description' => 'The result of the get_user_meta() call.',
						'resolve'     => function ( $payload ) {
							return $payload['result'];
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// TODO Security check. Check if user is logged in.

					if ( !isset( $input['userId'] ) || !isset( $input['fieldName'] ) ) {
						return [
							'result' => new \WP_Error( 'no_name', __( 'Not enough data provided.', 'rise' ) ),
						];
					}

					$user_id    = absint( $input['userId'] );
					$field_name = esc_attr( $input['fieldName'] );

					$user = new Rise_UserProfile( ['id' => $user_id] );

					$result = $user->clear_profile_field( $field_name );

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'result' => !is_wp_error( $result ) ? $result : 0,
					];
				},
			]
		);

		/**
		 * Update or create a Credit.
		 */
		register_graphql_mutation(
			'updateOrCreateCredit',
			[
				'inputFields'         => [
					'credit' => [
						'type'        => 'CreditInput',
						'description' => __( 'The credit data to insert.', 'rise' ),
					],
				],
				'outputFields'        => [
					'updatedCredit' => [
						'type'        => 'CreditOutput',
						'description' => __( 'The updated credit data.', 'rise' ),
						'resolve'     => function ( $payload ) {
							return $payload['updatedCredit'];
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// TODO Security check. Check if user is logged in.

					if ( !isset( $input['credit'] ) ) {
						return [
							'updatedCredit' => new \WP_Error( 'no_id', __( 'No ID provided.', 'rise' ) ),
						];
					}

					$credit = new Rise_Credit( $input['credit'] );
					$result = $credit->update_credit();

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'updatedCredit' => !is_wp_error( $result ) ? $credit->prepare_credit_for_graphql() : 0,
					];
				},
			],
		);

		/**
		 * Update a user's credit order.
		 */
		register_graphql_mutation(
			'updateCreditOrder',
			[
				'inputFields'         => [
					'creditIds' => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The IDs of the credits in the order they should be displayed.', 'rise' ),
					],
				],
				'outputFields'        => [
					'creditIds' => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The IDs in their saved order.', 'rise' ),
						'resolve'     => function ( $payload ) {
							return $payload['creditIds'];
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// TODO Security check. Check if user is logged in.

					if ( !isset( $input['creditIds'] ) ) {
						return [
							'creditIds' => new \WP_Error( 'no_id', __( 'No IDs provided.', 'rise' ) ),
						];
					}

					$result = [];
					foreach ( $input['creditIds'] as $index => $id ) {
						$result[] = rise_update_credit_index( absint( $id ), $index );
					}

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'creditIds' => $result,
					];
				},
			]
		);

		/**
		 * Delete a user's credit.
		 */
		register_graphql_mutation(
			'deleteOwnCredit',
			[
				'inputFields'         => [
					'id' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the credit to delete.', 'rise' ),
					],
				],
				'outputFields'        => [
					'result' => [
						'type'        => 'Boolean',
						'description' => __( 'The result of the delete operation.', 'rise' ),
						'resolve'     => function ( $payload ) {
							return $payload['result'];
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// TODO Security check. Check if user is logged in.

					if ( !isset( $input['id'] ) ) {
						return [
							'result' => new \WP_Error( 'no_id', __( 'No ID provided.', 'rise' ) ),
						];
					}

					$result = wp_delete_post( $input['id'], false );

					if ( $result instanceof WP_Post ) {
						return [
							'result' => true,
						];
					} else {
						return new WP_Error( 'delete_failed', __( 'The credit could not be deleted.', 'rise' ) );
					}
				},
			]
		);

		/**
		 * Upload a file.
		 */
		register_graphql_mutation(
			'uploadFile', [
				'inputFields'         => [
					'file'   => [
						'type'        => ['non_null' => 'Upload'],
						'description' => __( 'The file to upload.', 'rise' ),
					],
					'name'   => [
						'type'        => 'String',
						'description' => __( 'The name of the field.', 'rise' ),
					],
					'userId' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the user to set the profile image for.', 'rise' ),
					],
				],
				'outputFields'        => [
					'fileUrl' => [
						'type'    => 'String',
						'resolve' => function ( $payload ) {
							return $payload['fileUrl'];
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					if ( !function_exists( 'wp_handle_sideload' ) ) {
						require_once ABSPATH . 'wp-admin/includes/file.php';
					}

					$field   = isset( $input['name'] ) ? camel_case_to_underscore( $input['name'] ) : '';
					$user_id = isset( $input['userId'] ) ? $input['userId'] : null;

					$uploaded = wp_handle_sideload( $input['file'], [
						'test_form' => false,
						'test_type' => true,
					] );

					$uploaded['file'] = maybe_strip_exif( $uploaded['file'] );

					// Get the attachment ID from the uploaded file.
					$attachment_id = rise_get_attachment_id_by_url( $uploaded['url'] );

					// Set the user's profile image.
					if ( $attachment_id && $user_id ) {
						$pod = pods( 'user', $user_id );

						$update_fields[$field] = $attachment_id;

						$pod->save( $update_fields );

						return ['fileUrl' => wp_get_attachment_image_url( $attachment_id, 'medium' )];
					}

					throw new WP_Error( 'upload_failed', __( 'The file could not be uploaded.', 'rise' ) );
				},
			]
		);

		/**
		 * Update a user's bookmarked profiles.
		 */
		register_graphql_mutation(
			'updateBookmarkedProfiles',
			[
				'inputFields'         => [
					'loggedInId'         => [
						'type'        => 'Int',
						'description' => __( 'The currently logged in user ID.', 'rise' ),
					],
					'bookmarkedProfiles' => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The updated list of bookmarked profiles.', 'rise' ),
					],
				],
				'outputFields'        => [
					'viewer' => [
						'type'        => 'User',
						'description' => __( 'The updated viewer field.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// TODO Security check. Check if user is logged in.

					$pod = pods( 'user', $input['loggedInId'] );

					$bookmarked_profiles = $pod->field( 'bookmarked_profile_connections' );

					// If the bookmarked profiles field is not set, make it an array.
					if ( !is_array( $bookmarked_profiles ) ) {
						$bookmarked_profiles = [];
					}

					$bookmarked_profile_ids = rise_pluck_profile_ids( $bookmarked_profiles );

					if ( !isset( $input['bookmarkedProfiles'] ) ) {
						throw new WP_Error( 'no_profile_id', __( 'No profile IDs provided.', 'rise' ) );
					}

					// Sanitize the input.
					$bookmarked_profile_ids = array_map( 'absint', $input['bookmarkedProfiles'] );

					// Update the bookmarked profiles field with the new array.
					$pod_id = $pod->save( [
						'bookmarked_profile_connections' => $bookmarked_profile_ids,
					] );

					// Get the updated bookmarked profile IDs.
					$updated_profile_ids = rise_pluck_profile_ids( $pod->field( 'bookmarked_profile_connections' ) );

					if ( $pod_id ) {
						return [
							'updatedBookmarkedProfiles' => $updated_profile_ids,
						];
					} else {
						throw new WP_Error( 'bookmarked_profile_toggle_failed', __( 'The profile could not be toggled.', 'rise' ) );
					}
				},
			],
		);
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
	private static function get_password_reset_email_message( $user_data, $key ) {
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
	private static function get_password_reset_email_subject( $user_data ) {
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
	 * Get the message body of the changed password alert email
	 *
	 * @since 1.0.3
	 *
	 * @param  WP_User $user_data User data
	 * @return string  Message body
	 */
	private static function get_password_change_email_message( $user_data ) {
		$first_name = get_user_meta( $user_data->ID, 'first_name', true );

		$message = __( 'Hi', 'rise' ) . ' ' . esc_html( $first_name ) . "\r\n\r\n";
		/* translators: %s: site name */
		$message .= sprintf( __( 'This notice confirms that your password was changed on: %s', 'rise' ), rise_get_email_friendly_site_name() ) . "\r\n\r\n";
		/* translators: %s: user login */
		$message .= sprintf( __( 'If you did not change your password, please contact us at %s', 'rise' ), get_option( 'admin_email' ) ) . "\r\n\r\n";
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
	private static function get_password_change_email_subject() {
		/* translators: Password reset email subject. %s: Site name */
		return sprintf( __( '[%s] Password Changed', 'rise' ), rise_get_email_friendly_site_name() );
	}

	/**
	 * Was a username or email address provided?
	 *
	 * @source wp-graphql/src/Mutation/SendPasswordResetEmail.php Original source
	 * @since 1.0.0
	 *
	 * @param  array  $input The input args.
	 * @return bool
	 */
	private static function was_username_provided( $input ) {
		return !empty( $input['username'] ) && is_string( $input['username'] );
	}
}
