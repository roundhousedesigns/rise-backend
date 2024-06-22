<?php
/**
 * The credit class.
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
	 * The credit's ID.
	 *
	 * @var int $id The credit post ID.
	 * @since 0.1.0
	 */
	public $credit_id;

	/**
	 * The credit's display index.
	 *
	 * @var int $index The credit's display index.
	 * @since 0.2.0
	 */
	private $index;

	/**
	 * The credit title.
	 *
	 * @var array $title The credit title.
	 * @since 0.1.0
	 */
	private $title;
	/**
	 * The credit job title.
	 *
	 * @var array $title The credit title.
	 * @since 0.1.0
	 */
	private $job_title;

	/**
	 * The credit location.
	 *
	 * @var array $title The credit title.
	 * @since 0.1.0
	 */
	private $job_location;

	/**
	 * The credit's venue meta field.
	 *
	 * @var string $venue The credit's venue meta field.
	 * @since 0.1.0
	 */
	private $venue;

	/**
	 * The credit's work start meta field.
	 *
	 * @var string $year The credit's work_start meta field.
	 * @since 0.1.0
	 */
	private $work_start;

	/**
	 * The credit's work end meta field.
	 *
	 * @var string $year The credit's work_end meta field.
	 * @since 0.1.0
	 */
	private $work_end;

	/**
	 * The credit's currently working meta field.
	 *
	 * @var boolean $year The credit's work_current meta field.
	 * @since 0.1.0
	 */
	private $work_current;

	/**
	 * The credit's intern meta field.
	 *
	 * @var boolean $intern The credit's intern meta field.
	 * @since 1.0.9.2
	 */
	private $intern;

	/**
	 * The credit's fellow meta field.
	 *
	 * @var boolean $fellow The credit's fellow meta field.
	 * @since 1.0.9.2
	 */
	private $fellow;

	/**
	 * The credit's 2nd-level `position` taxonomy terms.
	 *
	 * @var int[] $jobs The credit's `position` IDs.
	 * @since 1.0.9
	 */
	private $departments;

	/**
	 * The credit's 2nd-level `position` taxonomy terms.
	 *
	 * @var int[] $jobs The credit's `position` IDs.
	 * @since 0.1.0
	 */
	private $jobs;

	/**
	 * The credit's `skill` taxonomy terms.
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
		$this->credit_id    = $data['isNew'] ? 0 : $data['id'];
		$this->index        = $data['index'];
		$this->title        = $data['title'];
		$this->job_title    = $data['jobTitle'];
		$this->job_location = $data['jobLocation'];
		$this->venue        = $data['venue'];
		$this->work_start   = $data['workStart'];
		$this->work_end     = $data['workEnd'];
		$this->work_current = $data['workCurrent'];
		$this->intern       = $data['intern'];
		$this->fellow       = $data['fellow'];
		$this->departments  = $data['departments'];
		$this->jobs         = $data['jobs'];
		$this->skills       = $data['skills'];
	}

	/**
	 * Setter for the credit's ID.
	 *
	 * @param  int    $credit_id
	 * @return void
	 */
	private function set_id( $credit_id ) {
		$this->credit_id = $credit_id;
	}

	/**
	 * Update the user's profile data.
	 *
	 * @return int|WP_Error The credit post ID on success. WP_Error on failure.
	 */
	public function update_credit() {
		$credit_id = $this->update_base();

		if ( is_wp_error( $credit_id ) ) {
			return $credit_id->get_error_message();
		}

		$this->set_id( $credit_id );

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

		return $credit_id;
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

		if ( 0 === $this->credit_id ) {
			return wp_insert_post( $update_post_args );
		}

		$update_post_args['ID'] = $this->credit_id;
		return wp_update_post( $update_post_args );
	}

	/**
	 * Update the user's meta data.
	 *
	 * @return int|false|null The ID of the conflict range on success, false on failure, or null if there was an issue with the Pod itself.
	 */
	protected function update_meta() {
		// Get the user's pod.
		$pod = pods( 'credit', $this->credit_id );

		// Update the credit's pod.
		$update_fields = [
			'index'        => $this->index,
			'work_start'   => $this->work_start,
			'work_end'     => $this->work_end,
			'work_current' => $this->work_current,
			'intern'       => $this->intern,
			'fellow'       => $this->fellow,
			'venue'        => $this->venue,
			'job_title'    => $this->job_title,
			'job_location' => $this->job_location,
		];

		return $pod->save( $update_fields );
	}

	/**
	 * Set the credit's 2nd-level `position` terms.
	 *
	 * @return int[]|WP_Error The term IDs on success. WP_Error on failure.
	 */
	protected function update_positions() {
		$positions = array_merge( $this->departments, $this->jobs );
		return wp_set_object_terms( $this->credit_id, array_map( 'intval', $positions ), 'position', false );
	}

	/**
	 * Set the credit's `skill` terms.
	 *
	 * @return int[]|WP_Error The term IDs on success. WP_Error on failure.
	 */
	protected function update_skills() {
		return wp_set_object_terms( $this->credit_id, array_map( 'intval', $this->skills ), 'skill', false );
	}

	/**
	 * Set the credit's `index` field.
	 *
	 * @return int|false|null The ID of the conflict range on success, false on failure, or null if there was an issue with the Pod itself.
	 */
	public function update_index() {
		// Get the user's pod.
		$pod = pods( 'credit', $this->credit_id );

		// Update the credit's pod.
		$update_fields = [
			'index' => $this->index,
		];

		return $pod->save( $update_fields );
	}

	/**
	 * Get the credit's data for GraphQL.
	 *
	 * @return array The credit's data.
	 */
	public function prepare_credit_for_graphql() {
		return [
			'databaseId'  => $this->credit_id,
			'title'       => $this->title,
			'index'       => $this->index,
			'jobTitle'    => $this->job_title,
			'jobLocation' => $this->job_location,
			'venue'       => $this->venue,
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
