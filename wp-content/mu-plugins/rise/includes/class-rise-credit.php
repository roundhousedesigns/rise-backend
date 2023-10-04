<?php
/**
 * The Credit class.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Rise_Credit {
	/**
	 * The user's ID.
	 *
	 * @var int $id The user ID.
	 * @since 0.1.0
	 */
	public $id;

	/**
	 * The Credit's display index.
	 *
	 * @var int $index The Credit's display index.
	 * @since 0.2.0
	 */
	private $index;

	/**
	 * The Credit title.
	 *
	 * @var array $title The Credit title.
	 * @since 0.1.0
	 */
	private $title;
	/**
	 * The Credit job title.
	 *
	 * @var array $title The Credit title.
	 * @since 0.1.0
	 */
	private $job_title;

	/**
	 * The Credit location.
	 *
	 * @var array $title The Credit title.
	 * @since 0.1.0
	 */
	private $job_location;

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
	 * @deprecated 0.2.0
	 * @since 0.1.0
	 */
	private $year;

	/**
	 * The Credit's work start meta field.
	 *
	 * @var string $year The Credit's work_start meta field.
	 * @since 0.1.0
	 */
	private $work_start;

	/**
	 * The Credit's work end meta field.
	 *
	 * @var string $year The Credit's work_end meta field.
	 * @since 0.1.0
	 */
	private $work_end;

	/**
	 * The Credit's currently working meta field.
	 *
	 * @var boolean $year The Credit's work_current meta field.
	 * @since 0.1.0
	 */
	private $work_current;

	/**
	 * The Credit's intern meta field.
	 *
	 * @var boolean $intern The Credit's intern meta field.
	 * @since 1.0.9.2
	 */
	private $intern;

	/**
	 * The Credit's fellow meta field.
	 *
	 * @var boolean $fellow The Credit's fellow meta field.
	 * @since 1.0.9.2
	 */
	private $fellow;

	/**
	 * The Credit's 2nd-level `position` taxonomy terms.
	 *
	 * @var int[] $jobs The credit's `position` IDs.
	 * @since 1.0.9
	 */
	private $departments;

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
		$this->id           = $data['isNew'] ? 0 : $data['id'];
		$this->index        = $data['index'];
		$this->title        = $data['title'];
		$this->job_title    = $data['jobTitle'];
		$this->job_location = $data['jobLocation'];
		$this->venue        = $data['venue'];
		$this->year         = $data['year'];
		$this->work_start   = $data['workStart'];
		$this->work_end     = $data['workEnd'];
		$this->work_current = $data['workCurrent'];
		$this->intern       = $data['intern'];
		$this->fellow       = $data['fellow'];
		$this->departments  = $data['departments'];
		$this->jobs         = $data['jobs'];
		$this->skills       = $data['skills'];
	}

	private function set_id( $id ) {
		$this->id = $id;
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
		$jobs   = $this->update_positions();
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
		if ( !$user_id ) {
			return new WP_Error( 'no_user', 'No user is logged in.' );
		}

		$update_post_args = [
			'post_title'  => $this->title,
			'post_author' => $user_id,
			'post_status' => 'publish',
			'post_type'   => 'credit',
		];

		if ( 0 === $this->id ) {
			$result = wp_insert_post( $update_post_args );
		} else {
			$update_post_args['ID'] = $this->id;
			$result                 = wp_update_post( $update_post_args );
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
			'index'        => $this->index,
			'year'         => $this->year,
			'work_start'   => $this->work_start,
			'work_end'     => $this->work_end,
			'work_current' => $this->work_current,
			'intern'       => $this->intern,
			'fellow'       => $this->fellow,
			'venue'        => $this->venue,
			'job_title'    => $this->job_title,
			'job_location' => $this->job_location,
		];

		// TODO investigate error handling (does $pod->save() return 0 on failure?)

		return $pod->save( $update_fields );
	}

	/**
	 * Set the credit's 2nd-level `position` terms.
	 *
	 * @return int[]|WP_Error The term IDs on success. WP_Error on failure.
	 */
	protected function update_positions() {
		$positions = array_merge( $this->departments, $this->jobs );
		return wp_set_object_terms( $this->id, array_map( 'intval', $positions ), 'position', false );
	}

	/**
	 * Set the credit's `skill` terms.
	 *
	 * @return int[]|WP_Error The term IDs on success. WP_Error on failure.
	 */
	protected function update_skills() {
		return wp_set_object_terms( $this->id, array_map( 'intval', $this->skills ), 'skill', false );
	}

	/**
	 * Set the credit's `index` field.
	 *
	 * @return void
	 */
	public function update_index() {
		// Get the user's pod.
		$pod = pods( 'credit', $this->id );

		// Update the credit's pod.
		$update_fields = [
			'index' => $this->index,
		];

		// TODO investigate error handling (does $pod->save() return 0 on failure?)
		return $pod->save( $update_fields );
	}

	/**
	 * Get the credit's data for GraphQL.
	 *
	 * @return array The credit's data.
	 */
	public function prepare_credit_for_graphql() {
		return [
			'databaseId'  => $this->id,
			'title'       => $this->title,
			'index'       => $this->index,
			'jobTitle'    => $this->job_title,
			'jobLocation' => $this->job_location,
			'venue'       => $this->venue,
			'year'        => $this->year,
			'workStart'   => $this->work_start,
			'workEnd'     => $this->work_end,
			'workCurrent' => $this->work_current,
			'intern'      => $this->intern,
			'fellow'      => $this->fellow,
			'jobs'        => $this->jobs,
			'departments' => $this->departments,
			'skills'      => $this->skills,
		];
	}
}
