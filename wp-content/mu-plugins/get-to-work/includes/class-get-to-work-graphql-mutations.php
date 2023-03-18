<?php
/**
 * Registers GraphQL mutations.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Get_To_Work_GraphQL_Mutations {
	/**
	 * The allowed origins for CORS.
	 *
	 * @var array $allowed_origins The allowed origins for CORS.
	 */
	protected $allowed_origins;

	/**
	 * Get_To_Work_GraphQL_Mutations constructor.
	 *
	 * @param array $allowed_origins
	 */
	public function __construct( $allowed_origins = [] ) {
		$this->allowed_origins = $allowed_origins;
	}

	/**
	 * Set CORS to allow frontend logins
	 *
	 * @since 0.0.1
	 *
	 * @param  array $headers The HTTP headers present.
	 * @return array The modified headers.
	 */
	public function response_headers_to_send( $headers ) {
		$http_origin = get_http_origin();

		if ( in_array( $http_origin, $this->allowed_origins, true ) ) {
			// If the request is coming from an allowed origin, tell the browser it can accept the response.
			$headers['Access-Control-Allow-Origin'] = $http_origin;
		}

		// Tells browsers to expose the response to frontend JavaScript code when the request credentials mode is "include".
		$headers['Access-Control-Allow-Credentials'] = 'true';

		return $headers;
	}

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
						'description' => __( 'Profile data to update.', 'gtw' ),
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
							'result' => new \WP_Error( 'no_id', __( 'No ID provided.', 'gtw' ) ),
						];
					}

					$user = new Get_To_Work_UserProfile( $input['profile'] );

					$result = $user->update_user_profile();

					// TODO maybe return a WP_Error object instead of 0.
					return [
						'result' => ! is_wp_error( $result ) ? $result : 0,
					];
				},
			],
		);
	}

}
