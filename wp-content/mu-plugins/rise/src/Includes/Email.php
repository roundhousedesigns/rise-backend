<?php

namespace RHD\Rise\Includes;

/**
 * Email class.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.0.3
 */
class Email {
	/**
	 * Get the message body of the changed password alert email
	 *
	 * @since 1.0.3
	 *
	 * @param  WP_User $user_data User data
	 * @return string  Message body
	 */
	public static function get_password_change_email_message( $user_data ) {
		$first_name = get_user_meta( $user_data->ID, 'first_name', true );

		$message = __( 'Hi', 'rise' ) . ' ' . esc_html( $first_name ) . "\r\n\r\n";
		/* translators: %s: site name */
		$message .= sprintf( __( 'This notice confirms that your password was changed on: %s', 'rise' ), self::get_email_friendly_site_name() ) . "\r\n\r\n";
		/* translators: %s: user login */
		$message .= sprintf( __( 'If you did not change your password, please contact us immediately at %s', 'rise' ), get_option( 'admin_email' ) ) . "\r\n\r\n";
		$message .= sprintf( __( 'This email has been sent to %s', 'rise' ), $user_data->user_email ) . "\r\n\r\n";
		$message .= __( 'Thanks,', 'rise' ) . "\r\n\r\n";
		$message .= self::get_email_friendly_site_name() . "\r\n";
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
	public static function get_password_change_email_subject() {
		/* translators: Password reset email subject. %s: Site name */
		return sprintf( __( '[%s] Password Changed', 'rise' ), self::get_email_friendly_site_name() );
	}

/**
 * Get the message body of the changed email address alert email
 *
 * @since 1.0.3
 *
 * @param  WP_User $user_data User data
 * @return string  Message body
 */
	public static function get_email_change_email_message( $user_data ) {
		$first_name = get_user_meta( $user_data->ID, 'first_name', true );

		$message = __( 'Hi', 'rise' ) . ' ' . esc_html( $first_name ) . "\r\n\r\n";
		/* translators: %s: site name */
		$message .= sprintf( __( 'This notice confirms that your email was updated on: %s', 'rise' ), self::get_email_friendly_site_name() ) . "\r\n\r\n";
		/* translators: %s: user login */
		$message .= sprintf( __( 'If you did not change your email address, please contact us immediately at %s', 'rise' ), get_option( 'admin_email' ) ) . "\r\n\r\n";
		$message .= sprintf( __( 'This email has been sent to %s', 'rise' ), $user_data->user_email ) . "\r\n\r\n";
		$message .= __( 'Thanks,', 'rise' ) . "\r\n\r\n";
		$message .= self::get_email_friendly_site_name() . "\r\n";
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
	public static function get_email_change_email_subject() {
		/* translators: Password reset email subject. %s: Site name */
		return sprintf( __( '[%s] Email Changed', 'rise' ), self::get_email_friendly_site_name() );
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
	public static function get_password_reset_email_message( $user_data, $key ) {
		$message = __( 'Someone has requested a password reset for the following account:', 'rise' ) . "\r\n\r\n";
		/* translators: %s: site name */
		$message .= sprintf( __( 'Site Name: %s', 'rise' ), self::get_email_friendly_site_name() ) . "\r\n\r\n";
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
	public static function get_password_reset_email_subject( $user_data ) {
		/* translators: Password reset email subject. %s: Site name */
		$title = sprintf( __( '[%s] Password Reset', 'rise' ), self::get_email_friendly_site_name() );

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
	 * Filter password reset request email's body.
	 *
	 * @param  string $message
	 * @param  string $key
	 * @param  string $user_login
	 * @return string The email message to send.
	 */
	public function filter_retrieve_password_message( $message, $key, $user_login ) {
		$site_name       = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$reset_link_base = defined( 'RISE_FRONTEND_URL' ) ? esc_url( RISE_FRONTEND_URL ) : home_url();
		$reset_link      = esc_url( $reset_link_base ) . '/reset-password?key=' . $key . '&login=' . rawurlencode( $user_login );

		// Create new message
		$message = __( 'Someone has requested a password reset for the following account:' . $user_login, 'rise' ) . "\n";
		$message .= sprintf( __( 'Site Name: %s' ), $site_name ) . "\n";
		$message .= sprintf( __( 'Username: %s', 'rise' ), $user_login ) . "\n";
		$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'rise' ) . "\n";
		$message .= __( 'To reset your password, visit the following address:', 'rise' ) . "\n";
		$message .= $reset_link . "\n";

		return $message;
	}

	/**
	 * Get the email friendly site name.
	 *
	 * @source wp-graphql/src/Mutation/SendPasswordResetEmail.php Original source
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_email_friendly_site_name() {
		if ( \is_multisite() ) {
			$network = \get_network();
			if ( isset( $network->site_name ) ) {
				return $network->site_name;
			}
		}

		/*
			* The blogname option is escaped with esc_html on the way into the database
			* in sanitize_option we want to reverse this for the plain text arena of emails.
		*/

		return \wp_specialchars_decode( \get_option( 'blogname' ), ENT_QUOTES );
	}
}