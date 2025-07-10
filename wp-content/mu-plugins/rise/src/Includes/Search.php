<?php

namespace RHD\Rise\Includes;

class Search {
	/**
	 * Query crew members based on the given arguments.
	 *
	 * Used by the frontend search filters, and by reporting functions.
	 *
	 * @param  array $args
	 * @return int[] The user IDs.
	 */
	public static function search_and_filter_crew_members( $args ) {
		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_tax_query

		$user_id = get_current_user_id();

		// Save args for future recall
		// TODO Complete saved search history feature.
		// Users::save_user_search_history( $user_id, $args );

		$credit_filters = [
			'position' => isset( $args['positions'] ) ? $args['positions'] : '',
			'skill'    => isset( $args['skills'] ) ? $args['skills'] : '',
		];

		$user_filters = self::translate_taxonomy_filters( $args );

		// Start building the Credit query args.
		$credit_args = [
			'post_type'      => 'credit',
			'tax_query'      => ['relation' => 'AND'],
			'posts_per_page' => -1,
			'orderby'        => 'rand',
		];

		foreach ( $credit_filters as $taxonomy => $selected_terms ) {
			$terms = $selected_terms;

			if ( 'integer' === gettype( $terms ) ) {
				$terms = [$terms];
			}

			if ( !empty( $terms ) ) {
				if ( 'position' === $taxonomy ) {
					foreach ( $terms as $term_id ) {
						// Get 'also_search' terms add them to the query.
						$pod     = \pods( 'position', $term_id );
						$related = $pod->field( 'also_search' );

						// No related terms.
						if ( empty( $related ) || !$related ) {
							continue;
						}

						// Add the related terms to the query. Scoring happens elsewhere, so it's not affected
						// by the query additions.
						foreach ( $related as $term ) {
							$terms[] = $term['term_id'];
						}
					}
				}

				$credit_args['tax_query'][] = [
					'taxonomy'         => $taxonomy,
					'field'            => 'term_id',
					'terms'            => array_unique( $terms ),
					'include_children' => true,
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

		// Filter out authors with no name set, or no contact info.
		$authors = array_filter( array_unique( $authors ), [self::class, 'remove_incomplete_profiles_from_search'] );

		// Filter users by selected taxonomies.
		$user_taxonomy_term_ids = [];
		foreach ( $user_filters as $tax => $term_ids ) {
			if ( empty( $term_ids ) ) {
				continue;
			}

			$user_taxonomy_term_ids[$tax] = $term_ids;
		}

		return Users::query_users( $user_taxonomy_term_ids, $authors );
	}

	// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_tax_query

	/**
	 * Filters out user profiles which don't have either a first or last name, or which have no contact information.
	 *
	 * Used to prevent incomplete profiles from appearing in search.
	 *
	 * @param  int     $author_id
	 * @return boolean The result of the filter operation.
	 */
	public static function remove_incomplete_profiles_from_search( $author_id ) {
		$meta = get_user_meta( $author_id );
		if ( !$meta['first_name'][0] && !$meta['last_name'][0] ) {
			return false;
		}

		return true;
	}

	/**
	 * Translate the frontend search filters to backend query arguments.
	 *
	 * @param  array $args The frontend search filters.
	 * @return array The arguments for use in a WP_Query.
	 */
	public static function translate_taxonomy_filters( $args ) {
		return [
			'union'             => isset( $args['unions'] ) ? $args['unions'] : '',
			'location'          => isset( $args['locations'] ) ? $args['locations'] : '',
			'experience_level'  => isset( $args['experienceLevels'] ) ? $args['experienceLevels'] : '',
			'gender_identity'   => isset( $args['genderIdentities'] ) ? $args['genderIdentities'] : '',
			'personal_identity' => isset( $args['personalIdentities'] ) ? $args['personalIdentities'] : '',
			'racial_identity'   => isset( $args['racialIdentities'] ) ? $args['racialIdentities'] : '',
		];
	}

	/**
	 * Filter job posts query based on expired status
	 *
	 * @param  WP_Query $query   The WP_Query instance
	 * @return WP_Query Modified query
	 */
	public function filter_job_posts_query( $query ) {
		// Only modify queries in admin and for job posts
		if ( !is_admin() || !$query->is_main_query() || $query->get( 'post_type' ) !== 'job_post' ) {
			return $query;
		}

		// If expired view is selected
		if ( isset( $_GET['expired'] ) ) {
			$query->set( 'meta_query', [
				[
					'key'     => 'expired',
					'value'   => '1',
					'compare' => '=',
				],
			] );
		} else {
			// Exclude expired posts from "All" view
			$query->set( 'meta_query', [
				'relation' => 'OR',
				[
					'key'     => 'expired',
					'value'   => '0',
					'compare' => '=',
				],
				[
					'key'     => 'expired',
					'compare' => 'NOT EXISTS',
				],
			] );
		}

		return $query;
	}
}
