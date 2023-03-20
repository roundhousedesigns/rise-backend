<?php
/**
 * The Credit class.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Get_To_Work_Credit {
	/**
	 * The user's ID.
	 *
	 * @var int $ The user ID.
	 * @since 0.1.0
	 */
	public $id;

	/**
	 * The Credit title.
	 *
	 * @var array $title The Credit title.
	 * @since 0.1.0
	 */
	private $title;

	/**
	 * The Credit's venue meta field.
	 *
	 * @var string $venue The Credit's venue meta field.
	 * @since 0.1.0
	 */
	private $venue;

	/**
	 * The Credit's year meta field.
	 *
	 * @var string $year The Credit's year meta field.
	 * @since 0.1.0
	 */
	private $year;

	/**
	 * The Credit's 2nd-level `position` taxonomy terms.
	 *
	 * @var int[] $jobs The credit's `position` IDs.
	 * @since 0.1.0
	 */
	private $jobs;

	/**
	 * The Credit's `skill` taxonomy terms.
	 *
	 * @var int[] $credits The credit's credit IDs.
	 * @since 0.1.0
	 */
	private $skills;

	/**
	 * The constructor.
	 *
	 * @since  0.1.0
	 *
	 * @param  array  $data The user's data.
	 * @return void
	 */
	public function __construct( $data ) {
		// TODO sanitize input.
		$this->id     = $data['id'];
		$this->title  = $data['title'];
		$this->venue  = $data['venue'];
		$this->year   = $data['year'];
		$this->jobs   = $data['positions'];
		$this->skills = $data['skills'];
	}

	private function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * Get the user's ID.
	 *
	 * @return void
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Update the user's profile data.
	 *
	 * @return int|WP_Error The user ID on success. WP_Error on failure.
	 */
	public function update_credit() {
		$credit = $this->update_base();

		if ( is_wp_error( $credit ) ) {
			return $credit->get_error_message();
		}

		$this->set_id( $credit );

		$meta   = $this->update_meta();
		$jobs   = $this->update_jobs();
		$skills = $this->update_skills();

		// TODO add error condition for $meta
		if ( 0 === $meta ) {
			return new WP_Error( 'no_meta', 'No meta was updated.' );
		} elseif ( is_wp_error( $jobs ) ) {
			return $jobs->get_error_message();
		} elseif ( is_wp_error( $skills ) ) {
			return $skills->get_error_message();
		}

		return $credit;
	}

	/**
	 * Update the credit's post data.
	 *
	 * @return int|WP_Error The post ID on success. WP_Error on failure.
	 */
	protected function update_base() {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return new WP_Error( 'no_user', 'No user is logged in.' );
		}

		$update_post_args = [
			'post_title'  => $this->title,
			'post_author' => $user_id,
			'post_status' => 'publish',
			'post_type'   => 'credit',
		];

		if ( $this->id ) {
			$update_post_args['ID'] = $this->id;
			$result                 = wp_update_post( $update_post_args );
		} else {
			$result = wp_insert_post( $update_post_args );
		}

		return $result;
	}

	/**
	 * Update the user's meta data.
	 *
	 * @return int The post ID return value of pods->save().
	 */
	protected function update_meta() {
		// Get the user's pod.
		$pod = pods( 'credit', $this->id );

		// Update the credit's pod.
		$update_fields = [
			'year'  => $this->year,
			'venue' => $this->venue,
		];

		// TODO investigate error handling (does $pod->save() return 0 on failure?)

		return $pod->save( $update_fields );
	}

	/**
	 * Set the credit's 2nd-level `position` terms.
	 *
	 * @return int[]|WP_Error The term IDs on success. WP_Error on failure.
	 */
	protected function update_jobs() {
		return wp_set_object_terms( $this->id, array_map( 'intval', $this->jobs ), 'position', false );
	}

	/**
	 * Set the credit's `skill` terms.
	 *
	 * @return int[]|WP_Error The term IDs on success. WP_Error on failure.
	 */
	protected function update_skills() {
		return wp_set_object_terms( $this->id, array_map( 'intval', $this->skills ), 'skill', false );
	}
}
