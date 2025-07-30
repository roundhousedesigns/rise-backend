<?php

namespace RHD\Rise\Core;

/**
 * Registers cron jobs.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Cron {
	/**
	 * Constructor for the Cron class.
	 * Initializes the cron job scheduling.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_filter( 'cron_schedules', [ $this, 'add_custom_cron_intervals' ] );
		$this->schedule_cron_jobs();
	}

	/**
	 * Add custom cron intervals.
	 *
	 * @param array $schedules Existing cron schedules.
	 * @return array Modified cron schedules.
	 * @since 1.2
	 */
	public function add_custom_cron_intervals( $schedules ) {
		$schedules['four_times_daily'] = [
			'interval' => 6 * HOUR_IN_SECONDS, // Every 6 hours
			'display'  => __( 'Four Times Daily', 'rise' )
		];
		
		return $schedules;
	}

	/**
	 * Deactivates all Rise cron jobs.
	 * Removes scheduled events for conflict ranges, expired jobs, and no credits notifications.
	 *
	 * @since 0.1.0
	 */
	public function rise_cron_deactivate() {
		$timestamp = wp_next_scheduled( 'rise_delete_expired_conflict_ranges_cron' );
		wp_unschedule_event( $timestamp, 'rise_delete_expired_conflict_ranges_cron' );

		$job_timestamp = wp_next_scheduled( 'rise_check_expired_job_posts_cron' );
		wp_unschedule_event( $job_timestamp, 'rise_check_expired_job_posts_cron' );

		$no_credits_timestamp = wp_next_scheduled( 'rise_notify_users_no_credits_cron' );
		wp_unschedule_event( $no_credits_timestamp, 'rise_notify_users_no_credits_cron' );
	}

	/**
	 * Schedules the cron jobs for conflict ranges, expired jobs, and no credits notifications.
	 * Only schedules if the jobs are not already scheduled.
	 *
	 * @access private
	 * @since 0.1.0
	 */
	private function schedule_cron_jobs() {
		if ( !wp_next_scheduled( 'rise_delete_expired_conflict_ranges_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'rise_delete_expired_conflict_ranges_cron' );
		}

		if ( !wp_next_scheduled( 'rise_check_expired_job_posts_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'rise_check_expired_job_posts_cron' );
		}

		if ( !wp_next_scheduled( 'rise_notify_users_no_credits_cron' ) ) {
			wp_schedule_event( time(), 'four_times_daily', 'rise_notify_users_no_credits_cron' );
		}
	}

	/**
	 * Deletes expired conflict ranges from the database.
	 * Checks each conflict range and removes those with end dates in the past.
	 *
	 * @since 0.1.0
	 *
	 * @throws \WP_Error If there's an error deleting a conflict range.
	 */
	public function delete_expired_conflict_ranges() {
		$args = [
			'post_type'      => 'conflict_range',
			'posts_per_page' => -1,
		];

		$conflict_ranges = get_posts( $args );

		foreach ( $conflict_ranges as $conflict_range ) {
			$pod      = \pods( 'conflict_range', $conflict_range->ID );
			$end_date = $pod->field( 'end_date' );

			$date = \DateTime::createFromFormat( 'Y-m-d', $end_date );

			// Check if the date is in the past
			if ( $date < new \DateTime() ) {
				$result = wp_delete_post( $conflict_range->ID, true );

				if ( !$result ) {
					throw new \WP_Error( 'conflict_range_deletion_error', esc_html( 'An error occurred while deleting the conflict range.' ) );
				}
			}
		}
	}

	/**
	 * Checks for and updates expired job posts.
	 * Finds all job posts that have passed their expiration date and marks them as expired.
	 *
	 * @since 1.2
	 */
	public function check_expired_job_posts() {
		$pod = \pods( 'job_post' );

		$params = [
			'where' => [
				'relation' => 'AND',
				[
					'key'     => 'expiration_date',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '<',
					'type'    => 'DATE',
				],
				[
					'key'     => 'expired',
					'value'   => 1,
					'compare' => '!=',
				],
			],
		];

		$pod->find( $params );

		while ( $pod->fetch() ) {
			$pod->save( 'expired', true );
		}
	}

	/**
	 * Alias for check_expired_job_posts to match hook name.
	 */
	public function expire_job_posts() {
		$this->check_expired_job_posts();
	}

	/**
	 * Notify users with no credits about adding credits to their profile.
	 * Checks for crew-member users with no Credit posts who should be notified.
	 *
	 * @since 1.2
	 */
	public function notify_users_no_credits() {
		// Get all users with the 'crew-member' role
		$users = get_users( [
			'role'       => 'crew-member',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'     => 'disable_profile',
					'value'   => true,
					'compare' => '!=',
				],
				[
					'relation' => 'OR',
					[
						'key'     => 'disable_profile',
						'compare' => 'NOT EXISTS',
					],
					[
						'key'     => 'disable_profile',
						'value'   => false,
						'compare' => '=',
					],
				],
			],
		] );

		$today = current_time( 'Y-m-d' );

		foreach ( $users as $user ) {
			// Check if user has any Credit posts
			$credit_count = get_posts( [
				'post_type'      => 'credit',
				'author'         => $user->ID,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			] );

			// Skip if user has credits
			if ( !empty( $credit_count ) ) {
				continue;
			}

			// Get user pod to check the notification date
			$user_pod = \pods( 'user', $user->ID );
			if ( !$user_pod ) {
				continue;
			}

			$last_notified = $user_pod->field( 'no_credits_last_notified_on' );

			// Skip if last notified date is after today
			if ( !empty( $last_notified ) && $last_notified > $today ) {
				continue;
			}

			// Check if user already has a 'no_profile_credits' notification
			$existing_notification = get_posts( [
				'post_type'      => 'profile_notification',
				'author'         => $user->ID,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => [
					[
						'key'   => 'notification_type',
						'value' => 'no_profile_credits',
					],
				],
			] );

			// Skip if notification already exists
			if ( !empty( $existing_notification ) ) {
				continue;
			}

			// Create the notification
			$notification_pod = \pods( 'profile_notification' );
			if ( !$notification_pod ) {
				continue;
			}

			$notification_id = $notification_pod->add( [
				'notification_type' => 'no_profile_credits',
				'value'             => 'Add some credits to your profile to make sure you\'re listed in our Directory!',
				'is_read'           => false,
				'author'            => $user->ID,
			] );

			if ( $notification_id ) {
				// Update the post title and status
				\wp_update_post( [
					'ID'          => $notification_id,
					'post_status' => 'publish',
					'post_title'  => \__( 'Add credits to your profile', 'rise' ),
				] );

				// Update the user's last notified date
				$user_pod->save( 'no_credits_last_notified_on', $today );
			}
		}
	}
}