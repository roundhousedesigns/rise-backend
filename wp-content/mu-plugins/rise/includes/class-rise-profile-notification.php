<?php
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

class Rise_Profile_Notification {

	const NOTIFICATION_TYPES = [
		'starred_profile_updated',
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
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 */
	public function __construct( $data ) {
		$this->id                = absint( $data['id'] );
		$this->title             = sanitize_text_field( $data['title'] );
		$this->notification_type = esc_attr( $data['notification_type'] );
		$this->value             = sanitize_text_field( $data['value'] );
	}

	/**
	 * Mark a notification as read.
	 *
	 * @param  int  $notification_id The notification post ID.
	 * @return bool Whether the notification was marked as read.
	 */
	public static function mark_notification_as_read( $notification_id ) {
		$pod = pods( 'profile_notification', $notification_id );
		$pod->save( [
			'is_read' => true,
		] );
	}

	/**
	 * Get unread notification count for a user.
	 *
	 * @param  int   $user_id The user ID to get unread notifications for.
	 * @return array Array of unread notifications.
	 */
	public static function get_unread_for_graphql( $user_id ) {
		$params = [
			'where'   => ['d.is_read' => false],
			'author'  => $user_id,
			'limit'   => -1,
			'orderby' => 'date',
			'order'   => 'DESC',
		];

		$notification_pods = pods( 'profile_notification' )->find( $params );

		$notifications = [];

		while ( $notification_pods->fetch() ) {
			$notification = new Rise_Profile_Notification( [
				'id'                => $notification_pods->field( 'ID' ),
				'title'             => $notification_pods->field( 'post_title' ),
				'notification_type' => $notification_pods->field( 'notification_type' ),
				'value'             => $notification_pods->field( 'value' ),
			] );

			$notifications[] = [
				'id'               => $notification->id,
				'title'            => $notification->title,
				'notificationType' => $notification->notification_type,
				'value'            => $notification->value,
			];
		}

		return $notifications;
	}
}