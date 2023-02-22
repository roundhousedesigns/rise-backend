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
	public function register_types() {
		$this->register_fields();
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
					'department' => [
						'type' => 'String',
					],
					'jobs'       => [
						'type' => ['list_of' => 'ID'],
					],
				],
				'resolve'     => function ( $source, $args ) {
					$selected_dept_id = intval( $args['department'] );
					$selected_job_ids = $args['jobs'];

					$skill_terms = get_terms( [
						'taxonomy'   => 'skill',
						'hide_empty' => false,
						// phpcs:ignore
						'meta_query' => [
							'relation' => 'OR',
							[
								'key'     => 'department',
								'value'   => $selected_dept_id,
								'compare' => '=',
							],
							[
								'key'     => 'jobs',
								'value'   => $selected_job_ids,
								'compare' => 'IN',
							],
						],
					] );

					$sorted_skill_terms = [];
					foreach ( $skill_terms as $skill_term ) {
						$departments = get_term_meta( $skill_term->term_id, 'departments', false );
						$jobs        = get_term_meta( $skill_term->term_id, 'jobs', false );

						if ( array_intersect( $selected_job_ids, $jobs ) ) {
							$sorted_skill_terms[] = [
								'databaseId' => $skill_term->term_id,
								'name'       => $skill_term->name,
								'slug'       => $skill_term->slug,
							];
						} elseif ( array_intersect( [$selected_dept_id], $departments ) ) {
							$sorted_skill_terms[] = [
								'databaseId' => $skill_term->term_id,
								'name'       => $skill_term->name,
								'slug'       => $skill_term->slug,
							];
						}
					}

					$sorted_skill_terms = array_unique( $sorted_skill_terms, SORT_REGULAR );

					return $sorted_skill_terms;
				},
			],
		);

		register_graphql_field(
			'RootQuery',
			'filteredCandidates',
			[
				'type'        => ['list_of' => 'Int'],
				'description' => __( 'Users with matching selected criteria.', 'gtw' ),
				'args'        => [
					'department' => [
						'description' => __( 'A top level `position` term_id', 'gtw' ),
						'type'        => 'String',
					],
					'jobs'       => [
						'description' => __( 'A list of `position` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					'skills'     => [
						'description' => __( 'A list of `skill` term ids', 'gtw' ),
						'type'        => ['list_of' => 'ID'],
					],
					// TODO Add more filter args.
				],
				'resolve'     => function ( $source, $args ) {
					$selected_department_id = isset( $args['department'] ) ? $args['department'] : '';
					$selected_job_ids       = isset( $args['jobs'] ) ? $args['jobs'] : '';
					$selected_skill_ids     = isset( $args['skills'] ) ? $args['skills'] : '';

					// If no terms were sent, return an empty array.
					if ( ! $selected_department_id && ! $selected_job_ids && ! $selected_skill_ids ) {
						return [];
					}

					$credit_args = [
						'post_type' => 'credit',
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						'tax_query' => ['relation' => 'AND'],
					];

					// Check filters and add to query.
					if ( $selected_department_id ) {
						$credit_args['tax_query'][] = [
							'taxonomy'         => 'position',
							'field'            => 'term_id',
							'include_children' => false,
							'terms'            => $selected_department_id,
						];
					}
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

					// Get the user objects.
					$users = get_users( [
						'include' => $authors,
					] );

					// Prepare users for GraphQL.
					// TODO move this to a function.

					$prepared_users = [];
					foreach ( $users as $user ) {
						$prepared_users[] = $user->ID;
					}

					return $prepared_users;
				},
			],
		);
	}
}
