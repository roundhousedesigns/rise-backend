<?php

namespace RHD\Rise\Includes;

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
use RHD\Rise\Core\Utils;
use RHD\Rise\Includes\Credit;
use RHD\Rise\Includes\JobPost;
use RHD\Rise\Includes\ProfileNotification;
use RHD\Rise\Includes\UserProfile;
use RHD\Rise\Includes\Users;
use WP_Error;

class GraphQLMutations {
	/**
	 * Run the registrations.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_mutations() {
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
		$this->register_mutation__toggleUserOption( 'toggleIsOrg', 'is_org', 'updatedIsOrg' );
		$this->register_mutation__markProfileNotificationAsRead();
		$this->register_mutation__dismissProfileNotification();
	}

	/**
	 * Change a user's email address.
	 *
	 * @return void
	 */
	protected function register_mutation__changeEmail() {
		\register_graphql_mutation(
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
					$user = \wp_authenticate( $input['username'], $input['password'] );

					if ( \is_wp_error( $user ) ) {
						throw new UserError( esc_html( $user->get_error_code() ) );
					}

					// Update the user's password.
					\wp_update_user(
						[
							'ID'         => $user->ID,
							'user_email' => sanitize_email( $input['newEmail'] ),
						]
					);

					// Send the confirmation email
					$message = Email::get_email_change_email_message( $user );
					$subject = Email::get_email_change_email_subject();

					// TODO verify that change password notices are sending
					$email_sent = wp_mail(  // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
						$user->user_email,
						wp_specialchars_decode( $subject ),
						$message
					);

					// wp_mail can return a wp_error, but the docblock for it in WP Core is incorrect.
					// phpstan should ignore this check.
					// @phpstan-ignore-next-line
					if ( \is_wp_error( $email_sent ) ) {
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
		\register_graphql_mutation(
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
					$user = \wp_authenticate( $input['username'], $input['currentPassword'] );

					if ( \is_wp_error( $user ) ) {
						throw new UserError( esc_html( $user->get_error_code() ) );
					}

					// Update the user's password.
					\wp_set_password( $input['newPassword'], $user->ID );

					// Send the confirmation email
					$message = Email::get_password_change_email_message( $user );
					$subject = Email::get_password_change_email_subject();

					// TODO verify that change password notices are sending
					$email_sent = \wp_mail(  // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
						$user->user_email,
						wp_specialchars_decode( $subject ),
						$message
					);

					// wp_mail can return a wp_error, but the docblock for it in WP Core is incorrect.
					// phpstan should ignore this check.
					// @phpstan-ignore-next-line
					if ( \is_wp_error( $email_sent ) ) {
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
		\register_graphql_mutation(
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
					if ( \get_current_user_id() !== $user->ID ) {
						throw new UserError( 'user_not_authorized' );
					}

					// If the new slug matches the current slug, just return success.
					if ( $user->user_nicename === $input['newSlug'] ) {
						$payload['success'] = true;

						return $payload;
					}

					// Make sure the new slug is unique
					$existing_slug_user = \get_user_by( 'slug', $input['newSlug'] );

					if ( $existing_slug_user ) {
						throw new UserError( 'user_slug_not_unique' );
					}

					// Update the user's slug
					$user->user_nicename = sanitize_title( $input['newSlug'] );
					$result              = \wp_update_user( $user );

					if ( \is_wp_error( $result ) ) {
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
		\register_graphql_mutation(
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

					$user = new UserProfile( $input['profile'] );

					$result = $user->update_user_profile();

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'result' => !\is_wp_error( $result ) ? $result : 0,
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
		\register_graphql_mutation(
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

					$user = new UserProfile( ['id' => $user_id] );

					$result = $user->clear_profile_field( $field_name );

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'result' => !\is_wp_error( $result ) ? $result : 0,
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
		\register_graphql_mutation(
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

					$credit = new Credit( $input['credit'] );
					$result = $credit->update_credit();

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'updatedCredit' => !\is_wp_error( $result ) ? $credit->prepare_credit_for_graphql() : 0,
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
		\register_graphql_mutation(
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
						$credit   = new Credit( ['id' => absint( $id )] );
						$result[] = $credit->update_index( $index );
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
		\register_graphql_mutation(
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

					$result = Users::delete_own_allowed_post_item( $input['id'], $input['userId'] );

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
		\register_graphql_mutation(
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

					$result = Users::delete_own_allowed_post_item( $input['id'], $input['userId'] );

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
		\register_graphql_mutation(
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

					$result = Users::delete_own_allowed_post_item( $input['id'], $input['userId'] );

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
		\register_graphql_mutation(
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

					$field   = isset( $input['name'] ) ? Utils::camel_to_snake( $input['name'] ) : '';
					$user_id = isset( $input['userId'] ) ? $input['userId'] : null;

					$uploaded = \wp_handle_sideload( $input['file'], [
						'test_form' => false,
						'test_type' => true,
					] );

					$uploaded['file'] = Utils::maybe_strip_exif( $uploaded['file'] );

					// Get the attachment ID from the uploaded file.
					$attachment_id = Utils::get_attachment_id_by_url( $uploaded['url'] );

					// Set the user's profile image.
					if ( $attachment_id && $user_id ) {
						$pod = \pods( 'user', $user_id );

						$update_fields = [$field => $attachment_id];

						// TODO Upload file error handling.
						$pod->save( $update_fields );

						return ['fileUrl' => \wp_get_attachment_image_url( $attachment_id, 'medium' )];
					}

					throw new WP_Error( 'upload_failed', \esc_html__( 'The file could not be uploaded.', 'rise' ) );
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
		\register_graphql_mutation(
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
		\register_graphql_mutation(
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
					$current_starred_ids = Users::pluck_profile_ids( $current_starred_profiles );

					// Update the collection.
					$updated_starred_ids = Utils::toggle_id_in_array( $current_starred_ids, $input['toggledId'] );

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
		\register_graphql_mutation(
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

					$result = Users::update_saved_search( $input['userId'], $input['filterSet'], $input['title'], $saved_search_id );

					if ( \is_wp_error( $result ) ) {
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
		\register_graphql_mutation(
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

					$result = Users::update_conflict_range( $input['userId'], $input['startDate'], $input['endDate'], $conflict_range_id );

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
		\register_graphql_mutation(
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
		\register_graphql_mutation(
			'updateOrCreateJobPost',
			[
				'inputFields'         => [
					'jobPost' => [
						'type'        => 'JobPostInput',
						'description' => __( 'The job post data to insert.', 'rise' ),
					],
				],
				'outputFields'        => [
					'updatedJobPost'     => [
						'type'        => 'JobPostOutput',
						'description' => __( 'The updated job post data.', 'rise' ),
						'resolve'     => function ( $payload ) {
							return $payload['updatedJobPost'];
						},
					],
					'awaitingPayment'    => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the job post is awaiting payment.', 'rise' ),
					],
					'wcCheckoutEndpoint' => [
						'type'        => 'String',
						'description' => __( 'The WooCommerce cart endpoint.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					// We obfuscate the actual success of this mutation to prevent user enumeration.
					$payload = [
						'updatedJobPost'     => null,
						'awaitingPayment'    => false,
						'wcCheckoutEndpoint' => \esc_url_raw( \wc_get_checkout_url() ),
					];

					if ( !isset( $input['jobPost'] ) ) {
						return [
							'updatedJobPost' => new \WP_Error( 'no_job_post_data', __( 'No job post data provided.', 'rise' ) ),
						];
					}

					$job_input_data = $input['jobPost'];

					$job_post_product_id = 15695;

					// Exit early if WooCommerce is not active.
					if ( !class_exists( 'WC_Session' ) ) {
						throw new \Error( 'WooCommerce is not active.' );
					}

					$job_post_defaults = [
						'post_author' => get_current_user_id(),
					];
					$job_post_defaults['isNew'] = !isset( $job_input_data['id'] ) || !$job_input_data['id'];

					if ( $job_post_defaults['isNew'] ) {
						// Only force the status if the job post is new.
						$job_post_defaults['status'] = 'pending';
					}

					// Create a new job post object
					$job_post = new JobPost( array_merge( $job_input_data, $job_post_defaults ) );

					// Store the job post in session so we can post it after payment is complete.
					if ( $job_post_defaults['isNew'] ) {
						WC()->session->set( 'new_job_post_awaiting_payment', $job_post );

						$payload['updatedJobPost']  = $job_post->prepare_job_post_for_graphql();
						$payload['awaitingPayment'] = true;

						// Empty the cart and add the job post product to it.
						WC()->cart->empty_cart();
						WC()->cart->add_to_cart( $job_post_product_id );
					} else {
						// Update the job post if it already exists.
						$result = $job_post->update_job_post();

						if ( \is_wp_error( $result ) ) {
							throw new UserError( esc_html( $result->get_error_message() ) );
						}

						$payload['updatedJobPost']  = $job_post->prepare_job_post_for_graphql();
						$payload['awaitingPayment'] = false;
					}

					return $payload;
				},
			]
		);
	}

	/**
	 * Register the markProfileNotificationAsRead mutation.
	 *
	 * @return void
	 */
	private function register_mutation__markProfileNotificationAsRead() {
		\register_graphql_mutation(
			'markProfileNotificationAsRead',
			[
				'inputFields'         => [
					'id' => [
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
				'mutateAndGetPayload' => function ( $input ) {
					$notification_id = $input['id'];
					$user_id         = get_current_user_id();

					if ( !$user_id ) {
						return [
							'success' => false,
						];
					}

					// Verify the notification belongs to the current user
					$notification_author_id = absint( get_post_field( 'post_author', $notification_id ) );

					if ( !$notification_author_id || $notification_author_id !== $user_id ) {
						throw new UserError( 'You are not authorized to mark this notification as read.' );
					}

					$success = ProfileNotification::mark_notification_as_read( $notification_id );

					return [
						'success' => $success,
					];
				},
			]
		);
	}

	/**
	 * Register the dismissProfileNotification mutation.
	 *
	 * @return void
	 */
	private function register_mutation__dismissProfileNotification() {
		\register_graphql_mutation(
			'dismissProfileNotification',
			[
				'inputFields'         => [
					'id' => [
						'type'        => ['non_null' => 'ID'],
						'description' => __( 'The ID of the notification to dismiss.', 'rise' ),
					],
				],
				'outputFields'        => [
					'success' => [
						'type'        => ['non_null' => 'Boolean'],
						'description' => __( 'Whether the notification was dismissed.', 'rise' ),
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					$notification_id = $input['id'];
					$user_id         = get_current_user_id();
					$author_id       = absint( get_post_field( 'post_author', $notification_id ) );

					if ( !$user_id || $author_id !== $user_id ) {
						throw new UserError( 'You are not authorized to dismiss this notification.' );
					}

					$success = ProfileNotification::dismiss_notification( $notification_id );

					return [
						'success' => $success,
					];
				},
			]
		);
	}

	/**
	 * Add the isOrg field to the RegisterUserInput type.
	 *
	 * @return void
	 */
	public function add_fields_to_registerUser() {
		register_graphql_field( 'RegisterUserInput', 'isOrg', [
			'type'        => 'Boolean',
			'description' => __( 'Whether the user is registering as an organization', 'rise' ),
		] );

		register_graphql_field( 'RegisterUserInput', 'orgName', [
			'type'        => 'String',
			'description' => __( 'The name of the organization.', 'rise' ),
		] );
	}

	/**
	 * Handle organization-related fields during user registration.
	 *
	 * @param  int    $user_id       The ID of the user being created/updated.
	 * @param  array  $input         The input data for the mutation.
	 * @param  string $mutation_name The name of the mutation being executed.
	 * @param  mixed  $context       The application context.
	 * @param  mixed  $info          The resolve info.
	 * @return void
	 */
	public function handle_registerUser_org_fields( $user_id, $input, $mutation_name, $context, $info ) {
		// Only handle registerUser mutation
		if ( 'registerUser' !== $mutation_name ) {
			return;
		}

		// Prepare fields to save
		$fields_to_save = [];

		// Check if isOrg field is provided in the input
		if ( isset( $input['isOrg'] ) ) {
			$fields_to_save['is_org'] = (bool) $input['isOrg'];
		}

		// Check if orgName field is provided in the input
		if ( isset( $input['orgName'] ) ) {
			$fields_to_save['org_name'] = sanitize_text_field( $input['orgName'] );
		}

		// Save the fields if any are provided
		if ( !empty( $fields_to_save ) ) {
			$pod = pods( 'user', $user_id );
			if ( $pod ) {
				$pod->save( $fields_to_save );
			}
		}
	}
}
