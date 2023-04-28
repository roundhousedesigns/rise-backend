<?php

/**
 * Registers GraphQL queries.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Get_To_Work_GraphQL_Queries {
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
		$terms = get_the_terms( $user_id, $taxonomy );

		if ( ! $terms ) {
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
				'description' => __( 'The skills related to the selected jobs and departments.', 'gtw' ),
				'args'        => [
					'jobs' => [
						'type' => ['list_of' => 'ID'],
					],
				],
				'resolve'     => function ( $root, $args ) {
					$skill_query_args = [
						'taxonomy'   => 'skill',
						'hide_empty' => false,
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						'meta_query' => [
							'relation' => 'OR',
						],
					];

					// MAYBE run individiual queries for each job and merge the results to enable job-weighted sorting of results.
					foreach ( $args['jobs'] as $job ) {
						$skill_query_args['meta_query'][] = [
							'key'     => 'jobs',
							'value'   => (string) $job,
							'compare' => 'LIKE',
						];
					}

					$skill_terms     = get_terms( $skill_query_args );
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
				'description' => __( 'The jobs related to the selected departments.', 'gtw' ),
				'args'        => [
					'departments' => [
						'type' => ['list_of' => 'ID'],
					],
				],
				'resolve'     => function ( $root, $args ) {
					$departments = $args['departments'];
					if ( count( $departments ) === 1 && empty( $departments[0] ) ) {
						// retrieve only top-level terms for `position`
						$terms = get_terms( [
							'taxonomy'   => 'position',
							'hide_empty' => false,
							'parent'     => 0,
						] );
					} else {
						// get all terms that are children of any of the term ids in the `$departments` array
						$all_children = [];
						foreach ( $departments as $department ) {
							$children = get_term_children( $department, 'position' );
							if ( ! empty( $children ) ) {
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
		 * Query for users with matching selected criteria.
		 */
		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		register_graphql_field(
			'RootQuery',
			'filteredCandidates',
			[
				'type'        => ['list_of' => 'Int'],
				'description' => __( 'Users with matching selected criteria.', 'gtw' ),
				'args'        => [
					'positions'          => [
						'description' => __( 'A list of `position` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'skills'             => [
						'description' => __( 'A list of `skill` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'unions'             => [
						'description' => __( 'A list of `union` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'locations'          => [
						'description' => __( 'A list of `location` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'experienceLevels'   => [
						'description' => __( 'A list of `experience_level` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'genderIdentities'   => [
						'description' => __( 'A list of `gender_identity` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'racialIdentities'   => [
						'description' => __( 'A list of `racial_identity` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'personalIdentities' => [
						'description' => __( 'A list of `personal_identity` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'exclude'            => [
						'description' => __( 'A list of user ids to exclude (for now, used for the current user)', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
				],
				'resolve'     => function ( $root, $args ) {
					$credit_filters = [
						'position' => isset( $args['positions'] ) ? $args['positions'] : '',
						'skill'    => isset( $args['skills'] ) ? $args['skills'] : '',
					];

					$user_filters = [
						'union'             => isset( $args['unions'] ) ? $args['unions'] : '',
						'location'          => isset( $args['locations'] ) ? $args['locations'] : '',
						'experience_level'  => isset( $args['experienceLevels'] ) ? $args['experienceLevels'] : '',
						'gender_identity'   => isset( $args['genderIdentities'] ) ? $args['genderIdentities'] : '',
						'personal_identity' => isset( $args['personalIdentities'] ) ? $args['personalIdentities'] : '',
						'racial_identity'   => isset( $args['racialIdentities'] ) ? $args['racialIdentities'] : '',
					];

					// Start building the Credit query args.
					$credit_args = [
						'post_type' => 'credit',
						'tax_query' => ['relation' => 'AND'],
					];

					foreach ( $credit_filters as $taxonomy => $terms ) {
						if ( ! empty( $terms ) ) {
							$credit_args['tax_query'][] = [
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $terms,
							];
						}
					}

					// Query credits with the desired attributes.
					$credits = get_posts( $credit_args );

					// If no credits are found, return an empty array.
					if ( empty( $credits ) ) {
						return [];
					}

					// Collect the credit authors.
					$authors = [];
					foreach ( $credits as $credit ) {
						$authors[] = $credit->post_author;
					}

					// Filter out any excluded users.
					if ( isset( $args['exclude'] ) ) {
						$authors = array_diff( $authors, $args['exclude'] );
					}

					// Filter out authors with no name set, or no contact info.
					$authors = array_filter( array_unique( $authors ), 'remove_incomplete_profiles_from_search' );

					// Filter users by selected taxonomies.
					$user_taxonomy_terms = [];
					foreach ( $user_filters as $tax => $term_ids ) {
						if ( empty( $term_ids ) ) {
							continue;
						}

						$user_taxonomy_terms[$tax] = $term_ids;
					}

					// Filter users by taxonomy
					$filtered_authors = query_users_with_terms( $user_taxonomy_terms, $authors );

					return $filtered_authors;
				},
			],
		);
		// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_tax_query

		/**
		 * Query for users with matching terms from the `union` taxonomy.
		 */
		register_graphql_field(
			'User',
			'unions',
			[
				'type'        => ['list_of' => 'Union'],
				'description' => __( 'The user\'s selected union terms.', 'gtw' ),
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
				'description' => __( 'The user\'s selected location.', 'gtw' ),
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
				'description' => __( 'The user\'s selected experience level terms.', 'gtw' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'experience_level' );
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
				'description' => __( 'The user\'s selected gender identity terms.', 'gtw' ),
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
				'description' => __( 'The user\'s selected racial identity terms.', 'gtw' ),
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
				'description' => __( 'The user\'s seelcted personal identity terms.', 'gtw' ),
				'resolve'     => function ( $user ) {
					return self::prepare_taxonomy_terms( $user->fields['userId'], 'personal_identity' );
				},
			]
		);
	}
}
