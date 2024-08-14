<?php

/**
 * Registers cron jobs.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.1.7
 */

class Rise_Cron {

	public function __construct() {
		$this->schedule_cron_jobs();
	}

	public function rise_cron_deactivate() {
		$timestamp = wp_next_scheduled( 'rise_delete_expired_conflict_ranges_cron' );
		wp_unschedule_event( $timestamp, 'rise_delete_expired_conflict_ranges_cron' );
	}

	private function schedule_cron_jobs() {
		if ( !wp_next_scheduled( 'rise_delete_expired_conflict_ranges_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'rise_delete_expired_conflict_ranges_cron' );
		}
	}

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
}
