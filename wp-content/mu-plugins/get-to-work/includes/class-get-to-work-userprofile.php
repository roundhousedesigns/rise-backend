<?php
/**
 * The UserProfile class.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Get_To_Work_UserProfile {
	/**
	 * The user's ID.
	 *
	 * @var int $id The user ID.
	 * @since 0.1.0
	 */
	public $id;

	/**
	 * The user's initial raw data.
	 *
	 * @var array $raw The user's initial raw data.
	 */
	private $raw;

	/**
	 * User's base data.
	 *
	 * @var array $base The user's base data.
	 * @since 0.1.0
	 */
	private $base;

	/**
	 * User's meta data.
	 *
	 * @var array $meta The user's meta data.
	 * @since 0.1.0
	 */
	private $meta;

	/**
	 * User's taxonomy data.
	 *
	 * @var array $taxonomies The user's taxonomy data.
	 * @since 0.1.0
	 */
	private $taxonomies;

	/**
	 * User's credits.
	 *
	 * @var Get_To_Work_Credit[] $credits The user's credits.
	 * @since 0.1.0
	 */
	private $credits;

	/**
	 * The user's fields updatable by wp_update_user() keyed by frontend key.
	 *
	 * @var array
	 * @since 0.1.0
	 */
	const BASE_INPUT_FIELDS = [
		'id'        => 'ID',
		'firstName' => 'first_name',
		'lastName'  => 'last_name',
		'email'     => 'user_email',
	];

	/**
	 * The user's fields updatable by pods->save() keyed by frontend key.
	 *
	 * @var array
	 * @since 0.1.0
	 */
	const META_INPUT_FIELDS = [
		'pronouns'    => 'pronouns',
		'selfTitle'   => 'self_title',
		'homebase'    => 'homebase',
		'image'       => 'image',
		'phone'       => 'phone',
		'description' => 'description',
		'willTravel'  => 'will_travel',
		'willTour'    => 'will_tour',
		'resume'      => 'resume',
		'education'   => 'education',
		'media'       => 'media',
	];

	/**
	 * The user's social fields updatable by pods->save() keyed by frontend key.
	 *
	 * @var array
	 * @since 0.2.0
	 */
	const SOCIAL_INPUT_FIELDS = [
		'linkedin'  => 'linkedin',
		'instagram' => 'instagram',
		'twitter'   => 'twitter',
		'facebook'  => 'facebook',
		'website'   => 'website_url',
	];

	/**
	 * The user taxonomy slugs keyed by frontend key.
	 *
	 * @var array
	 * @since 0.1.0
	 */
	const TAXONOMY_INPUT_FIELDS = [
		'locations'          => 'location',
		'unions'             => 'union',
		'experienceLevels'   => 'experience_level',
		'genderIdentities'   => 'gender_identity',
		'racialIdentities'   => 'racial_identity',
		'personalIdentities' => 'personal_identity',
	];

	/**
	 * The constructor.
	 *
	 * @since  0.1.0
	 *
	 * @param  array  $user_data The user's data.
	 * @return void
	 */
	public function __construct( $user_data ) {
		$this->raw = $user_data;

		// Use the respective collections of keys to create the base and meta data arrays.
		$this->set_id();
		$this->set_base_data();
		$this->set_meta_data();
		$this->set_social_data();
		$this->set_taxonomy_data();
		$this->set_credits();
	}

	/**
	 * Set the user's ID.
	 *
	 * @return void
	 */
	private function set_id() {
		$this->id = $this->raw['id'];
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function get_base() {
		return $this->base;
	}

	public function get_meta() {
		return $this->meta;
	}

	/**
	 * Set the user's base data.
	 *
	 * @return void
	 */
	private function set_base_data() {
		foreach ( self::BASE_INPUT_FIELDS as $input_key => $save_key ) {
			if ( isset( $this->raw[$input_key] ) ) {
				$this->base[$save_key] = $this->raw[$input_key];
			}
		}
	}

	/**
	 * Set the user's meta data.
	 *
	 * @return void
	 */
	private function set_meta_data() {
		foreach ( self::META_INPUT_FIELDS as $input_key => $save_key ) {
			if ( isset( $this->raw[$input_key] ) ) {
				$this->meta[$save_key] = $this->raw[$input_key];
			}
		}
	}

	/**
	 * Set the user's social meta data.
	 *
	 * @return void
	 */
	private function set_social_data() {
		foreach ( self::SOCIAL_INPUT_FIELDS as $input_key => $save_key ) {
			if ( isset( $this->raw['socials'][$input_key] ) ) {
				$this->meta[$save_key] = $this->raw['socials'][$input_key];
			}
		}
	}

	/**
	 * Set the user's taxonomy data.
	 *
	 * @return void
	 */
	private function set_taxonomy_data() {
		foreach ( self::TAXONOMY_INPUT_FIELDS as $input_key => $tax_slug ) {
			if ( isset( $this->raw[$input_key] ) ) {
				if ( empty( $this->raw[$input_key] ) ) {
					$this->taxonomies[$tax_slug] = [];
					continue;
				}

				foreach ( $this->raw[$input_key] as $term_id ) {
					$this->taxonomies[$tax_slug][] = $term_id;
				}
			}
		}
	}

	/**
	 * Set the user's credits.
	 *
	 * @return void
	 */
	private function set_credits() {
		if ( isset( $this->raw['credits'] ) ) {
			$this->credits = $this->raw['credits'];
		}
	}

	/**
	 * Update the user's profile data.
	 *
	 * @return int|WP_Error The user ID on success. WP_Error on failure.
	 */
	public function update_user_profile() {
		$base = $this->update_base();
		$meta = $this->update_meta();
		$tax  = $this->update_taxonomies();

		if ( is_wp_error( $base ) ) {
			return $base->get_error_message();
		} elseif ( ! $meta ) {
			return new \WP_Error( 'update_user_profile', 'There was an error updating the user profile.' );
		} elseif ( is_wp_error( $tax ) ) {
			return $tax->get_error_message();
		}

		return $base;
	}

	/**
	 * Update the user's base data.
	 *
	 * @return int|WP_Error The return value of wp_update_user().
	 */
	protected function update_base() {
		return wp_update_user( $this->base );
	}

	/**
	 * Update the user's meta data.
	 *
	 * @return int The post ID return value of pods->save().
	 */
	protected function update_meta() {
		// Get the user's pod.
		$pod = pods( 'user', $this->id );

		// Update the user's pod.
		$update_fields = [];

		foreach ( $this->meta as $key => $value ) {
			$update_fields[$key] = $value;
		}

		// TODO investigate error handling (does $pod->save() return 0 on failure?)

		return $pod->save( $update_fields );
	}

	/**
	 * Update the user's taxonomy data.
	 *
	 * @return boolean|WP_Error True on success. WP_Error on failure.
	 */
	protected function update_taxonomies() {
		if ( ! $this->taxonomies ) {
			return false;
		}

		foreach ( $this->taxonomies as $tax_slug => $terms ) {
			$result = wp_set_object_terms( $this->id, array_map( 'intval', $terms ), $tax_slug );

			if ( is_wp_error( $result ) ) {
				return $result->get_error_message();
			}
		}

		return true;
	}

	protected function update_credits() {
		if ( ! $this->credits ) {
			return new \WP_Error( 'update_credits', 'There are no credits to update.' );
		}

		foreach ( $this->credits as $credit ) {
			$credit = new Get_To_Work_Credit( $credit );
			$credit->update_credit();
		}
	}
}
