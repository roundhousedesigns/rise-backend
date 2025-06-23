<?php

namespace RHD\Rise\Core;

/**
 * Initialize the plugin and run sanity checks.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Core
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */
class Init {
	/**
	 * Allow additional redirect hosts.
	 *
	 * @since  1.0.4
	 *
	 * @param  string[] $hosts
	 * @return string[] The allowed hosts.
	 */
	public function allowed_redirect_hosts( $hosts ) {
		$allowed = [
			'work.risetheatre.org',
			'risetheatre.org',
			'dev.risedirectory.pages.dev',
		];

		return \array_merge( $hosts, $allowed );
	}
}
