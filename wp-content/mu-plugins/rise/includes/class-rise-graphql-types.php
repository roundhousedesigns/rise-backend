<?php
/**
 * Register GraphQL object types, connections, interfaces, etc.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Rise_GraphQL_Types {
	public function register_types() {
		$this->register_graphql_input_types();
	}

	/**
	 * Register GraphQL input types.
	 *
	 * @return void
	 */
	protected function register_graphql_input_types() {
		/**
		 * PersonalLinks input type.
		 */
		register_graphql_input_type(
			'PersonalLinks',
			[
				'description' => __( 'A user\'s personal links.', 'rise' ),
				'fields'      => [
					'facebook'  => [
						'type'        => 'String',
						'description' => __( 'The user\'s Facebook profile URL.', 'rise' ),
					],
					'twitter'   => [
						'type'        => 'String',
						'description' => __( 'The user\'s Twitter handle.', 'rise' ),
					],
					'linkedin'  => [
						'type'        => 'String',
						'description' => __( 'The user\'s LinkedIn URL.', 'rise' ),
					],
					'instagram' => [
						'type'        => 'String',
						'description' => __( 'The user\'s Instagram handle.', 'rise' ),
					],
				],
			]
		);

		/**
		 * UserProfileInput input type.
		 */
		register_graphql_input_type(
			'UserProfileInput',
			[
				'description' => __( 'A user profile.', 'rise' ),
				'fields'      => [
					'id'                 => [
						'type'        => 'ID',
						'description' => __( 'The user\'s ID.', 'rise' ),
					],
					'firstName'          => [
						'type'        => 'String',
						'description' => __( 'The user\'s first name.', 'rise' ),
					],
					'lastName'           => [
						'type'        => 'String',
						'description' => __( 'The user\'s last name.', 'rise' ),
					],
					'pronouns'           => [
						'type'        => 'String',
						'description' => __( 'The user\'s pronouns.', 'rise' ),
					],
					'email'              => [
						'type'        => 'String',
						'description' => __( 'The user\'s contact email.', 'rise' ),
					],
					'selfTitle'          => [
						'type'        => 'String',
						'description' => __( 'The user\'s self title.', 'rise' ),
					],
					'homebase'           => [
						'type'        => 'String',
						'description' => __( 'The user\'s homebase.', 'rise' ),
					],
					'image'              => [
						'type'        => 'String',
						'description' => __( 'The user\'s image.', 'rise' ),
					],
					'phone'              => [
						'type'        => 'String',
						'description' => __( 'The user\'s phone number.', 'rise' ),
					],
					'description'        => [
						'type'        => 'String',
						'description' => __( 'The user\'s bio.', 'rise' ),
					],
					'locations'          => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s location.', 'rise' ),
					],
					'website'            => [
						'type'        => 'String',
						'description' => __( 'The user\'s website.', 'rise' ),
					],
					'socials'            => [
						'type'        => 'PersonalLinks',
						'description' => __( 'The user\'s social media and external web links.', 'rise' ),
					],
					'resume'             => [
						'type'        => 'String',
						'description' => __( 'The user\'s resume.', 'rise' ),
					],
					'willTravel'         => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the user will travel.', 'rise' ),
					],
					'willTour'           => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the user will tour.', 'rise' ),
					],
					'mediaVideo1'        => [
						'type'        => 'String',
						'description' => __( 'A user video oEmbed URL.', 'rise' ),
					],
					'mediaVideo2'        => [
						'type'        => 'String',
						'description' => __( 'A user video oEmbed URL.', 'rise' ),
					],
					'education'          => [
						'type'        => 'String',
						'description' => __( 'The user\'s education.', 'rise' ),
					],
					'unions'             => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s unions.', 'rise' ),
					],
					'partnerDirectories' => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s partner directories.', 'rise' ),
					],
					'experienceLevels'   => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s racial identities.', 'rise' ),
					],
					'racialIdentities'   => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s racial identities.', 'rise' ),
					],
					'genderIdentities'   => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s gender identities.', 'rise' ),
					],
					'personalIdentities' => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s additional personal identities.', 'rise' ),
					],
				],
			]
		);

		/**
		 * CreditInput input type.
		 */
		register_graphql_input_type(
			'CreditInput',
			[
				'description' => __( 'A new or updated credit.', 'rise' ),
				'fields'      => [
					'id'          => [
						'type'        => 'ID',
						'description' => __( 'The credit\'s ID.', 'rise' ),
					],
					'index'       => [
						'type'        => 'ID',
						'description' => __( 'The credit\'s display index.', 'rise' ),
					],
					'title'       => [
						'type'        => 'String',
						'description' => __( 'The credit\'s title.', 'rise' ),
					],
					'jobTitle'    => [
						'type'        => 'String',
						'description' => __( 'The credit\'s job title.', 'rise' ),
					],
					'jobLocation' => [
						'type'        => 'String',
						'description' => __( 'The credit\'s location.', 'rise' ),
					],
					'venue'       => [
						'type'        => 'String',
						'description' => __( 'The credit\'s venue.', 'rise' ),
					],
					'year'        => [ // TODO deprecate year in favor of workStart and workEnd
						'type'        => 'String',
						'description' => __( 'The credit\'s year.', 'rise' ),
					],
					'workStart'   => [
						'type'        => 'String',
						'description' => __( 'The credit\'s work start date.', 'rise' ),
					],
					'workEnd'     => [
						'type'        => 'String',
						'description' => __( 'The credit\'s work end date.', 'rise' ),
					],
					'workCurrent' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the this is a current job.', 'rise' ),
					],
					'departments' => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The credit\'s 1st-level position term IDs.', 'rise' ),
					],
					'jobs'        => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The credit\'s 2nd-level position term IDs.', 'rise' ),
					],
					'skills'      => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The credit\'s skill term IDs.', 'rise' ),
					],
					'isNew'       => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the credit is a new entry.', 'rise' ),
					],
				],
			]
		);

		/**
		 * SearchFilterSetRaw input type.
		 */
		register_graphql_input_type(
			'SearchFilterSetRaw',
			[
				'description' => __( 'A set of search filters.', 'rise' ),
				'fields'      => [
					'positions'          => [
						'description' => __( 'A list of `position` term ids', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
					'skills'             => [
						'description' => __( 'A list of `skill` term ids', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
					'unions'             => [
						'description' => __( 'A list of `union` term ids', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
					'locations'          => [
						'description' => __( 'A list of `location` term ids', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
					'experienceLevels'   => [
						'description' => __( 'A list of `experience_level` term ids', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
					'genderIdentities'   => [
						'description' => __( 'A list of `gender_identity` term ids', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
					'racialIdentities'   => [
						'description' => __( 'A list of `racial_identity` term ids', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
					'personalIdentities' => [
						'description' => __( 'A list of `personal_identity` term ids', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
				],
			]
		);

		/**
		 * CreditOutput output type.
		 */
		register_graphql_object_type(
			'CreditOutput',
			[
				'description' => __( 'A credit prepared for the frontend.', 'rise' ),
				'fields'      => [
					'databaseId'  => [
						'type'        => 'ID',
						'description' => __( 'The credit\'s ID.', 'rise' ),
					],
					'index'       => [
						'type'        => 'ID',
						'description' => __( 'The credit\'s display index.', 'rise' ),
					],
					'title'       => [
						'type'        => 'String',
						'description' => __( 'The credit\'s title.', 'rise' ),
					],
					'jobTitle'    => [
						'type'        => 'String',
						'description' => __( 'The credit\'s job title.', 'rise' ),
					],
					'jobLocation' => [
						'type'        => 'String',
						'description' => __( 'The credit\'s location.', 'rise' ),
					],
					'venue'       => [
						'type'        => 'String',
						'description' => __( 'The credit\'s venue.', 'rise' ),
					],
					'year'        => [
						'type'        => 'String',
						'description' => __( 'The credit\'s year.', 'rise' ),
					],
					'workStart'   => [
						'type'        => 'String',
						'description' => __( 'The credit\'s work start date.', 'rise' ),
					],
					'workEnd'     => [
						'type'        => 'String',
						'description' => __( 'The credit\'s work end date.', 'rise' ),
					],
					'workCurrent' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the this is a current job.', 'rise' ),
					],
					'departments' => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The credit\'s 1st-level position term IDs.', 'rise' ),
					],
					'jobs'        => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The credit\'s 2nd-level position term IDs.', 'rise' ),
					],
					'skills'      => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The credit\'s skill terms.', 'rise' ),
					],
				],
			]
		);

		/**
		 * PositionOutput output type.
		 */
		register_graphql_object_type(
			'PositionOutput',
			[
				'description' => __( 'A position term prepared for the frontend.', 'rise' ),
				'fields'      => [
					'databaseId'       => [
						'type'        => 'ID',
						'description' => __( 'The term ID.', 'rise' ),
					],
					'parentDatabaseId' => [
						'type'        => 'ID',
						'description' => __( 'The term parent ID.', 'rise' ),
					],
					'name'             => [
						'type'        => 'String',
						'description' => __( 'The term name.', 'rise' ),
					],
					'slug'             => [
						'type'        => 'String',
						'description' => __( 'The term slug.', 'rise' ),
					],
				],
			]
		);
	}
}
