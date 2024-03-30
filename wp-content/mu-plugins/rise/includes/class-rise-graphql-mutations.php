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
		$this->register_mutation__registerUserWithReCaptcha();
		$this->register_mutation__loginWithCookiesAndReCaptcha();
		$this->register_mutation__sendPasswordResetEmailWithReCaptcha();
		$this->register_mutation__changeEmail();
		$this->register_mutation__changePassword();
		$this->register_mutation__changeProfileSlug();
		$this->register_mutation__updateProfile();
		$this->register_mutation__clearProfileField();
		$this->register_mutation__updateOrCreateCredit();
		$this->register_mutation__updateCreditOrder();
		$this->register_mutation__deleteOwnCredit();
		$this->register_mutation__deleteOwnSavedSearch();
		$this->register_mutation__uploadFile();
		$this->register_mutation__updateBookmarkedProfiles();
		$this->register_mutation__updateOrCreateSavedSearch();
		$this->register_mutation__toggleUserOption( 'toggleDisableProfile', 'disable_profile', 'updatedDisableProfile' );
		$this->register_mutation__toggleUserOption( 'toggleLookingForWork', 'looking_for_work', 'updatedLookingForWork' );
		$this->register_mutation__toggleUserOption( 'toggleIsOrg', 'is_org', 'updatedIsOrg' );
	}

	/**
	 * Create a user with reCAPTCHA verification.
	 *
	 * @return void
	 */
	protected function register_mutation__registerUserWithReCaptcha() {
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
						throw new UserError( esc_attr( 'no_recaptcha_token' ) );
					}

					/**
					 * Check the reCAPTCHA response
					 */
					if ( !recaptcha_is_valid( $input['reCaptchaToken'] ) ) {
						throw new UserError( esc_attr( 'bad_recaptcha_token' ) );
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
							throw new UserError( esc_html( $error_code ) );
						}

						throw new UserError( esc_attr( 'unspecified_create_user_error' ) );
					}

					/**
					 * If the $post_id is empty, we should throw an exception
					 */
					if ( empty( $user_id ) ) {
						throw new UserError( esc_attr( 'unspecified_create_user_error' ) );
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
	}

	/**
	 * Logs in a user with WordPress cookies.
	 *
	 * @return void
	 */
	protected function register_mutation__loginWithCookiesAndReCaptcha() {
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
						throw new UserError( esc_attr( 'no_recaptcha_token' ) );
					}

					/**
					 * Check the reCAPTCHA response
					 */
					if ( !recaptcha_is_valid( $input['reCaptchaToken'] ) ) {
						throw new UserError( esc_attr( 'bad_recaptcha_token' ) );
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

					// Verify that the user is retrievable.
					$user = get_user_by( 'email', $credentials['user_login'] );
					if ( !$user ) {
						throw new UserError( esc_attr( 'invalid_email' ) );
					}

					// Verify that the user's role is 'crew-member'.
					if ( in_array( 'administrator', $user->roles, true ) ) {
						throw new UserError( esc_attr( 'invalid_account' ) );
					}

					// Authenticate User.
					$user = wpgraphql_cors_signon( $credentials );

					if ( is_wp_error( $user ) ) {
						throw new UserError( esc_html( !empty( $user->get_error_code() ) ? $user->get_error_code() : 'bad_login' ) );
					}

					return ['status' => 'SUCCESS'];
				},
			]
		);
	}

	/**
	 * Send a password reset email to a user.
	 *
	 * @return void
	 */
	protected function register_mutation__sendPasswordResetEmailWithReCaptcha() {
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
					$username_provided = !empty( $input['username'] ) && is_string( $input['username'] );

					if ( !$username_provided ) {
						throw new UserError( esc_attr( 'no_username' ) );
					}

					if ( !isset( $input['reCaptchaToken'] ) || !$input['reCaptchaToken'] ) {
						throw new UserError( esc_attr( 'no_recaptcha_token' ) );
					}

					/**
					 * Check the reCAPTCHA response
					 */
					if ( !recaptcha_is_valid( $input['reCaptchaToken'] ) ) {
						throw new UserError( esc_attr( 'bad_recpatcha_response' ) );
					}

					// We obsfucate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'success' => false,
					];

					$user_data = get_user_by( 'email', $input['username'] );

					if ( !$user_data ) {
						throw new UserError( esc_attr( 'invalid_username' ) );
					}

					// Get the password reset key.
					$key = get_password_reset_key( $user_data );
					if ( is_wp_error( $key ) ) {
						throw new UserError( esc_attr( 'invalid_reset_key' ) );
					}

					// Mail the reset key.
					$subject = rise_get_password_reset_email_subject( $user_data );
					$message = rise_get_password_reset_email_message( $user_data, $key );

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
					}

					/**
					 * Return the ID of the user
					 */
					return [
						'success' => true,
					];
				},
			]
		);
	}

	/**
	 * Change a user's email address.
	 *
	 * @return void
	 */
	protected function register_mutation__changeEmail() {
		register_graphql_mutation(
			'changeEmail',
			[
				'inputFields'         => [
					'username' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The user\'s username or current email address', 'rise' ),
					],
					'password' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The user\'s password', 'rise' ),
					],
					'newEmail' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The user\'s new email address', 'rise' ),
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

					if ( !$input['username'] || !$input['password'] || !$input['newEmail'] || $input['username'] ) {
						// TODO throw error here?
						return $payload;
					}

					// Authenticate the user with their current password.
					$user = wp_authenticate( $input['username'], $input['password'] );

					if ( is_wp_error( $user ) ) {
						throw new UserError( esc_html( $user->get_error_code() ) );
					}

					// Update the user's password.
					wp_update_user(
						[
							'ID'         => $user->ID,
							'user_email' => $input['newEmail'],
						]
					);

					// Send the confirmation email
					$message = rise_get_email_change_email_message( $user );
					$subject = rise_get_email_change_email_subject();

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
	}

	/**
	 * Change a user's password.
	 *
	 * @return void
	 */
	protected function register_mutation__changePassword() {
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
						throw new UserError( esc_html( $user->get_error_code() ) );
					}

					// Update the user's password.
					wp_set_password( $input['newPassword'], $user->ID );

					// Send the confirmation email
					$message = rise_get_password_change_email_message( $user );
					$subject = rise_get_password_change_email_subject();

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
	}

	/**
	 * Change a user's profile slug.
	 *
	 * @return void
	 */
	protected function register_mutation__changeProfileSlug() {
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
						throw new UserError( esc_html( $result->get_error_code() ) );
					}

					return [
						'success' => true,
						'slug'    => $user->user_nicename,
					];
				},
			]
		);
	}

	/**
	 * Update a user's profile.
	 *
	 * @return void
	 */
	protected function register_mutation__updateProfile() {
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
	}

	/**
	 * Clear a user's specified profile field set up in Pods.
	 *
	 * @return void
	 */
	protected function register_mutation__clearProfileField() {
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
	}

	/**
	 * Update or create a Credit.
	 *
	 * @return void
	 */
	protected function register_mutation__updateOrCreateCredit() {
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
	}

	/**
	 * Update a user's credit order.
	 *
	 * @return void
	 */
	protected function register_mutation__updateCreditOrder() {
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
	}

	/**
	 * Delete a user's credit.
	 *
	 * @return void
	 */
	protected function register_mutation__deleteOwnCredit() {
		register_graphql_mutation(
			'deleteOwnCredit',
			[
				'inputFields'         => [
					'id'     => [
						'type'        => 'ID',
						'description' => __( 'The ID of the credit to delete.', 'rise' ),
					],
					'userId' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the user to delete the credit for.', 'rise' ),
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
					$payload = [
						'result' => false,
					];

					if ( !isset( $input['id'] ) ) {
						return [
							'result' => new \WP_Error( 'no_id', __( 'No ID provided.', 'rise' ) ),
						];
					}

					if ( !isset( $input['userId'] ) ) {
						return [
							'result' => new \WP_Error( 'no_user_id', __( 'No user ID provided.', 'rise' ) ),
						];
					}

					$result = rise_delete_own_allowed_post_item( $input['id'], $input['userId'] );

					if ( $result ) {
						$payload['result'] = true;
					}

					return $payload;
				},
			]
		);
	}

	/**
	 * Delete a user's saved search.
	 *
	 * @return void
	 */
	protected function register_mutation__deleteOwnSavedSearch() {
		register_graphql_mutation(
			'deleteOwnSavedSearch',
			[
				'inputFields'         => [
					'id'     => [
						'type'        => 'ID',
						'description' => __( 'The ID of the saved search to delete.', 'rise' ),
					],
					'userId' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the user to delete the saved search for.', 'rise' ),
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

					if ( !isset( $input['userId'] ) ) {
						return [
							'result' => new \WP_Error( 'no_user_id', __( 'No user ID provided.', 'rise' ) ),
						];
					}

					$result = rise_delete_own_allowed_post_item( $input['id'], $input['userId'] );

					if ( $result ) {
						$payload['result'] = true;
					}

					return $payload;
				},
			]
		);
	}

	/**
	 * Upload a file.
	 *
	 * @return void
	 */
	protected function register_mutation__uploadFile() {
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

					$field   = isset( $input['name'] ) ? camel_to_snake( $input['name'] ) : '';
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

						$update_fields = [$field => $attachment_id];

						$pod->save( $update_fields );

						return ['fileUrl' => wp_get_attachment_image_url( $attachment_id, 'medium' )];
					}

					throw new WP_Error( 'upload_failed', esc_html__( 'The file could not be uploaded.', 'rise' ) );
				},
			]
		);
	}

	/**
	 * Toggle a user option.
	 *
	 * @param  string $mutation_name The mutation name.
	 * @param  string $field_name    The Pods field name.
	 * @param  string $updated_field The Pods updated field name.
	 * @return void
	 */
	protected function register_mutation__toggleUserOption( $mutation_name, $field_name, $updated_field ) {
		register_graphql_mutation(
			$mutation_name,
			[
				'inputFields'         => [
					'userId' => [
						'type'        => ['non_null' => 'Int'],
						'description' => __( 'The user\'s ID.', 'rise' ),
					],
				],
				'outputFields'        => [
					$updated_field => [
						'type'        => 'Boolean',
						'description' => __( 'The updated value.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input, $updated ) use ( $field_name, $updated_field ) {
					// TODO Security check. Check if user is logged in.

					$pod = pods( 'user', $input['userId'] );

					$pod->save( [
						$field_name => $pod->field( $field_name ) ? false : true,
					] );

					return [
						$updated_field => $pod->field( $field_name ),
					];
				},
			]
		);
	}

	/**
	 * Update a user's bookmarked profiles.
	 *
	 * @return void
	 */
	protected function register_mutation__updateBookmarkedProfiles() {
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

					$bookmarked_ids = rise_pluck_profile_ids( $bookmarked_profiles );

					if ( !isset( $input['bookmarkedProfiles'] ) ) {
						throw new WP_Error( esc_html( 'no_profile_id' ), esc_html__( 'No profile IDs provided.', 'rise' ) );
					}

					// Sanitize the input.
					$bookmarked_ids = array_map( 'absint', $input['bookmarkedProfiles'] );

					// Update the bookmarked profiles field with the new array.
					$pod_id = $pod->save( [
						'bookmarked_profile_connections' => $bookmarked_ids,
					] );

					// Get the updated bookmarked profile IDs.
					$updated_profile_ids = rise_pluck_profile_ids( $pod->field( 'bookmarked_profile_connections' ) );

					if ( $pod_id ) {
						return [
							'updatedBookmarkedProfiles' => $updated_profile_ids,
						];
					}

					throw new WP_Error( esc_html( 'bookmarked_profile_toggle_failed' ), esc_html__( 'The profile could not be toggled.', 'rise' ) );
				},
			],
		);
	}

	/**
	 * Save a search filter set.
	 *
	 * @return void
	 */
	protected function register_mutation__updateOrCreateSavedSearch() {
		register_graphql_mutation(
			'updateOrCreateSavedSearch',
			[
				'inputFields'         => [
					'id'        => [
						'description' => __( 'The ID of the saved search, if it already exists.', 'rise' ),
						'type'        => 'ID',
					],
					'userId'    => [
						'description' => __( 'The ID of the user performing the search.', 'rise' ),
						'type'        => 'ID',
					],
					'filterSet' => [
						'type'        => 'SearchFilterSetRaw',
						'description' => __( 'The search filter set to save.', 'rise' ),
					],
					'title'     => [
						'description' => __( 'The user-generated title.', 'rise' ),
						'type'        => 'String',
					],
				],
				'outputFields'        => [
					'id' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the saved search item.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// We obsfucate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'success' => false,
					];

					if ( !isset( $input['filterSet'] ) || !$input['userId'] ) {
						return $payload;
					}

					$saved_search_id = isset( $input['id'] ) && $input['id'] ? $input['id'] : 0;

					$result = Rise_Users::update_saved_search( $input['userId'], $input['filterSet'], $input['title'], $saved_search_id );

					if ( is_wp_error( $result ) ) {
						throw new UserError( esc_html( $result->get_error_code() ) );
					}

					return [
						'success' => true,
					];
				},
			]
		);
	}
}
