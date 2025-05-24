<?php
/**
 * Functions.
 *
 * @package rise
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Includes.
require_once get_template_directory() . '/includes/template-functions.php';
require_once get_template_directory() . '/includes/template-tags.php';

/**
 * Theme Setup
 */
function rise_theme_setup() {
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add theme support for selective refresh for widgets
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Register nav menus
	register_nav_menus(
		[
			'primary' => esc_html__( 'Primary Menu', 'rise' ),
		]
	);

	// Add support for core custom logo
	add_theme_support( 'custom-logo' );

	// Add support for HTML5
	add_theme_support(
		'html5',
		[
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		]
	);

	// Set content width based on theme's design
	if ( !isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 620; // Based on theme.json contentSize
	}
}
add_action( 'after_setup_theme', 'rise_theme_setup' );

/**
 * Add custom font family to editor
 */
function rise_theme_editor_styles() {
	// Add editor styles
	add_editor_style( 'editor-style.css' );

	// Add support for editor font sizes
	add_theme_support( 'editor-font-sizes', [] );

	// Add support for responsive embeds
	add_theme_support( 'responsive-embeds' );
}
add_action( 'after_setup_theme', 'rise_theme_editor_styles' );

/**
 * Enqueue scripts and styles
 */
function rise_theme_scripts() {
	wp_enqueue_style( 'rise-style', get_stylesheet_uri(), [], wp_get_theme()->get( 'Version' ) );

	// Enqueue frontend JS
	wp_enqueue_script( 'rise-frontend', get_template_directory_uri() . '/js/frontend.js', [], wp_get_theme()->get( 'Version' ), true );

	// Enqueue nav JS
	// wp_enqueue_script( 'rise-nav', get_template_directory_uri() . '/js/nav.js', [], wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'rise_theme_scripts' );

/**
 * Disable WordPress admin bar
 */
add_filter( 'show_admin_bar', '__return_false' );
