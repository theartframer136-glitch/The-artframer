<?php
/**
 * Postero Child Theme — functions and definitions.
 *
 * Custom functionality, scripts, and styles for The Art Framer site.
 *
 * @package PosteroChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Enqueue parent and child theme styles.
 *
 * Parent CSS loads first, then child CSS overrides. Versioned via the theme
 * header's `Version:` field so a bump there cache-busts CSS at CDN + browser.
 */
function postero_child_enqueue_styles() {
	$parent_handle = 'postero-parent-style';
	$child_theme   = wp_get_theme();
	$parent_theme  = wp_get_theme( 'postero' );

	wp_enqueue_style(
		$parent_handle,
		get_template_directory_uri() . '/style.css',
		array(),
		$parent_theme->exists() ? $parent_theme->get( 'Version' ) : false
	);

	wp_enqueue_style(
		'postero-child-style',
		get_stylesheet_uri(),
		array( $parent_handle ),
		$child_theme->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'postero_child_enqueue_styles', 20 );

/**
 * Optionally load custom.css if the file exists.
 *
 * Lets us drop in extra CSS without touching the main style.css. Uses
 * filemtime() so any edit cache-busts immediately.
 */
function postero_child_custom_assets() {
	$custom_css_path = get_stylesheet_directory() . '/assets/css/custom.css';
	if ( file_exists( $custom_css_path ) ) {
		wp_enqueue_style(
			'postero-child-custom',
			get_stylesheet_directory_uri() . '/assets/css/custom.css',
			array( 'postero-child-style' ),
			filemtime( $custom_css_path )
		);
	}

	$custom_js_path = get_stylesheet_directory() . '/assets/js/custom.js';
	if ( file_exists( $custom_js_path ) ) {
		wp_enqueue_script(
			'postero-child-custom-js',
			get_stylesheet_directory_uri() . '/assets/js/custom.js',
			array( 'jquery' ),
			filemtime( $custom_js_path ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'postero_child_custom_assets', 25 );
