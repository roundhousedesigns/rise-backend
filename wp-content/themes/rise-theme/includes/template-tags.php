<?php
/**
 * Template tags.
 *
 * @package rise
 */

/**
 * Get the background color for a term.
 */
function rise_term_background_color_class( $term_id = null ) {
	if ( !$term_id ) {
		return;
	}

	$pod        = pods( 'network_partner_tag', $term_id ? $term_id : $term_id );
	$term_color = $pod->field( 'background_color' ) ? $pod->field( 'background_color' ) : 'dark';

	echo esc_attr( 'bg-color-' . $term_color );
}

/**
 * Get the background color for a page.
 */
function rise_page_background_color_chevron_class( $page_id = null ) {
	if ( !$page_id ) {
		return;
	}

	$pod        = pods( 'page', $page_id );
	$page_color = $pod->field( 'background_color' ) ? $pod->field( 'background_color' ) : 'bg-color-dark';

	echo esc_attr( 'bg-color-' . $page_color );
}
