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

	public function register_mutations() {
		$this->register_login_mutation();
		$this->register_logout_mutation();
	}

	/**
	 * Login mutation with HTTP Cookies.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function register_login_mutation() {
		register_graphql_mutation(
			'login',
			[
				'inputFields'         => [
					'login'    => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'Input your username/email.' ),
					],
					'password' => [
						'type'        => ['non_null' => 'String'],
						'description' => __( 'Input your password.' ),
					],
				],
				'outputFields'        => [
					'status'    => [
						'type'        => 'String',
						'description' => __( 'Login operation status', 'gtw' ),
						'resolve'     => function ( $payload ) {
							return $payload['status'];
						},
					],
					'id'        => [
						'type'        => 'String',
						'description' => __( 'User ID', 'gtw' ),
						'resolve'     => function ( $payload ) {
							return $payload['id'];
						},
					],
					'firstName' => [
						'type'        => 'String',
						'description' => __( 'First Name', 'gtw' ),
						'resolve'     => function ( $payload ) {
							return $payload['firstName'];
						},
					],
					'lastName'  => [
						'type'        => 'String',
						'description' => __( 'Last Name', 'gtw' ),
						'resolve'     => function ( $payload ) {
							return $payload['lastName'];
						},
					],
				],
				'mutateAndGetPayload' => function ( $input ) {
					$user = wp_signon(
						[
							'user_login'    => wp_unslash( $input['login'] ),
							'user_password' => $input['password'],
							'remember'      => true,
						],
						true
					);

					if ( is_wp_error( $user ) ) {
						throw new \GraphQL\Error\UserError( ! empty( $user->get_error_code() ) ? $user->get_error_code() : __( 'Invalid login', 'gtw' ) );
					}

					$userdata = get_userdata( $user->ID );

					return [
						'status'    => 'SUCCESS',
						'id'        => $user->ID,
						'firstName' => $userdata->first_name ? $userdata->first_name : '',
						'lastName'  => $userdata->last_name ? $userdata->last_name : '',
					];
				},
			]
		);
	}

	/**
	 * Logout mutation.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function register_logout_mutation() {
		register_graphql_mutation(
			'logout',
			[
				'inputFields'         => [],
				'outputFields'        => [
					'status' => [
						'type'        => 'String',
						'description' => __( 'Logout result', 'gtw' ),
						'resolve'     => function ( $payload ) {
							return $payload['status'];
						},
					],
				],
				'mutateAndGetPayload' => function () {
					wp_logout(); // This destroys the WP Login cookie.
					return ['status' => 'SUCCESS'];
				},
			]
		);
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

		// If the request is coming from an allowed origin, tell the browser it can accept the response.
		if ( in_array( $http_origin, $this->allowed_origins, true ) ) {
			$headers['Access-Control-Allow-Origin'] = $http_origin;
		}

		// Tells browsers to expose the response to frontend JavaScript code when the request credentials mode is "include".
		$headers['Access-Control-Allow-Credentials'] = 'true';

		return $headers;
	}
}
