<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Edin
 */

function edin_jetpack_setup() {
	// Add theme support for Infinite Scroll.
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'main',
		'footer_widgets' => array(
			'sidebar-2',
			'sidebar-3',
			'sidebar-4',
		),
		'footer'         => 'page',
		'render'    	 => 'edin_infinite_scroll_render',
	) );

	// Add theme support for Responsive Videos.
	add_theme_support( 'jetpack-responsive-videos' );

	// Add theme support for Testimonial CPT.
	add_theme_support( 'jetpack-testimonial' );

	// Add theme support for Logo upload.
	add_image_size( 'edin-logo', 583, 192 );
	add_theme_support( 'site-logo', array( 'size' => 'edin-logo' ) );

	// Add theme support for Content Options.
	add_theme_support( 'jetpack-content-options', array(
		'blog-display'    => 'content',
		'post-details'    => array(
			'stylesheet' => 'edin-style',
			'date'       => '.posted-on',
			'categories' => '.cat-links',
			'tags'       => '.tags-links',
			'author'     => '.byline',
		),
		'featured-images' => array(
			'archive'          => true,
			'post'             => true,
			'page'             => true,
			'fallback'         => true,
			'fallback-default' => false,
		),
	) );
}
add_action( 'after_setup_theme', 'edin_jetpack_setup' );

/**
 * Define the code that is used to render the posts added by Infinite Scroll.
 *
 * Includes the whole loop. Used to include the correct template part for the Testimonial CPT.
 */
function edin_infinite_scroll_render() {
	while( have_posts() ) {
		the_post();
		if ( is_post_type_archive( 'jetpack-testimonial' ) ) {
			get_template_part( 'content', 'testimonial' );
		} else {
			get_template_part( 'content', get_post_format() );
		}
	}
}

/**
 * Flush the Rewrite Rules for the Testimonial CPT after the user has activated the theme.
 */
function edin_flush_rewrite_rules() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'edin_flush_rewrite_rules' );

/**
 * Return early if Site Logo is not available.
 */
function edin_the_site_logo() {
	if ( ! function_exists( 'jetpack_the_site_logo' ) ) {
		return;
	} else {
		jetpack_the_site_logo();
	}
}

/**
 * Remove sharedaddy from excerpt.
 */
function edin_remove_sharedaddy() {
    remove_filter( 'the_excerpt', 'sharing_display', 19 );
}
add_action( 'loop_start', 'edin_remove_sharedaddy' );

/**
 * Custom function to check for a post thumbnail;
 * If Jetpack is not available, fall back to has_post_thumbnail()
 */
function edin_has_post_thumbnail( $post = null ) {
	if ( function_exists( 'jetpack_has_featured_image' ) ) {
		return jetpack_has_featured_image( $post );
	} else {
		return has_post_thumbnail( $post );
	}
}

/**
 * Custom function to get the URL of a post thumbnail;
 * If Jetpack is not available, fall back to wp_get_attachment_image_src()
 */
function edin_get_attachment_image_src( $post_id, $post_thumbnail_id, $size ) {
	if ( function_exists( 'jetpack_featured_images_fallback_get_image_src' ) ) {
		return jetpack_featured_images_fallback_get_image_src( $post_id, $post_thumbnail_id, $size );
	} else {
		$attachment = wp_get_attachment_image_src( $post_thumbnail_id, $size ); // Attachment array
		$url = $attachment[0]; // Attachment URL
		return $url;
	}
}

/**
 * Remove Infinite Scroll from Testimonials archive page.
 */
function edin_infinite_scroll_archive_supported() {
    $supported =
    	current_theme_supports( 'infinite-scroll' )
    	&& (
    		is_home()
    		|| ( is_archive() && ! is_post_type_archive( 'jetpack-testimonial' ) )
    		|| is_search()
		);

    return $supported;
}
add_filter( 'infinite_scroll_archive_supported', 'edin_infinite_scroll_archive_supported' );
