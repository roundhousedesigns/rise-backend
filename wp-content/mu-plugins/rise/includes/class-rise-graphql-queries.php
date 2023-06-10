<?php

/**
 * Registers GraphQL queries.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Rise_GraphQL_Queries {
	/**
	 * Register GraphQL queries.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_queries() {
		$this->register_fields();
	}

	/**
	 * Prepare taxonomy terms for GraphQL.
	 *
	 * @param  \WPGraphQL\Model\User $user
	 * @param  string                $taxonomy
	 * @return void
	 */
	private static function prepare_taxonomy_terms( $user_id, $taxonomy ) {
		// Use the more general wp_get_object_terms instead of get_the_terms
		// to ensure User object support.
		$terms = wp_get_object_terms( $user_id, $taxonomy );

		if ( !$terms ) {
			return [];
		}

		$prepared_terms = [];

		foreach ( $terms as $term ) {
			$prepared_terms[] = [
				'databaseId' => $term->term_id,
				'name'       => $term->name,
				'slug'       => $term->slug,
			];
		}

		return $prepared_terms;
	}

	/**
	 * Register GraphQL queries for skills.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_fields() {
		/**
		 * Query for skills related to the selected jobs and departments.
		 */
		register_graphql_field(
			'RootQuery',
			'jobSkills',
			[
				'type'        => ['list_of' => 'Skill'],
				'description' => __( 'The skills related to the selected jobs and departments.', 'rise' ),
				'args'        => [
					'jobs' => [
						'type' => ['list_of' => 'ID'],
					],
				],
				'resolve'     => function ( $root, $args ) {
					$selected_skills = [];
					foreach ( $args['jobs'] as $job ) {
						$pod       = pods( 'position', $job );
						$retrieved = $pod->field( 'skills' );

						if ( !$retrieved ) {
							return [];
						}

						$selected_skills[] = wp_list_pluck( $retrieved, 'term_id' );
					}

					$term_args = [
						'include'  => $selected_skills[0],
						'number'   => 0,
						'taxonomy' => 'skill',
					];
					$skill_terms = get_terms( $term_args );

					$prepared_skills = [];
					foreach ( $skill_terms as $skill ) {
						$prepared_skills[] = [
							'databaseId' => $skill->term_id,
							'name'       => $skill->name,
							'slug'       => $skill->slug,
						];
					}

					return $prepared_skills;
				},
			],
		);

		register_graphql_field(
			'RootQuery',
			'jobsByDepartments',
			[
				'type'        => ['list_of' => 'PositionOutput'],
				'description' => __( 'The jobs related to the selected departments.', 'rise' ),
				'args'        => [
					'departments' => [
						'type' => ['list_of' => 'ID'],
					],
				],
				'resolve'     => function ( $root, $args ) {
					$departments = $args['departments'];

					if ( count( $departments ) === 1 && 0 === absint( $departments[0] ) ) {
						// retrieve only top-level terms for `position`
						$terms = get_terms( [
							'taxonomy'   => 'position',
							'hide_empty' => false,
							'parent'     => 0,
						] );
					} elseif ( empty( $departments ) ) {
						// If no departments are selected, return an empty array.
						return [];
					} else {
						// get all terms that are children of any of the term ids in the `$departments` array
						$all_children = [];
						foreach ( $departments as $department ) {
							$children = get_term_children( $department, 'position' );
							if ( !empty( $children ) ) {
								$all_children = array_merge( $all_children, $children );
							}
						}

						$terms = get_terms( [
							'taxonomy'   => 'position',
							'hide_empty' => false,
							'include'    => $all_children,
						] );
					}

					$prepared_terms = [];

					foreach ( $terms as $term ) {
						$prepared_terms[] = [
							'databaseId'       => $term->term_id,
							'parentDatabaseId' => $term->parent,
							'name'             => $term->name,
							'slug'             => $term->slug,
						];
					}

					return $prepared_terms;
				},
			]
		);

		/**
		 * Query for users by name.
		 */
		register_graphql_field(
			'RootQuery',
			'usersByName',
			[
				'type'        => ['list_of' => 'Int'],
				'description' => __( 'Query users by name', 'rise' ),
				'args'        => [
					'name' => [
						'type' => 'String',
					],
				],
				'resolve'     => function ( $root, $args ) {
					$search_string = sanitize_text_field( $args['name'] );

					// Prepare an array of arguments for the user query
					$query_args = [
						'search'         => '*' . $search_string . '*',
						'search_columns' => [
							'meta_value', // Assumes first name and last name are stored as user meta
							// 'user_email',
							// 'user_display_name',
						],
						'role__in'       => ['crew-member'],
					];

					// Create a new instance of WP_User_Query
					$user_query = new WP_User_Query( $query_args );

					// Retrieve the results
					$users = $user_query->get_results();

					// Process the results
					$user_ids = [];

					if ( !empty( $users ) ) {
						foreach ( $users as $user ) {
							$user_ids[] = $user->ID;
						}
					}

					return array_filter( array_unique( $user_ids ), 'remove_incomplete_profiles_from_search' );
				},
			],
		);

		/**
		 * Query for users with matching selected criteria.
		 */
		register_graphql_field(
			'RootQuery',
			'filteredCandidates',
			[
				'type'        => ['list_of' => 'Int'],
				'description' => __( 'Users with matching selected criteria.', 'rise' ),
				'args'        => [
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
					'exclude'            => [
						'description' => __( 'Deprecated. A list of user ids to exclude (was used for the current user)', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
				],
				'resolve'     => function ( $root, $args ) {
					return search_and_filter_crew_members( $args );
				},
			],
		);

		/**
		 * Query for users with matching terms from the `union` taxonomy.
		 */
		register_graphql_field(
			'User',
			'unions',
			[
				'type'        => ['list_of' => 'Union'],
				'description' => __( 'The user\'s selected union terms.', 'rise' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'union' );
				},
			]
		);

		/**
		 * Query for users with matching terms from the `union` taxonomy.
		 */
		register_graphql_field(
			'User',
			'locations',
			[
				'type'        => ['list_of' => 'Location'],
				'description' => __( 'The user\'s selected location.', 'rise' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'location' );
				},
			]
		);

		/**
		 * Query for users with matching terms from the `genderIdentity` taxonomy.
		 */
		register_graphql_field(
			'User',
			'experienceLevels',
			[
				'type'        => ['list_of' => 'Experience_Level'],
				'description' => __( 'The user\'s selected experience level terms.', 'rise' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'experience_level' );
				},
			]
		);

		/**
		 * Query for users with matching terms from the `partnerDirectory` taxonomy.
		 */
		register_graphql_field(
			'User',
			'partnerDirectories',
			[
				'type'        => ['list_of' => 'Partner_Directory'],
				'description' => __( 'The user\'s selected partner directory terms.', 'rise' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'partner_directory' );
				},
			]
		);

		/**
		 * Query for users with matching terms from the `genderIdentity` taxonomy.
		 */
		register_graphql_field(
			'User',
			'genderIdentities',
			[
				'type'        => ['list_of' => 'Gender_Identity'],
				'description' => __( 'The user\'s selected gender identity terms.', 'rise' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'gender_identity' );
				},
			]
		);

		/**
		 * Query for users with matching terms from the `racialIdentity` taxonomy.
		 */
		register_graphql_field(
			'User',
			'racialIdentities',
			[
				'type'        => ['list_of' => 'Racial_Identity'],
				'description' => __( 'The user\'s selected racial identity terms.', 'rise' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'racial_identity' );
				},
			]
		);

		/**
		 * Query for users with matching selected criteria.
		 */
		register_graphql_field(
			'User',
			'personalIdentities',
			[
				'type'        => ['list_of' => 'Personal_Identity'],
				'description' => __( 'The user\'s seelcted personal identity terms.', 'rise' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'personal_identity' );
				},
			]
		);

		/**
		 * Query user by slug.
		 */
		register_graphql_field(
			'RootQuery',
			'userBySlug',
			[
				'type'        => 'Int',
				'description' => __( 'Get a user ID by their slug.', 'rise' ),
				'args'        => [
					'slug' => [
						'description' => __( 'The slug of the user to return.', 'rise' ),
						'type'        => 'String',
					],
				],
				'resolve'     => function ( $root, $args ) {
					$user = get_user_by( 'slug', $args['slug'] );

					if ( !$user ) {
						return;
					}

					// TODO Ultimately, this should return the whole profile data set, not just the ID.
					// Right now, we're running a separate query by ID to get the full profile data.

					return $user->ID;
				},
			]
		);
	}
}
