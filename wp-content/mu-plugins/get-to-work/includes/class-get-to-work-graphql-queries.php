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

// FIXME phpfmt is shifting comments to start of line

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
				// TODO change to 'id'
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

					// MAYBE run individiual queries for each job and merge the results to enable job-weighted sort of results.
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

		/**
		 * Query for users with matching selected criteria.
		 */
		register_graphql_field(
			'RootQuery',
			'filteredCandidates',
			[
				'type'        => ['list_of' => 'Int'],
				'description' => __( 'Users with matching selected criteria.', 'gtw' ),
				'args'        => [
					'jobs'    => [
						'description' => __( 'A list of `position` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'skills'  => [
						'description' => __( 'A list of `skill` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'exclude' => [
						'description' => __( 'A list of user ids to exclude', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					// TODO Add more filter args.
				],
				'resolve'     => function ( $root, $args ) {
					$selected_job_ids   = isset( $args['jobs'] ) ? $args['jobs'] : '';
					$selected_skill_ids = isset( $args['skills'] ) ? $args['skills'] : '';

					// If no terms were sent, return an empty array.
					if ( ! $selected_job_ids ) {
						return [];
					}

					$credit_args = [
						'post_type' => 'credit',
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						'tax_query' => ['relation' => 'AND'],
					];

					if ( $selected_job_ids ) {
						$credit_args['tax_query'][] = [
							'taxonomy' => 'position',
							'field'    => 'term_id',
							'terms'    => $selected_job_ids,
						];
					}

					if ( $selected_skill_ids ) {
						$credit_args['tax_query'][] = [
							'taxonomy' => 'skill',
							'field'    => 'term_id',
							'terms'    => $selected_skill_ids,
						];
					}

					// Query credits with the desired attributes.
					$credits = get_posts( $credit_args );

					// If no credits are found, return an empty array.
					if ( empty( $credits ) ) {
						return [];
					}

					// Get the authors of the credits.
					$authors = [];

					foreach ( $credits as $credit ) {
						$authors[] = $credit->post_author;
					}

					// Filter out any excluded users.
					if ( isset( $args['exclude'] ) ) {
						$authors = array_diff( $authors, $args['exclude'] );
					}

					$users    = [];
					$user_ids = [];

					// Get the user objects.
					if ( ! empty( $authors ) ) {
						$users = get_users( [
							'include' => $authors,
						] );
					}

					if ( $users ) {
						foreach ( $users as $user ) {
							$user_ids[] = $user->ID;
						}
					}

					return $user_ids;
				},
			],
		);

		/**
		 * Query for users with matching terms from the `union` taxonomy.
		 */
		register_graphql_field( 'User', 'unions', [
			'type'        => ['list_of' => 'Union'],
			'description' => __( 'The user\'s selected union terms.', 'gtw' ),
			'resolve'     => function ( $user ) {
				return self::prepare_taxonomy_terms( $user->fields['userId'], 'union' );
			},
		] );

		/**
		 * Query for users with matching terms from the `genderIdentity` taxonomy.
		 */
		register_graphql_field( 'User', 'genderIdentities', [
			'type'        => ['list_of' => 'Gender_Identity'],
			'description' => __( 'The user\'s selected gender identity terms.', 'gtw' ),
			'resolve'     => function ( $user ) {
				return self::prepare_taxonomy_terms( $user->fields['userId'], 'gender_identity' );
			},
		] );

		/**
		 * Query for users with matching terms from the `racialIdentity` taxonomy.
		 */
		register_graphql_field( 'User', 'racialIdentities', [
			'type'        => ['list_of' => 'Racial_Identity'],
			'description' => __( 'The user\'s selected racial identity terms.', 'gtw' ),
			'resolve'     => function ( $user ) {
				return self::prepare_taxonomy_terms( $user->fields['userId'], 'racial_identity' );
			},
		] );

		/**
		 * Query for users with matching selected criteria.
		 */
		register_graphql_field( 'User', 'personalIdentities', [
			'type'        => ['list_of' => 'Personal_Identity'],
			'description' => __( 'The user\'s seelcted personal identity terms.', 'gtw' ),
			'resolve'     => function ( $user ) {
				return self::prepare_taxonomy_terms( $user->fields['userId'], 'personal_identity' );
			},
		] );
	}
}
