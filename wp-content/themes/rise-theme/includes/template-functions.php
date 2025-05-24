<?php
/**
 * Actions and filters.
 *
 * @package rise
 */

/**
 * Remove 'Network Partner Tag:' prefix from archive titles
 */
function rise_remove_archive_title_prefix( $title ) {
	if ( is_tax( 'network_partner_tag' ) ) {
		$title = single_term_title( '', false );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'rise_remove_archive_title_prefix' );
