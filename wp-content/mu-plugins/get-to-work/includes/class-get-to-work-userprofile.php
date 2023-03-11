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
	 * @var int $user_id The user ID.
	 * @since 0.1.0
	 */
	protected $user_id;

	/**
	 * The user's initial raw data.
	 *
	 * @var array $raw The user's initial raw data.
	 */
	private $_raw;

	/**
	 * The user's WP_User object.
	 */
	protected $init;

	/**
	 * User's basic data.
	 *
	 * @var array $basic The user's basic data.
	 * @since 0.1.0
	 */
	private $basic;

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
	 * @var array $taxonomies The user's taxonomy data. // TODO Document this better.
	 * @since 0.1.0
	 */
	private $taxonomies;

	/**
	 * A user's credits.
	 *
	 * @var array $credits The user's credit IDs.
	 * @since 0.1.0
	 */
	private $credits;

	/**
	 * The user's fields updatable by wp_update_user() keyed by frontend key.
	 *
	 * @var array
	 * @since 0.1.0
	 */
	const BASIC_FIELD_PAIRS = [
		'id'        => 'ID',
		'firstName' => 'first_name',
		'lastName'  => 'last_name',
		'website'   => 'user_url',
	];

	/**
	 * The user's fields updatable by pods->save() keyed by frontend key.
	 *
	 * @var array
	 * @since 0.1.0
	 */
	const META_FIELD_PAIRS = [
		'pronouns'    => 'pronouns',
		'email'       => 'contactEmail',
		'selfTitle'   => 'self_title',
		'image'       => 'image',
		'phone'       => 'phone',
		'description' => 'description',
		'location'    => 'location',
		'resume'      => 'resume',
		'education'   => 'education',
		'media'       => 'media',
		'linkedin'    => 'linkedin',
		'instagram'   => 'instagram',
		'twitter'     => 'twitter',
		'facebook'    => 'facebook',
	];

	/**
	 * The user taxonomy slugs keyed by frontend key.
	 *
	 * @var array
	 * @since 0.1.0
	 */
	const USER_TAXONOMY_FIELDS = [
		'unions'             => 'union',
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
		$this->_raw = $user_data;

		// MAYBE There could be a better way to do this than to pass the entire user data array to each method.

		// Use the respective collections of keys to create the basic and meta data arrays.
		$this->set_id();
		$this->set_basic_data();
		$this->set_meta_data();
		$this->set_taxonomy_data();
		// $this->destroy_raw();
	}

	/**
	 * Unset the raw data.
	 *
	 * @return void
	 */
	private function destroy_raw() {
		unset( $this->_raw );
	}

	private function set_id() {
		$this->user_id = $this->_raw['id'];
	}

	/**
	 * Get the user's ID.
	 *
	 * @return void
	 */
	public function get_id() {
		return $this->user_id;
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function get_basic() {
		return $this->basic;
	}

	public function get_meta() {
		return $this->meta;
	}

	/**
	 * Set the user's basic data.
	 *
	 * @return void
	 */
	private function set_basic_data() {
		foreach ( self::BASIC_FIELD_PAIRS as $input_key => $save_key ) {
			if ( isset( $this->_raw[$input_key] ) ) {
				$this->basic[$save_key] = $this->_raw[$input_key];
			}
		}
	}

	/**
	 * Set the user's meta data.
	 *
	 * @return void
	 */
	private function set_meta_data() {
		foreach ( self::META_FIELD_PAIRS as $input_key => $save_key ) {
			if ( isset( $this->_raw[$input_key] ) ) {
				$this->meta[$save_key] = $this->_raw[$input_key];
			}
		}
	}

	/**
	 * Set the user's taxonomy data.
	 *
	 * @return void
	 */
	private function set_taxonomy_data() {
		foreach ( self::USER_TAXONOMY_FIELDS as $input_key => $tax_slug ) {
			if ( isset( $this->_raw[$input_key] ) ) {
				foreach ( $this->_raw[$input_key] as $term_id ) {
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
		$basic = $this->update_basic();
		$meta  = $this->update_meta();
		$tax   = $this->update_taxonomies();

		if ( is_wp_error( $basic ) ) {
			return $basic->get_error_message();
		} elseif ( ! $meta ) {
			return new \WP_Error( 'update_user_profile', 'There was an error updating the user profile.' );
		} elseif ( is_wp_error( $tax ) ) {
			return $tax->get_error_message();
		} else {
			return $basic;
		}
	}

	/**
	 * Update the user's basic data.
	 *
	 * @return int|WP_Error The return value of wp_update_user().
	 */
	protected function update_basic() {
		return wp_update_user( $this->basic );
	}

	/**
	 * Update the user's meta data.
	 *
	 * @return int The post ID return value of pods->save().
	 */
	protected function update_meta() {
		// Get the user's pod.
		$pod = pods( 'user', $this->user_id );

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
		if ( ! $this->taxonomies ) {
			return new \WP_Error( 'update_taxonomies', 'There are no taxonomies to update.' );
		}

		foreach ( $this->taxonomies as $tax_slug => $terms ) {
			$result = wp_set_object_terms( $this->user_id, array_map( 'absint', $terms ), $tax_slug );

			if ( is_wp_error( $result ) ) {
				return $result->get_error_message();
			}
		}

		return true;
	}
}
