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
		$this->register_mutation__deleteOwnConflictRange();
		$this->register_mutation__deleteOwnAccount();
		$this->register_mutation__uploadFile();
		$this->register_mutation__updateStarredProfiles();
		$this->register_mutation__updateOrCreateSavedSearch();
		$this->register_mutation__updateOrCreateConflictRange();
		$this->register_mutation__updateOrCreateJobPost();
		$this->register_mutation__toggleUserOption( 'toggleDisableProfile', 'disable_profile', 'updatedDisableProfile' );
		$this->register_mutation__markNotificationAsRead();
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
					$input_data = array_merge( $input_data, ['nicename' => rise_generate_default_user_slug( $input['firstName'], $input['lastName'] )] );

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
						// TODO Implement 'rememberMe'
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

					return [
						'status' => 'SUCCESS',
					];
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

					// We obfuscate the actual success of this mutation to prevent user enumeration.
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
					// We obfuscate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'success' => false,
					];

					if ( !$input['username'] || !$input['password'] || !$input['newEmail'] ) {
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
							'user_email' => sanitize_email( $input['newEmail'] ),
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
					// We obfuscate the actual success of this mutation to prevent user enumeration.
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

					// We obfuscate the actual success of this mutation to prevent user enumeration.
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
	 * Delete a user's Conflictable Date Range.
	 *
	 * @return void
	 */
	protected function register_mutation__deleteOwnConflictRange() {
		register_graphql_mutation(
			'deleteOwnConflictRange',
			[
				'inputFields'         => [
					'id'     => [
						'type'        => 'ID',
						'description' => __( 'The ID of the conflict date range to delete.', 'rise' ),
					],
					'userId' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the user to delete the conflict date range for.', 'rise' ),
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

						// TODO Upload file error handling.
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
				'mutateAndGetPayload' => function ( $input ) use ( $field_name, $updated_field ) {
					// TODO Security check. Check if user is logged in.

					$pod = pods( 'user', $input['userId'] );

					// TODO Error handling.
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
	 * Update a user's starred profiles.
	 *
	 * @return void
	 */
	protected function register_mutation__updateStarredProfiles() {
		register_graphql_mutation(
			'updateStarredProfiles',
			[
				'inputFields'         => [
					'toggledId' => [
						'type'        => 'Int',
						'description' => __( 'The updated list of starred profiles.', 'rise' ),
					],
				],
				'outputFields'        => [
					'starredProfiles' => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The updated list of starred profiles.', 'rise' ),
					],
					'toggledId'       => [
						'type'        => 'Int',
						'description' => __( 'The id of the item that was added or removed.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					$pod = pods( 'user', get_current_user_id() );

					$current_starred_profiles = $pod->field( 'starred_profiles' );

					// If the starred profiles field is not set, make it an array.
					if ( !is_array( $current_starred_profiles ) ) {
						$current_starred_profiles = [];
					}

					// Get the current starred IDs.
					$current_starred_ids = rise_pluck_profile_ids( $current_starred_profiles );

					// Update the collection.
					$updated_starred_ids = toggle_id_in_array( $current_starred_ids, $input['toggledId'] );

					// Sanitize the input.
					$updated_starred_ids = array_map( 'absint', $updated_starred_ids );

					// Update the starred profiles field with the new array.
					$pod_id = $pod->save( [
						'starred_profiles' => $updated_starred_ids,
					] );

					if ( $pod_id ) {
						return [
							'starredProfiles' => $updated_starred_ids,
							'toggledId'       => $input['toggledId'],
						];
					}

					throw new WP_Error( esc_html( 'starred_profile_toggle_failed' ), esc_html__( 'The profile could not be toggled.', 'rise' ) );
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
						'type'        => 'QueryableSearchFilterSet',
						'description' => __( 'The search filter set to save.', 'rise' ),
					],
					'title'     => [
						'description' => __( 'The user-generated title.', 'rise' ),
						'type'        => 'String',
					],
				],
				'outputFields'        => [
					'id' => [
						'type'        => 'Int',
						'description' => __( 'The ID of the saved search item.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// We obfuscate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'id' => 0,
					];

					if ( !isset( $input['filterSet'] ) || !$input['userId'] ) {
						return $payload;
					}

					$saved_search_id = isset( $input['id'] ) && $input['id'] ? $input['id'] : 0;

					$result = Rise_Users::update_saved_search( $input['userId'], $input['filterSet'], $input['title'], $saved_search_id );

					if ( is_wp_error( $result ) ) {
						throw new UserError( esc_html( $result->get_error_code() ) );
					}

					$payload['id'] = $result;

					return $payload;
				},
			]
		);
	}

	/**
	 * Save an Conflictable Date Range.
	 *
	 * @return void
	 */
	protected function register_mutation__updateOrCreateConflictRange() {
		register_graphql_mutation(
			'updateOrCreateConflictRange',
			[
				'inputFields'         => [
					'id'        => [
						'description' => __( 'The ID of the conflict_range, if it already exists.', 'rise' ),
						'type'        => 'ID',
					],
					'userId'    => [
						'description' => __( 'The ID of the user.', 'rise' ),
						'type'        => 'ID',
					],
					'startDate' => [
						'type'        => 'String',
						'description' => __( 'The range start date.', 'rise' ),
					],
					'endDate'   => [
						'description' => __( 'The range end date.', 'rise' ),
						'type'        => 'String',
					],
				],
				'outputFields'        => [
					'id' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the conflict_range item.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// We obfuscate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'id' => 0,
					];

					$conflict_range_id = isset( $input['id'] ) && $input['id'] ? $input['id'] : 0;

					$result = Rise_Users::update_conflict_range( $input['userId'], $input['startDate'], $input['endDate'], $conflict_range_id );

					if ( false === $result ) {
						throw new UserError( esc_html( 'update_pod_failed' ) );
					} elseif ( !$result ) {
						throw new UserError( esc_html( 'pod_error_conflict_range' ) );
					}

					$payload['id'] = $result;

					return $payload;
				},
			]
		);
	}

	protected function register_mutation__deleteOwnAccount() {
		register_graphql_mutation(
			'deleteOwnAccount',
			[
				'inputFields'         => [
					'userId' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the user to delete.', 'rise' ),
					],
				],
				'outputFields'        => [
					'result' => [
						'type'        => 'Boolean',
						'description' => __( 'The result of the delete operation.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// Make sure the current user id matches the user id in the input.
					if ( get_current_user_id() !== absint( $input['userId'] ) ) {
						throw new UserError( 'user_not_authorized' );
					}

					$user_id = isset( $input['userId'] ) ? $input['userId'] : 0;

					if ( !$user_id ) {
						throw new UserError( esc_html( 'no_user_id' ) );
					}

					/**
					 * @link https://developer.wordpress.org/reference/functions/wp_delete_user/
					 */
					require_once ( ABSPATH . 'wp-admin/includes/user.php' );
					$result = wp_delete_user( $user_id );

					if ( !$result ) {
						throw new UserError( esc_html( 'delete_user_failed' ) );
					}

					return [
						'result' => true,
					];
				},
			]
		);
	}

	protected function register_mutation__updateOrCreateJobPost() {
		register_graphql_mutation(
			'updateOrCreateJobPost',
			[
				'inputFields'         => [
					'id'               => [
						'type'        => 'Int',
						'description' => __( 'The ID of the job post, if it already exists.', 'rise' ),
					],
					'author'           => [
						'type'        => 'Int',
						'description' => __( 'The ID of the author of the job post, if it already exists.', 'rise' ),
					],
					'title'            => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The title of the job post.', 'rise' ),
					],
					'companyName'      => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The name of the company.', 'rise' ),
					],
					'companyAddress'   => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The address of the company.', 'rise' ),
					],
					'contactName'      => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The name of the contact person.', 'rise' ),
					],
					'contactEmail'     => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The email of the contact person.', 'rise' ),
					],
					'contactPhone'     => [
						'type'        => 'String',
						'description' => __( 'The phone number of the contact person.', 'rise' ),
					],
					'startDate'        => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The start date of the job.', 'rise' ),
					],
					'endDate'          => [
						'type'        => 'String',
						'description' => __( 'The end date of the job.', 'rise' ),
					],
					'instructions'     => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'The application instructions.', 'rise' ),
					],
					'compensation'     => [
						'type'        => 'String',
						'description' => __( 'The compensation details.', 'rise' ),
					],
					'applicationUrl'   => [
						'type'        => 'String',
						'description' => __( 'The URL for applications.', 'rise' ),
					],
					'applicationPhone' => [
						'type'        => 'String',
						'description' => __( 'The phone number for applications.', 'rise' ),
					],
					'applicationEmail' => [
						'type'        => 'String',
						'description' => __( 'The email for applications.', 'rise' ),
					],
					'description'      => [
						'type'        => 'String',
						'description' => __( 'The job description.', 'rise' ),
					],
					'isPaid'           => [
						'type'        => 'Boolean',
						'description' => __( 'Whether this is a paid position.', 'rise' ),
					],
					'isInternship'     => [
						'type'        => 'Boolean',
						'description' => __( 'Whether this is an internship position.', 'rise' ),
					],
					'isUnion'          => [
						'type'        => 'Boolean',
						'description' => __( 'Whether this is a union position.', 'rise' ),
					],
				],
				'outputFields'        => [
					'jobPost' => [
						'type'        => 'JobPostOutput',
						'description' => __( 'The created or updated job post.', 'rise' ),
						'resolve'     => function ( $payload ) {
							if ( !$payload['jobPost'] ) {
								return null;
							}

							$post_id = $payload['jobPost'];

							$job_post = new Rise_Job_Post( [
								'id'               => $post_id,
								'author'           => get_post_field( 'post_author', $post_id ),
								'isNew'            => false,
								'title'            => get_the_title( $post_id ),
								'companyName'      => get_post_meta( $post_id, 'company_name', true ),
								'companyAddress'   => get_post_meta( $post_id, 'company_address', true ),
								'contactName'      => get_post_meta( $post_id, 'contact_name', true ),
								'contactEmail'     => get_post_meta( $post_id, 'contact_email', true ),
								'contactPhone'     => get_post_meta( $post_id, 'contact_phone', true ),
								'startDate'        => get_post_meta( $post_id, 'start_date', true ),
								'endDate'          => get_post_meta( $post_id, 'end_date', true ),
								'instructions'     => get_post_meta( $post_id, 'instructions', true ),
								'compensation'     => get_post_meta( $post_id, 'compensation', true ),
								'applicationUrl'   => get_post_meta( $post_id, 'application_url', true ),
								'applicationPhone' => get_post_meta( $post_id, 'application_phone', true ),
								'applicationEmail' => get_post_meta( $post_id, 'application_email', true ),
								'description'      => get_post_meta( $post_id, 'description', true ),
								'isPaid'           => (bool) get_post_meta( $post_id, 'is_paid', true ),
								'isInternship'     => (bool) get_post_meta( $post_id, 'is_internship', true ),
								'isUnion'          => (bool) get_post_meta( $post_id, 'is_union', true ),
							] );

							return $job_post->prepare_job_post_for_graphql();
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// We obfuscate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'jobPost' => null,
					];

					// TODO Ultimately, set new posts to 'pending' and approve them in the admin.
					$job_post_defaults          = [];
					$job_post_defaults['isNew'] = !isset( $input['id'] ) || !$input['id'];

					if ( $job_post_defaults['isNew'] ) {
						// Only force the status if the job post is new.
						$job_post_defaults['status'] = 'pending';
					}

					// Create a new job post object
					$job_post = new Rise_Job_Post( array_merge( $input, $job_post_defaults ) );

					// Update the job post
					$result = $job_post->update_job_post();

					if ( is_wp_error( $result ) ) {
						throw new UserError( esc_html( $result->get_error_message() ) );
					}

					$payload['jobPost'] = $result;

					return $payload;
				},
			]
		);
	}

	/**
	 * Register the markNotificationAsRead mutation.
	 *
	 * @return void
	 */
	private function register_mutation__markNotificationAsRead() {
		register_graphql_mutation(
			'markNotificationAsRead',
			[
				'inputFields'         => [
					'notificationId' => [
						'type'        => ['non_null' => 'ID'],
						'description' => __( 'The ID of the notification to mark as read.', 'rise' ),
					],
				],
				'outputFields'        => [
					'success' => [
						'type'        => ['non_null' => 'Boolean'],
						'description' => __( 'Whether the notification was marked as read.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function( $input ) {
					$notification_id = $input['notificationId'];
					$user_id = get_current_user_id();

					if ( ! $user_id ) {
						return [
							'success' => false,
						];
					}

					// Verify the notification belongs to the current user
					$notification = get_post( $notification_id );
					if ( ! $notification || $notification->post_author != $user_id ) {
						return [
							'success' => false,
						];
					}

					$success = Rise_Profile_Notification::mark_notification_as_read( $notification_id );

					return [
						'success' => $success,
					];
				},
			]
		);
	}
}
