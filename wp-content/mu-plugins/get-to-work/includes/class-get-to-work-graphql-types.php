<?php
/**
 * Register GraphQL object types, connections, interfaces, etc.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Get_To_Work_GraphQL_Types {
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
				'description' => __( 'A user\'s personal links.', 'gtw' ),
				'fields'      => [
					'website'   => [
						'type'        => 'String',
						'description' => __( 'The user\'s website.', 'gtw' ),
					],
					'facebook'  => [
						'type'        => 'String',
						'description' => __( 'The user\'s Facebook profile.', 'gtw' ),
					],
					'twitter'   => [
						'type'        => 'String',
						'description' => __( 'The user\'s Twitter profile.', 'gtw' ),
					],
					'linkedin'  => [
						'type'        => 'String',
						'description' => __( 'The user\'s LinkedIn profile.', 'gtw' ),
					],
					'instagram' => [
						'type'        => 'String',
						'description' => __( 'The user\'s Instagram profile.', 'gtw' ),
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
				'description' => __( 'A user profile.', 'gtw' ),
				'fields'      => [
					'id'                 => [
						'type'        => 'ID',
						'description' => __( 'The user\'s ID.', 'gtw' ),
					],
					'firstName'          => [
						'type'        => 'String',
						'description' => __( 'The user\'s first name.', 'gtw' ),
					],
					'lastName'           => [
						'type'        => 'String',
						'description' => __( 'The user\'s last name.', 'gtw' ),
					],
					'pronouns'           => [
						'type'        => 'String',
						'description' => __( 'The user\'s pronouns.', 'gtw' ),
					],
					'email'              => [
						'type'        => 'String',
						'description' => __( 'The user\'s contact email.', 'gtw' ),
					],
					'selfTitle'          => [
						'type'        => 'String',
						'description' => __( 'The user\'s self title.', 'gtw' ),
					],
					'homebase'           => [
						'type'        => 'String',
						'description' => __( 'The user\'s homebase.', 'gtw' ),
					],
					'image'              => [
						'type'        => 'String',
						'description' => __( 'The user\'s image.', 'gtw' ),
					],
					'phone'              => [
						'type'        => 'String',
						'description' => __( 'The user\'s phone number.', 'gtw' ),
					],
					'description'        => [
						'type'        => 'String',
						'description' => __( 'The user\'s bio.', 'gtw' ),
					],
					'locations'          => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s location.', 'gtw' ),
					],
					'resume'             => [
						'type'        => 'String',
						'description' => __( 'The user\'s resume.', 'gtw' ),
					],
					'willTravel'         => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the user will travel.', 'gtw' ),
					],
					// 'media'        => [
					// 	'type'        => 'String',
					// 	'description' => __( 'The user\'s media.', 'gtw' ),
					// ],
					'education'          => [
						'type'        => 'String',
						'description' => __( 'The user\'s education.', 'gtw' ),
					],
					'unions'             => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s unions.', 'gtw' ),
					],
					'experienceLevels'   => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s racial identities.', 'gtw' ),
					],
					'racialIdentities'   => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s racial identities.', 'gtw' ),
					],
					'genderIdentities'   => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s gender identities.', 'gtw' ),
					],
					'personalIdentities' => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The user\'s additional personal identities.', 'gtw' ),
					],
					'socials'            => [
						'type'        => 'PersonalLinks',
						'description' => __( 'The user\'s social media and external web links.', 'gtw' ),
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
				'description' => __( 'A new or updated credit.', 'gtw' ),
				'fields'      => [
					'id'          => [
						'type'        => 'ID',
						'description' => __( 'The credit\'s ID.', 'gtw' ),
					],
					'index'       => [
						'type'        => 'ID',
						'description' => __( 'The credit\'s display index.', 'gtw' ),
					],
					'title'       => [
						'type'        => 'String',
						'description' => __( 'The credit\'s title.', 'gtw' ),
					],
					'jobTitle'    => [
						'type'        => 'String',
						'description' => __( 'The credit\'s job title.', 'gtw' ),
					],
					'jobLocation' => [
						'type'        => 'String',
						'description' => __( 'The credit\'s location.', 'gtw' ),
					],
					'venue'       => [
						'type'        => 'String',
						'description' => __( 'The credit\'s venue.', 'gtw' ),
					],
					'year'        => [
						'type'        => 'String',
						'description' => __( 'The credit\'s year.', 'gtw' ),
					],
					'positions'   => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The credit\'s position term IDs.', 'gtw' ),
					],
					'skills'      => [
						'type'        => ['list_of' => 'ID'],
						'description' => __( 'The credit\'s skill term IDs.', 'gtw' ),
					],
					'isNew'       => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the credit is a new entry.', 'gtw' ),
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
				'description' => __( 'A credit prepared for the frontend.', 'gtw' ),
				'fields'      => [
					'databaseId'  => [
						'type'        => 'ID',
						'description' => __( 'The credit\'s ID.', 'gtw' ),
					],
					'index'       => [
						'type'        => 'ID',
						'description' => __( 'The credit\'s display index.', 'gtw' ),
					],
					'title'       => [
						'type'        => 'String',
						'description' => __( 'The credit\'s title.', 'gtw' ),
					],
					'jobTitle'    => [
						'type'        => 'String',
						'description' => __( 'The credit\'s job title.', 'gtw' ),
					],
					'jobLocation' => [
						'type'        => 'String',
						'description' => __( 'The credit\'s location.', 'gtw' ),
					],
					'venue'       => [
						'type'        => 'String',
						'description' => __( 'The credit\'s venue.', 'gtw' ),
					],
					'year'        => [
						'type'        => 'String',
						'description' => __( 'The credit\'s year.', 'gtw' ),
					],
					// TODO department: change to plural key
					'department'  => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The credit\'s 1st-level position term IDs.', 'gtw' ),
					],
					'jobs'        => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The credit\'s 2nd-level position term IDs.', 'gtw' ),
					],
					'skills'      => [
						'type'        => ['list_of' => 'Int'],
						'description' => __( 'The credit\'s skill terms.', 'gtw' ),
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
				'description' => __( 'A position term prepared for the frontend.', 'gtw' ),
				'fields'      => [
					'databaseId'       => [
						'type'        => 'ID',
						'description' => __( 'The term ID.', 'gtw' ),
					],
					'parentDatabaseId' => [
						'type'        => 'ID',
						'description' => __( 'The term parent ID.', 'gtw' ),
					],
					'name'             => [
						'type'        => 'String',
						'description' => __( 'The term name.', 'gtw' ),
					],
					'slug'             => [
						'type'        => 'String',
						'description' => __( 'The term slug.', 'gtw' ),
					],
				],
			]
		);
	}
}
