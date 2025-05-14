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
	 * Register GraphQL queries.`1
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_queries() {
		$this->register_fields();
	}

	/**
	 * Allow access to certain endpoints for non-logged-in users.
	 *
	 * @see @link https://www.wpgraphql.com/recipes/allow-login-mutation-to-be-public-when-the-endpoint-is-fully-restricted
	 * @since 1.0.8
	 *
	 * @param  array $allowed The allowed fields.
	 * @return array The modified allowed fields.
	 */
	public function require_authentication_allowed_fields( $allowed ) {
		$allowed[] = 'loginWithCookiesAndReCaptcha';
		$allowed[] = 'sendPasswordResetEmailWithReCaptcha';
		$allowed[] = 'registerUserWithReCaptcha';
		$allowed[] = 'resetUserPasswordMutation';

		return $allowed;
	}

	/**
	 * Removes the "extensions" data from the GraphQL response.
	 *
	 * @see @link https://www.wpgraphql.com/recipes/remove-extensions-from-graphql-response
	 * @since 1.0.8
	 *
	 * @param  array $response The GraphQL response.
	 * @return array The modified GraphQL response.
	 */
	public function remove_graphql_extensions_response_data( $response ) {
		if ( is_array( $response ) && isset( $response['extensions'] ) ) {
			unset( $response['extensions'] );
		}
		if ( is_object( $response ) && isset( $response->extensions ) ) {
			unset( $response->extensions );
		}
		return $response;
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
	 * Get jobs from departments.
	 *
	 * @param  array $departments
	 * @return array An array of job IDs.
	 */
	private static function get_job_skills( $jobs ) {
		$selected_skills = [];
		foreach ( $jobs as $job ) {
			$pod       = pods( 'position', $job );
			$retrieved = $pod->field( 'skills' );

			if ( !$retrieved ) {
				continue;
			}

			$selected_skills[] = wp_list_pluck( $retrieved, 'term_id' );
		}

		if ( !$selected_skills ) {
			return [];
		}

		// Flatten the array of job-related skills and remove duplicates.
		$selected_skills = array_unique( flatten_array( $selected_skills ) );

		$term_args = [
			'include'    => $selected_skills,
			'number'     => 0,
			'hide_empty' => false,
			'taxonomy'   => 'skill',
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
	}

	/**
	 * Get jobs from departments.
	 *
	 * @param  array $departments
	 * @return array An array of job IDs.
	 */
	private static function get_department_jobs( $departments ) {
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
	}

	/**
	 * Search users by name.
	 *
	 * @param  string $name
	 * @return array  An array of user IDs.
	 */
	private static function search_users_by_name( $name ) {
		$search_string = sanitize_text_field( $name );

		// Prepare an array of arguments for the user query
		$query_args = [
			'search' => '*' . $search_string . '*',
			'role'   => 'crew-member',
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

		// Remove incomplete profiles
		$user_ids = array_filter( array_unique( $user_ids ), 'rise_remove_incomplete_profiles_from_search' );

		// Remove users with the 'disable_profile' pod meta set to true
		$user_ids = array_filter( $user_ids, function ( $id ) {
			$pod = pods( 'user', $id );

			return boolval( $pod->field( 'disable_profile' ) ) === false;
		} );

		return $user_ids;
	}

	/**
	 * Score filtered candidates based on the given search terms.
	 *
	 * @param  array $args          The search terms.
	 * @param  array $candidate_ids The candidate user IDs.
	 * @return array The scored candidates.
	 */
	private static function rise_score_search_results( $args, $candidate_ids ) {
		if ( empty( $candidate_ids ) ) {
			return [];
		}

		// Set up the scoring array with user IDs as keys and starting score of 0 as values.
		$users = [];
		foreach ( $candidate_ids as $id ) {
			$users[$id] = 0;
		}

		$positions    = [];
		$skills       = [];
		$_departments = [];
		/**
		 * Split positions into departments and jobs. If no jobs are present,
		 * we'll score based on departments.
		 */
		$skills = empty( $args['skills'] ) ? [] : $args['skills'];

		foreach ( $args['positions'] as $position_id ) {
			$position = get_term_by( 'id', $position_id, 'position' );

			if ( $position ) {
				if ( $position->parent ) {
					$positions[] = $position->term_id;
				} else {
					$_departments[] = $position->term_id;
				}
			}
		}

		$positions = !empty( $positions ) ? $positions : $_departments;

		// Score candidates based on positions, skills, and filters.
		foreach ( $users as $user_id => $score ) {
			// Get the user's credits.
			$credits = get_posts( [
				'post_type'      => 'credit',
				'posts_per_page' => -1,
				'author'         => $user_id,
			] );

			foreach ( $credits as $credit ) {
				// First, score positions
				foreach ( $positions as $position ) {
					if ( has_term( $position, 'position', $credit->ID ) ) {
						$users[$user_id]++;
					}
				}

				// Next, score skills
				foreach ( $skills as $skill ) {
					if ( has_term( $skill, 'skill', $credit->ID ) ) {
						$users[$user_id]++;
					}
				}
			}

			// Remove 'positions' and 'skills' from the $args array so we don't score them again.
			unset( $args['positions'], $args['skills'] );

			// Score the rest of the filters.
			$filters = rise_translate_taxonomy_filters( $args );

			foreach ( $filters as $user_taxonomy => $term_ids ) {
				if ( empty( $term_ids ) ) {
					continue;
				}

				// Cast the term IDs to integers.
				$term_ids = array_map( 'absint', $term_ids );

				$user_taxonomy_term_ids = wp_get_object_terms( $user_id, $user_taxonomy, ['fields' => 'ids'] );

				foreach ( $term_ids as $term_id ) {
					if ( in_array( $term_id, $user_taxonomy_term_ids, true ) ) {
						$users[$user_id]++;
					}
				}
			}
		}

		// Transform the array into a list conforming to the ScoredCandidateOutput shape.
		$scored_candidates = [];
		foreach ( $users as $user_id => $score ) {
			$scored_candidates[] = [
				'user_id' => $user_id,
				'score'   => $score,
			];
		}

		return $scored_candidates;
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
		 * Query for the WordPress global stylesheet.
		 */
		register_graphql_field(
			'RootQuery',
			'wpGlobalStylesheet',
			[
				'type'        => 'String',
				'description' => __( 'Returns the WordPress global stylesheet', 'rise' ),
				'resolve'     => function () {
					return wp_get_global_stylesheet();
				},
			]
		);

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
					if ( $args['jobs'] && count( $args['jobs'] ) > 0 ) {
						return self::get_job_skills( $args['jobs'] );
					}

					return [];
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
					if ( $args['departments'] && count( $args['departments'] ) > 0 ) {
						return self::get_department_jobs( $args['departments'] );
					}

					return [];
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
					if ( $args['name'] ) {
						return self::search_users_by_name( $args['name'] );
					}

					return [];
				},
			],
		);

		// DEBUG
		register_graphql_field(
			'RootQuery',
			'disabledProfileUsers',
			[
				'type'        => ['list_of' => 'Int'],
				'description' => __( 'Users with disabled profiles.', 'rise' ),
				'resolve'     => function ( $root, $args ) {
					$params = [
						'where' => 'disable_profile = 1',
						'limit' => -1,
					];

					$users        = pods( 'user', $params );
					$disabled_ids = [];

					if ( $users->total() > 0 ) {
						while ( $users->fetch() ) {
							$disabled_ids[] = $users->field( 'ID' );
						}
					}

					return $disabled_ids;
				},
			],
		);

		/**
		 * Query for users with matching selected criteria.
		 *
		 * Returns an associative array of user IDs as keys, with search score as values.
		 */
		register_graphql_field(
			'RootQuery',
			'filteredCandidates',
			[
				'type'        => ['list_of' => 'ScoredCandidateOutput'],
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
					'searchUserId'       => [
						'description' => __( 'The ID of the user performing the search', 'rise' ),
						'type'        => 'ID',
					],
					'exclude'            => [
						'description' => __( 'Deprecate A list of user ids to exclude', 'rise' ),
						'type'        => ['list_of' => 'ID'],
					],
				],
				'resolve'     => function ( $root, $args ) {
					$candidate_ids = rise_search_and_filter_crew_members( $args );

					return self::rise_score_search_results( $args, $candidate_ids );
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
		 * Query for users with matching terms from the `PartnerDirectory` taxonomy.
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
			'userIdBySlug',
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

					// TODO Ultimately, this should return the whole profile data set, not just the I
					// Right now, we're running a separate query by ID to get the full profile data.

					return $user->ID;
				},
			]
		);

		/**
		 * Query page ID by slug.
		 */
		register_graphql_field(
			'RootQuery',
			'pageIdBySlug',
			[
				'type'        => 'Int',
				'description' => __( 'Get a page ID by its slug.', 'rise' ),
				'args'        => [
					'slug' => [
						'description' => __( 'The slug of the page to return.', 'rise' ),
						'type'        => 'String',
					],
				],
				'resolve'     => function ( $root, $args ) {
					$post = get_page_by_path( $args['slug'], OBJECT, 'page' );

					if ( !$post ) {
						return null;
					}

					return $post->ID;
				},
			]
		);

		/**
		 * Get RISE site settings.
		 */
		register_graphql_field(
			'RootQuery',
			'wpStylesheetDirectoryUri',
			[
				'type'        => 'String',
				'description' => __( 'Retrieve the current WP theme\'s stylesheet directory URI.', 'rise' ),
				'resolve'     => function ( $root, $args ) {
					return get_stylesheet_directory_uri();
				},
			]
		);

		/**
		 * Query filtered job posts.
		 */
		register_graphql_field(
			'RootQuery',
			'filteredJobPostIds',
			[
				'type'        => ['list_of' => 'ID'],
				'description' => __( 'Get filtered job post IDs.', 'rise' ),
				'args'        => [
					'internships' => [
						'type'        => 'Boolean',
						'description' => __( 'Filter by internship status', 'rise' ),
					],
					'union'       => [
						'type'        => 'Boolean',
						'description' => __( 'Filter by union status', 'rise' ),
					],
					'paid'        => [
						'type'        => 'Boolean',
						'description' => __( 'Filter by paid status', 'rise' ),
					],
					'status'      => [
						'type'        => ['list_of' => 'String'],
						'description' => __( 'Filter by post status', 'rise' ),
					],
				],
				'resolve'     => function ( $root, $args ) {
					$post_status = isset( $args['status'] ) ? $args['status'] : ['publish'];

					$params = [
						'where' => 't.post_status IN ("' . implode( '", "', $post_status ) . '")',
						'limit' => -1,
					];

					// Only add where conditions for filters that are explicitly set to true
					if ( isset( $args['internships'] ) && $args['internships'] ) {
						$params['where'] .= ' AND is_internship.meta_value = "1"';
					}

					if ( isset( $args['union'] ) && $args['union'] ) {
						$params['where'] .= ' AND is_union.meta_value = "1"';
					}

					if ( isset( $args['paid'] ) && $args['paid'] ) {
						$params['where'] .= ' AND is_paid.meta_value = "1"';
					}

					$jobs = pods( 'job_post' )->find( $params );

					$job_ids = [];

					while ( $jobs->fetch() ) {
						$job_ids[] = $jobs->field( 'ID' );
					}

					if ( !$job_ids ) {
						return [];
					}

					return $job_ids;
				},
			]
		);

		/**
		 * Get the unread notifications for the current user.
		 */
		register_graphql_field(
			'RootQuery',
			'unreadProfileNotifications',
			[
				'type'        => ['list_of' => 'ProfileNotificationOutput'],
				'description' => __( 'Get the notifications for the current user.', 'rise' ),
				'args'        => [
					'authorId' => [
						'type'        => 'Int',
						'description' => __( 'The ID of the user to get notifications for.', 'rise' ),
					],
					'limit'    => [
						'type'        => 'Int',
						'description' => __( 'The number of notifications to return.', 'rise' ),
					],
				],
				'resolve'     => function ( $root, $args ) {

					if ( !$args['authorId'] ) {
						return [];
					}

					return Rise_Profile_Notification::get_profile_notices_for_graphql( $args['authorId'], false );
				},
			]
		);

		/**
		 * Get the read notifications for the current user.
		 */
		register_graphql_field(
			'RootQuery',
			'readProfileNotifications',
			[
				'type'        => ['list_of' => 'ProfileNotificationOutput'],
				'description' => __( 'Get the latest read notifications for the current user.', 'rise' ),
				'args'        => [
					'authorId' => [
						'type'        => 'Int',
						'description' => __( 'The ID of the user to get notifications for.', 'rise' ),
					],
					'limit'    => [
						'type'        => 'Int',
						'description' => __( 'The number of notifications to return.', 'rise' ),
					],
				],
				'resolve'     => function ( $root, $args ) {

					if ( !$args['authorId'] ) {
						return [];
					}

					return Rise_Profile_Notification::get_profile_notices_for_graphql( $args['authorId'], true, $args['limit'] );
				},
			]
		);
	}
}
