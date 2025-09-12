<?php

namespace RHD\Rise\Includes;

/**
 * Handles profile notifications functionality.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

// TODO: Extend WP_Post to get the ID, title, and date_time.

class ProfileNotification {

	const NOTIFICATION_TYPES = [
		'test_notification',
		'starred_profile_updated',
		'job_posted',
		'no_profile_credits',
		'new_user',
		// Add other notification types here
	];

	/**
	 * The Profile Notification's ID
	 *
	 * @var int $id The Profile Notification's ID.
	 * @since 1.2.0
	 */
	private $id;

	/**
	 * The Profile Notification's title.
	 *
	 * @var string $title The Profile Notification's title.
	 * @since 1.2.0
	 */
	private $title;

	/**
	 * The Profile Notification's type.
	 *
	 * @var string $type The Profile Notification's type.
	 * @since 1.2.0
	 */
	private $notification_type;

	/**
	 * The Profile Notification's value. Combined with the notification type, this will determine the notification's content.
	 *
	 * @todo Will ultimately be constrained to a list of values,
	 * but since we only have one type for now, let's keep it simple.
	 *
	 * @var string $value The Profile Notification's value.
	 * @since 1.2.0
	 */
	private $value;

	/**
	 * The Profile Notification's read status.
	 *
	 * @var bool $is_read The Profile Notification's read status.
	 * @since 1.2.0
	 */
	private $is_read;

	/**
	 * The Profile Notification's date and time.
	 *
	 * @var string $date_time The Profile Notification's date and time.
	 * @since 1.2.0
	 */
	private $date_time;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 */
	public function __construct( $data ) {
		$this->id                = \absint( $data['id'] );
		$this->title             = \sanitize_text_field( $data['title'] );
		$this->notification_type = \esc_attr( $data['notification_type'] );
		$this->value             = \sanitize_text_field( $data['value'] );
		$this->date_time         = \sanitize_text_field( $data['date_time'] );
		$this->is_read           = $data['is_read'] ?? false;
	}

	/**
	 * Mark a notification as read.
	 *
	 * @param  int  $notification_id The notification post ID.
	 * @return bool Whether the notification was marked as read.
	 */
	public static function mark_notification_as_read( $notification_id ) {
		$pod    = \pods( 'profile_notification', $notification_id );
		$result = $pod->save( [
			'is_read' => true,
		] );

		return !!$result;
	}

	/**
	 * Dismiss a notification.
	 *
	 * @param  int  $notification_id The notification post ID.
	 * @return bool Whether the notification was dismissed.
	 */
	public static function dismiss_notification( $notification_id ) {
		$result = \wp_delete_post( $notification_id, true );

		return !!$result;
	}

	/**
	 * Get unread notification count for a user.
	 *
	 * @param  int   $user_id The user ID to get unread notifications for.
	 * @param  bool  $is_read Whether to get read or unread notifications.
	 * @param  int   $limit   The number of notifications to return.
	 * @return array Array of unread notifications.
	 */
	public static function get_profile_notices_for_graphql( $user_id, $is_read = false, $limit = -1 ) {
		$params = [
			'where'   => ['d.is_read' => $is_read, 't.post_author' => $user_id],
			'author'  => $user_id,
			'limit'   => $is_read ? $limit : -1,
			'orderby' => 'date',
			'order'   => 'DESC',
		];

		$notification_pods = \pods( 'profile_notification' )->find( $params );

		$notifications = [];

		while ( $notification_pods->fetch() ) {
			$notification = new ProfileNotification( [
				'id'                => $notification_pods->field( 'ID' ),
				'title'             => $notification_pods->field( 'post_title' ),
				'notification_type' => $notification_pods->field( 'notification_type' ),
				'value'             => $notification_pods->field( 'value' ),
				'is_read'           => $notification_pods->field( 'is_read' ),
				'date_time'         => get_the_date( 'Y-m-d H:i:s', $notification_pods->field( 'ID' ) ),
			] );

			$notifications[] = [
				'id'               => $notification->id,
				'title'            => $notification->title,
				'notificationType' => $notification->notification_type,
				'value'            => $notification->value,
				'isRead'           => $notification->is_read,
				'dateTime'         => $notification->date_time,
			];
		}

		return $notifications;
	}

	/**
	 * Create a notification for a user.
	 *
	 * @param  int    $user_id           The user ID to create the notification for.
	 * @param  string $notification_type The type of notification to create.
	 * @param  string $title             The notification title.
	 * @param  string $message_option    The option name containing the notification message.
	 * @return bool   Whether the notification was created.
	 */
	private static function create_user_notification( $user_id, $notification_type, $title, $message_option ) {
		$user_pod = \pods( 'user', $user_id );

		if ( !$user_pod ) {
			return false;
		}

		$today = current_time( 'Y-m-d' );

		// Create the notification
		$notification_pod = \pods( 'profile_notification' );

		$notification_message = get_option( $message_option );

		// Create the Profile Notification with pod data
		$notification_id = $notification_pod->add( [
			'post_title'        => $title,
			'post_status'       => 'publish',
			'notification_type' => $notification_type,
			'value'             => $notification_message,
			'is_read'           => false,
			'author'            => $user_id,
		] );

		if ( $notification_id ) {
			// Update the user's last notified date
			$user_pod->save( 'no_credits_last_notified_on', $today );

			return true;
		}

		return false;
	}

	/**
	 * Create a no credits notification for a user.
	 *
	 * @param  int  $user_id The user ID to create the notification for.
	 * @return bool Whether the notification was created.
	 */
	public static function create_no_credits_notification( $user_id ) {
		$created = self::create_user_notification(
			$user_id,
			'no_profile_credits',
			\__( 'Add credits to your profile', 'rise' ),
			'rise_settings_no_credits_reminder_message'
		);

		if ( $created ) {
			$user_pod = \pods( 'user', $user_id );
			$today    = current_time( 'Y-m-d' );

			// Update the user's last notified date
			$user_pod->save( 'no_credits_last_notified_on', $today );

			return true;
		}
	}

	/**
	 * Create a new user notification.
	 *
	 * @param  int  $user_id The user ID to create the notification for.
	 * @return bool Whether the notification was created.
	 */
	public static function create_new_user_notification( $user_id ) {
		return self::create_user_notification(
			$user_id,
			'new_user',
			\__( 'Welcome to the RISE Directory!', 'rise' ),
			'rise_settings_new_user_message'
		);
	}
}
