<?php
/**
 * The UserProfile class.
 *
 * @package    Rise
 * @subpackage Rise/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */

class Rise_UserProfile {
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
	 * @var Rise_Credit[] $credits The user's credits.
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
		'slug'      => 'user_nicename',
	];

	/**
	 * The user's fields updatable by pods->save() keyed by frontend key.
	 *
	 * @var array
	 * @since 0.1.0
	 */
	const META_INPUT_FIELDS = [
		'email'       => 'contact_email',
		'pronouns'    => 'pronouns',
		'selfTitle'   => 'self_title',
		'homebase'    => 'homebase',
		'image'       => 'image',
		'website'     => 'website_url',
		'phone'       => 'phone',
		'description' => 'description',
		'willTravel'  => 'will_travel',
		'willTour'    => 'will_tour',
		'resume'      => 'resume',
		'education'   => 'education',
		'mediaVideo1' => 'media_video_1',
		'mediaVideo2' => 'media_video_2',
		'mediaImage1' => 'media_image_1',
		'mediaImage2' => 'media_image_2',
		'mediaImage3' => 'media_image_3',
		'mediaImage4' => 'media_image_4',
		'mediaImage5' => 'media_image_5',
		'mediaImage6' => 'media_image_6',
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
		'partnerDirectories' => 'partner_directory',
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
	 * Get an input key from its associated frontend key.
	 *
	 * @param  string $frontend_key
	 * @return string The input key for use in wp_update_user() or pods->save().
	 */
	protected static function get_input_key( $frontend_key ) {
		if ( array_key_exists( $frontend_key, self::BASE_INPUT_FIELDS ) ) {
			return self::BASE_INPUT_FIELDS[$frontend_key];
		} elseif ( array_key_exists( $frontend_key, self::META_INPUT_FIELDS ) ) {
			return self::META_INPUT_FIELDS[$frontend_key];
		} elseif ( array_key_exists( $frontend_key, self::SOCIAL_INPUT_FIELDS ) ) {
			return self::SOCIAL_INPUT_FIELDS[$frontend_key];
		} elseif ( array_key_exists( $frontend_key, self::TAXONOMY_INPUT_FIELDS ) ) {
			return self::TAXONOMY_INPUT_FIELDS[$frontend_key];
		}

		return false;
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
		} elseif ( !$meta ) {
			return new \WP_Error( 'update_user_profile', 'There was an error updating the user profile.' );
		} elseif ( is_wp_error( $tax ) ) {
			return $tax->get_error_message();
		}

		return $base;
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
	 * @return int|false|null The ID of the conflict range on success, false on failure, or null if there was an issue with the Pod itself.
	 */
	protected function update_meta() {
		// Get the user's pod.
		$pod = pods( 'user', $this->id );

		// Update the user's pod.
		$update_fields = [];

		foreach ( $this->meta as $key => $value ) {
			$update_fields[$key] = $value;
		}

		return $pod->save( $update_fields );
	}

	/**
	 * Update the user's taxonomy data.
	 *
	 * @return boolean|WP_Error True on success. WP_Error on failure.
	 */
	protected function update_taxonomies() {
		if ( !$this->taxonomies ) {
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

	/**
	 * Update the user's credits.
	 *
	 * @return void
	 */
	protected function update_credits() {
		if ( !$this->credits ) {
			return new \WP_Error( 'update_credits', 'There are no credits to update.' );
		}

		foreach ( $this->credits as $credit ) {
			$credit = new Rise_Credit( $credit );
			$credit->update_credit();
		}
	}

	/**
	 * Sanitize an input value.
	 *
	 * @since 1.0.4
	 *
	 * @param  string|integer|array $value The value to sanitize.
	 * @return string|integer|array The sanitized value.
	 */
	public static function sanitize_input_value( $value ) {
		// Sanitize the value, which could be a string, a number, or an array of both.
		$type = gettype( $value );

		if ( 'string' === $type ) {
			$sanitized = sanitize_text_field( $value );
		} elseif ( 'integer' === $type ) {
			$sanitized = absint( $value );
		} elseif ( 'array' === $type ) {
			$sanitized = array_map( 'sanitize_text_field', $value );
		}

		return $sanitized;
	}

	/**
	 * Get the input key for a profile field.
	 *
	 * @since 1.0.4
	 *
	 * @param  string               $name
	 * @param  string|integer|array $value
	 * @return void
	 */
	public function set_profile_field( $name, $value ) {
		$pod = pods( 'user', $this->id );

		// Get the field's save key.
		$field = self::get_input_key( $name );

		// Sanitize
		$value = self::sanitize_input_value( $value );

		return $pod->save( $field, $value );
	}

	/**
	 * Clear a meta input field in the user's profile.
	 *
	 * @since 1.0.3
	 *
	 * @param  string         $name The field name.
	 * @return int|false|null The ID of the conflict range on success, false on failure, or null if there was an issue with the Pod itself.
	 */
	public function clear_profile_field( $name ) {
		$pod = pods( 'user', $this->id );

		// Get the field's save key.
		$field = self::get_input_key( $name );

		return $pod->save( $field, '' );
	}

}
