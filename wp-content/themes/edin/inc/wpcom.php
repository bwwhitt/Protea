<?php
/**
 * WordPress.com-specific functions and definitions.
 *
 * This file is centrally included from `wp-content/mu-plugins/wpcom-theme-compat.php`.
 *
 * @package Edin
 */

/**
 * Adds support for wp.com-specific theme functions.
 *
 * @global array $themecolors
 */
function edin_wpcom_setup() {
	global $themecolors;

	// Set theme colors for third party services.
	if ( ! isset( $themecolors ) ) {
		$themecolors = array(
			'bg'     => 'ffffff',
			'border' => 'c5c5c5',
			'text'   => '303030',
			'link'   => '1279be',
			'url'    => '1279be',
		);
	}

	// Add print stylesheet.
	add_theme_support( 'print-style' );
}
add_action( 'after_setup_theme', 'edin_wpcom_setup' );

/**
 * Enqueue wp.com-specific styles
 */
function edin_wpcom_styles() {
	wp_enqueue_style( 'edin-wpcom', get_template_directory_uri() . '/inc/style-wpcom.css', '20140723' );
}
add_action( 'wp_enqueue_scripts', 'edin_wpcom_styles' );

/**
 * De-queue Google fonts if custom fonts are being used instead.
 */
function edin_dequeue_fonts() {
	if ( class_exists( 'TypekitData' ) && class_exists( 'CustomDesign' ) && CustomDesign::is_upgrade_active() ) {
		$customfonts = TypekitData::get( 'families' );

		if ( $customfonts['headings']['id'] && $customfonts['body-text']['id'] ) {
			wp_dequeue_style( 'edin-pt-sans' );
			wp_dequeue_style( 'edin-pt-serif' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'edin_dequeue_fonts' );

/**
 * Remove ratings from excerpt.
 */
remove_filter( 'the_excerpt', 'polldaddy_show_rating' );

/**
 * Track Edin's menu usage in WP.com
 */
if ( file_exists( WP_CONTENT_DIR . '/blog-plugins/edin-menu-usage.php' ) ) {
	require_once WP_CONTENT_DIR . '/blog-plugins/edin-menu-usage.php';
}