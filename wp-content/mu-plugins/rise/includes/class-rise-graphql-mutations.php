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
					if ( ! isset( $input['profile']['id'] ) ) {
						return [
							'result' => new \WP_Error( 'no_id', __( 'No ID provided.', 'rise' ) ),
						];
					}

					$user = new Rise_UserProfile( $input['profile'] );

					$result = $user->update_user_profile();

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'result' => ! is_wp_error( $result ) ? $result : 0,
					];
				},
			],
		);

		/**
		 * Update a user's profile.
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

					if ( ! isset( $input['credit'] ) ) {
						return [
							'updatedCredit' => new \WP_Error( 'no_id', __( 'No ID provided.', 'rise' ) ),
						];
					}

					$credit = new Rise_Credit( $input['credit'] );
					$result = $credit->update_credit();

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'updatedCredit' => ! is_wp_error( $result ) ? $credit->prepare_credit_for_graphql() : 0,
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

					if ( ! isset( $input['creditIds'] ) ) {
						return [
							'creditIds' => new \WP_Error( 'no_id', __( 'No IDs provided.', 'rise' ) ),
						];
					}

					$result = [];
					foreach ( $input['creditIds'] as $index => $id ) {
						$result[] = update_credit_index( absint( $id ), $index );
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

					if ( ! isset( $input['id'] ) ) {
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
					// TODO check if this is necessary
					if ( ! function_exists( 'wp_handle_sideload' ) ) {
						require_once ABSPATH . 'wp-admin/includes/file.php';
					}

					$field   = isset( $input['name'] ) ? camel_case_to_underscore( $input['name'] ) : '';
					$user_id = isset( $input['userId'] ) ? $input['userId'] : null;

					$uploaded = wp_handle_sideload( $input['file'], [
						'test_form' => false,
						'test_type' => true,
					] );

					// Get the attachment ID from the uploaded file.
					$attachment_id = get_attachment_id_by_url( $uploaded['url'] );

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
	}
}
