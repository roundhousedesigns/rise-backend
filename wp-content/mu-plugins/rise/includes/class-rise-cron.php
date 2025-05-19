<?php

/**
 * Registers cron jobs.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Rise_Cron {

	/**
	 * Constructor for the Rise_Cron class.
	 * Initializes the cron job scheduling.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->schedule_cron_jobs();
	}

	/**
	 * Adds a custom twice daily schedule to WordPress cron schedules.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $schedules Array of existing cron schedules.
	 * @return array Modified array of cron schedules.
	 */
	public function add_twice_daily_schedule( $schedules ) {
		$schedules['twice_daily'] = [
			'interval' => 43200, // 12 hours in seconds
			'display'  => __( 'Twice Daily' ),
		];
		return $schedules;
	}

	/**
	 * Deactivates all Rise cron jobs.
	 * Removes scheduled events for conflict ranges and expired jobs.
	 *
	 * @since 0.1.0
	 */
	public function rise_cron_deactivate() {
		$timestamp = wp_next_scheduled( 'rise_delete_expired_conflict_ranges_cron' );
		wp_unschedule_event( $timestamp, 'rise_delete_expired_conflict_ranges_cron' );

		$job_timestamp = wp_next_scheduled( 'rise_check_expired_job_posts_cron' );
		wp_unschedule_event( $job_timestamp, 'rise_check_expired_job_posts_cron' );
	}

	/**
	 * Schedules the cron jobs for conflict ranges and expired jobs.
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
			wp_schedule_event( time(), 'twice_daily', 'rise_check_expired_job_posts_cron' );
		}
	}

	/**
	 * Deletes expired conflict ranges from the database.
	 * Checks each conflict range and removes those with end dates in the past.
	 *
	 * @since 0.1.0
	 *
	 * @throws WP_Error If there's an error deleting a conflict range.
	 */
	public function delete_expired_conflict_ranges() {
		$args = [
			'post_type'      => 'conflict_range',
			'posts_per_page' => -1,
		];

		$conflict_ranges = get_posts( $args );

		foreach ( $conflict_ranges as $conflict_range ) {
			$pod      = pods( 'conflict_range', $conflict_range->ID );
			$end_date = $pod->field( 'end_date' );

			$date = DateTime::createFromFormat( 'Y-m-d', $end_date );

			// Check if the date is in the past
			if ( $date < new DateTime() ) {
				$result = wp_delete_post( $conflict_range->ID, true );

				if ( !$result ) {
					throw new WP_Error( 'conflict_range_deletion_error', esc_html( 'An error occurred while deleting the conflict range.' ) );
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
		$pod = pods( 'job_post' );

		$params = [
			'where' => [
				'relation' => 'AND',
				[
					'key'     => '_expires_on',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '<',
					'type'    => 'DATE',
				],
				[
					'key'     => '_expired',
					'value'   => 1,
					'compare' => '!=',
				],
			],
		];

		$pod->find( $params );

		$expired_job_posts = [];
		while ( $pod->fetch() ) {
			$pod->save( '_expired', 1 );

			wp_update_post( [
				'ID'          => $pod->field( 'ID' ),
				'post_status' => 'private',
			] );

			$expired_job_posts[] = $pod->field( 'ID' );
		}
	}
}
