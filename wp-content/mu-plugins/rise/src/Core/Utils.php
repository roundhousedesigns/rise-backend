<?php
/**
 * Utilities.
 *
 * @package    Rise
 * @subpackage Rise/Core
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.0.4
 */

namespace RHD\Rise\Core;

/**
 * Utility functions for the Rise plugin.
 */
class Utils {
	/**
	 * Strip EXIF data from an image file if it's a jpg.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file The path to the image file.
	 * @return string The path to the new image file without EXIF data.
	 */
	public static function maybe_strip_exif( $file ) {
		// Get the image extension.
		$extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

		if ( 'jpg' !== $extension && 'jpeg' !== $extension ) {
			return $file;
		}

		// Create a new image resource.
		$image = imagecreatefromjpeg( $file );

		// Remove the EXIF data.
		imagejpeg( $image, $file, 91 );

		// Free the image resource.
		imagedestroy( $image );

		// Return the path to the new image file.
		return $file;
	}

	/**
	 * Converts a string from camelCase to underscore_notation.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string
	 * @return string The converted string.
	 */
	public static function camel_to_snake( $string ) {
		$string = preg_replace( '/(?!^)[[:upper:]]/', '_$0', $string );
		$string = preg_replace( '/([a-zA-Z])([0-9])/', '$1_$2', $string );
		$string = strtolower( $string );
		return $string;
	}

	/**
	 * Converts a string from underscore_notation to camelCase.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string
	 * @return string The converted string.
	 */
	public static function snake_to_camel( $string ) {
		$string = strtolower( $string );
		$string = preg_replace_callback( '/_([a-z0-9])/', function ( $matches ) {
			return strtoupper( $matches[1] );
		}, $string );
		$string = lcfirst( $string );
		return $string;
	}

	/**
	 * Checks whether the given reCAPTCHA response is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $response The reCAPTCHA response.
	 * @return boolean Whether the response is valid.
	 */
	public static function recaptcha_is_valid( $response ) {
		if ( !defined( 'RECAPTCHA_SECRET_KEY' ) ) {
			return false;
		}

		$url  = 'https://www.google.com/recaptcha/api/siteverify';
		$data = [
			'secret'   => RECAPTCHA_SECRET_KEY,
			'response' => $response,
		];

		$options = [
			'http' => [
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query( $data ),
			],
		];

		$context = stream_context_create( $options );
		// TODO use wp_remote_get() instead of file_get_contents()
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$result = file_get_contents( $url, false, $context );

		return json_decode( $result )->success;
	}

	/**
	 * Flatten a multidimensional array.
	 *
	 * @since 1.0.8
	 *
	 * @param  array $array The array to flatten.
	 * @return array The flattened array.
	 */
	public static function flatten_array( $array ) {
		$result = [];

		foreach ( $array as $element ) {
			if ( is_array( $element ) ) {
				$result = array_merge( $result, self::flatten_array( $element ) );
				continue;
			}

			$result[] = $element;
		}

		return $result;
	}

	/**
	 * Toggles the presence of a given ID in an array.
	 *
	 * If the ID is already in the array, it is removed. If it is not in the array, it is added.
	 *
	 * @param  int[] $array     The array of IDs to toggle.
	 * @param  int   $toggledId The ID to toggle.
	 * @return int[] The updated array of IDs.
	 */
	public static function toggle_id_in_array( $array, $toggledId ) {
		if ( in_array( $toggledId, $array ) ) {
			$key = array_search( $toggledId, $array );
			unset( $array[$key] );
		} else {
			$array[] = $toggledId;
		}

		return $array;
	}

	/**
	 * Gets a newly uploaded file's attachment ID.
	 *
	 * @param  string $url The URL of the file.
	 * @return int    The attachment ID.
	 */
	public static function get_attachment_id_by_url( $url ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$file             = [];
		$file['name']     = basename( $url );
		$file['tmp_name'] = download_url( $url, 300, true );
		$file['error']    = '';
		$file['size']     = filesize( $file['tmp_name'] );

		$attachment_id = media_handle_sideload( $file, 0 );

		if ( is_wp_error( $attachment_id ) ) {
			unlink( $file['tmp_name'] );
			throw new \WP_Error( 'attachment_processing_error', esc_html( $attachment_id->get_error_message() ) );
		}

		return $attachment_id;
	}
}