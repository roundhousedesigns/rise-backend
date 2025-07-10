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

/**
 * Add login/logout link to main navigation menu.
 *
 * @param  string $items   Menu items HTML
 * @param  object $args    Menu arguments
 * @return string Modified menu items HTML
 */
function rise_add_login_logout_link( $items, $args ) {
	if ( 'primary' !== $args->theme_location ) {
		return $items;
	}

	$link = '';
	if ( is_user_logged_in() ) {
		$link = sprintf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			wp_logout_url( home_url() ),
			esc_html__( 'Log Out', 'rise' )
		);
	} else {
		$login_url = defined( 'RISE_FRONTEND_URL' ) ? \RISE_FRONTEND_URL : wp_login_url();

		$link = sprintf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			$login_url,
			esc_html__( 'Log In', 'rise' )
		);
	}

	return $items . $link;
}
add_filter( 'wp_nav_menu_items', 'rise_add_login_logout_link', 10, 2 );
