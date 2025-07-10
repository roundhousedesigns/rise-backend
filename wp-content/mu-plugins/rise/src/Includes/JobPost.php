<?php

namespace RHD\Rise\Includes;

/**
 * The job post class.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.2
 */

// TODO Simplify 'job_post_id' to 'id'

class JobPost {

	/**
	 * The job post's ID.
	 *
	 * @var int $job_post_id The job post ID.
	 * @since 1.2
	 */
	public $job_post_id;

	/**
	 * The job post title.
	 *
	 * @var string $title The job post title.
	 * @since 1.2
	 */
	private $title;

	/**
	 * The job post author.
	 *
	 * @var int $post_author The job post author.
	 * @since 1.2
	 */
	private $post_author;

	/**
	 * The job post status.
	 *
	 * @var string $status The job post status.
	 * @since 1.2
	 */
	private $status;

	/**
	 * The company name.
	 *
	 * @var string $company_name The company name.
	 * @since 1.2
	 */
	private $company_name;

	/**
	 * The company address.
	 *
	 * @var string $company_address The company address.
	 * @since 1.2
	 */
	private $company_address;

	/**
	 * The contact person's name.
	 *
	 * @var string $contact_name The contact person's name.
	 * @since 1.2
	 */
	private $contact_name;

	/**
	 * The contact person's email.
	 *
	 * @var string $contact_email The contact person's email.
	 * @since 1.2
	 */
	private $contact_email;

	/**
	 * The contact person's phone.
	 *
	 * @var string $contact_phone The contact person's phone.
	 * @since 1.2
	 */
	private $contact_phone;

	/**
	 * The job start date.
	 *
	 * @var string $start_date The job start date.
	 * @since 1.2
	 */
	private $start_date;

	/**
	 * The job end date.
	 *
	 * @var string $end_date The job end date.
	 * @since 1.2
	 */
	private $end_date;

	/**
	 * The application instructions.
	 *
	 * @var string $instructions The application instructions.
	 * @since 1.2
	 */
	private $instructions;

	/**
	 * The compensation details.
	 *
	 * @var string $compensation The compensation details.
	 * @since 1.2
	 */
	private $compensation;

	/**
	 * The application URL.
	 *
	 * @var string $application_url The application URL.
	 * @since 1.2
	 */
	private $application_url;

	/**
	 * The application phone number.
	 *
	 * @var string $application_phone The application phone number.
	 * @since 1.2
	 */
	private $application_phone;

	/**
	 * The application email.
	 *
	 * @var string $application_email The application email.
	 * @since 1.2
	 */
	private $application_email;

	/**
	 * The job description.
	 *
	 * @var string $description The job description.
	 * @since 1.2
	 */
	private $description;

	/**
	 * Whether this is a paid position.
	 *
	 * @var boolean $is_paid Whether this is a paid position.
	 * @since 1.2
	 */
	private $is_paid;

	/**
	 * Whether this is an internship position.
	 *
	 * @var boolean $is_internship Whether this is an internship position.
	 * @since 1.2
	 */
	private $is_internship;

	/**
	 * Whether this is a union position.
	 *
	 * @var boolean $is_union Whether this is a union position.
	 * @since 1.2
	 */
	private $is_union;

	/**
	 * The job_post's department `position` taxonomy terms.
	 *
	 * @var int[] $departments The job_post's department position IDs.
	 * @since 1.2
	 */
	private $departments;

	/**
	 * The job_post's job `position` taxonomy terms.
	 *
	 * @var int[] $jobs The job_post's job position IDs.
	 * @since 1.2
	 */
	private $jobs;

	/**
	 * The job post's `skill` taxonomy terms.
	 *
	 * @var int[] $skills The job post's skill IDs.
	 * @since 1.2
	 */
	private $skills;

	/**
	 * The constructor.
	 *
	 * @since  1.2
	 *
	 * @param  array  $data The job post data.
	 * @return void
	 */
	public function __construct( $data ) {
		$this->job_post_id       = $data['isNew'] ? 0 : $data['id'];
		$this->post_author       = $data['post_author'];
		$this->status            = $data['status'] ?? 'pending';
		$this->title             = $data['title'];
		$this->company_name      = $data['companyName'];
		$this->company_address   = $data['companyAddress'];
		$this->contact_name      = $data['contactName'];
		$this->contact_email     = $data['contactEmail'];
		$this->contact_phone     = $data['contactPhone'];
		$this->description       = $data['description'];
		$this->start_date        = $data['startDate'];
		$this->end_date          = $data['endDate'];
		$this->instructions      = $data['instructions'];
		$this->compensation      = $data['compensation'];
		$this->application_url   = $data['applicationUrl'];
		$this->application_phone = $data['applicationPhone'];
		$this->application_email = $data['applicationEmail'];
		$this->is_paid           = $data['isPaid'];
		$this->is_internship     = $data['isInternship'];
		$this->is_union          = $data['isUnion'];
		$this->departments       = $data['departments'];
		$this->jobs              = $data['jobs'];
		$this->skills            = $data['skills'];
	}

	/**
	 * Setter for the job post's ID.
	 *
	 * @param  int    $job_post_id
	 * @return void
	 */
	private function set_id( $job_post_id ) {
		$this->job_post_id = $job_post_id;
	}

	/**
	 * Update the job post data.
	 *
	 * @return int|\WP_Error The job post ID on success. WP_Error on failure.
	 */
	public function update_job_post() {
		$job_post_id = $this->update_base();

		if ( \is_wp_error( $job_post_id ) ) {
			return $job_post_id->get_error_message();
		}

		$this->set_id( $job_post_id );

		$meta   = $this->update_meta();
		$jobs   = $this->update_positions();
		$skills = $this->update_skills();

		if ( 0 === $meta ) {
			return new \WP_Error( 'no_meta', 'No meta was updated.' );
		} elseif ( \is_wp_error( $jobs ) ) {
			return $jobs->get_error_message();
		} elseif ( \is_wp_error( $skills ) ) {
			return $skills->get_error_message();
		}

		return $job_post_id;
	}

	/**
	 * Update the job post's post data.
	 *
	 * @return int|\WP_Error The post ID on success. WP_Error on failure.
	 */
	protected function update_base() {
		$update_post_args = [
			'post_title'  => $this->title,
			'post_author' => $this->post_author,
			'post_status' => $this->status,
			'post_type'   => 'job_post',
		];

		if ( 0 === $this->job_post_id ) {
			return \wp_insert_post( $update_post_args );
		}

		$update_post_args['ID'] = $this->job_post_id;
		return \wp_update_post( $update_post_args );
	}

	/**
	 * Update the job post's meta data.
	 *
	 * @return int|false|null The ID of the job post on success, false on failure, or null if there was an issue with the Pod itself.
	 */
	protected function update_meta() {
		// Get the job post's pod.
		$pod = \pods( 'job_post', $this->job_post_id );

		// Update the job post's pod.
		$update_fields = [
			'description'       => $this->description,
			'company_name'      => $this->company_name,
			'company_address'   => $this->company_address,
			'contact_name'      => $this->contact_name,
			'contact_email'     => $this->contact_email,
			'contact_phone'     => $this->contact_phone,
			'start_date'        => $this->start_date,
			'end_date'          => $this->end_date,
			'instructions'      => $this->instructions,
			'compensation'      => $this->compensation,
			'application_url'   => $this->application_url,
			'application_phone' => $this->application_phone,
			'application_email' => $this->application_email,
			'is_paid'           => $this->is_paid,
			'is_internship'     => $this->is_internship,
			'is_union'          => $this->is_union,
		];

		$return = $pod->save( $update_fields );

		return $return;
	}

	/**
	 * Set the job post's 2nd-level `position` terms.
	 *
	 * @return int[]|\WP_Error The term IDs on success. WP_Error on failure.
	 */
	protected function update_positions() {
		$positions = array_merge( $this->departments, $this->jobs );
		return \wp_set_object_terms( $this->job_post_id, array_map( 'intval', $positions ), 'position', false );
	}

	/**
	 * Set the job post's `skill` terms.
	 *
	 * @return int[]|\WP_Error The term IDs on success. WP_Error on failure.
	 */
	protected function update_skills() {
		return \wp_set_object_terms( $this->job_post_id, array_map( 'intval', $this->skills ), 'skill', false );
	}

	/**
	 * Get the job post's data for GraphQL.
	 *
	 * @since 1.2
	 *
	 * @return array The job post's data.
	 */
	public function prepare_job_post_for_graphql() {
		return [
			'databaseId'       => $this->job_post_id,
			'title'            => $this->title,
			'description'      => $this->description,
			'companyName'      => $this->company_name,
			'companyAddress'   => $this->company_address,
			'contactName'      => $this->contact_name,
			'contactEmail'     => $this->contact_email,
			'contactPhone'     => $this->contact_phone,
			'startDate'        => $this->start_date,
			'endDate'          => $this->end_date,
			'instructions'     => $this->instructions,
			'compensation'     => $this->compensation,
			'applicationUrl'   => $this->application_url,
			'applicationPhone' => $this->application_phone,
			'applicationEmail' => $this->application_email,
			'isPaid'           => $this->is_paid,
			'isInternship'     => $this->is_internship,
			'isUnion'          => $this->is_union,
			'jobs'             => $this->jobs,
			'departments'      => $this->departments,
			'skills'           => $this->skills,
		];
	}
}
