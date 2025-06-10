<?php

namespace RHD\Rise\Includes;

/**
 * Registers cron jobs.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Cron {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var string $plugin_name The ID of this plugin.
	 * @since    0.1.0
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var string $version The current version of this plugin.
	 * @since    0.1.0
	 */
	private $version;

	/**
	 * Constructor for the Cron class.
	 * Initializes the cron job scheduling.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->schedule_cron_jobs();
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
			wp_schedule_event( time(), 'daily', 'rise_check_expired_job_posts_cron' );
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
} 