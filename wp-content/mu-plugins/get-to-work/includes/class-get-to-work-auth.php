<?php
/**
 * Hooks and filters for JWT authentication.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @link       https://roundhouse-designs.com
 * @since      0.2.0
 */

/**
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Get_To_Work_Auth {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1.0
	 */
	public function register_jwt_hooks() {
		$this->graphql_jwt_auth_expire();
	}

	/**
	 * Set JWT expiry.
	 *
	 * @return int JWT expiration in seconds.
	 */
	public function graphql_jwt_auth_expire() {
		if ( defined( 'GRAPHQL_JWT_AUTH_EXPIRE' ) ) {
			return GRAPHQL_JWT_AUTH_EXPIRE;
		} else {
			return 300;
		}
	}
}
