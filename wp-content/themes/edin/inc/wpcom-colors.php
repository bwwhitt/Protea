<?php

add_color_rule( 'bg', '#ffffff', array(

	// Background
	array(
		'body', 'background-color',
	),

),
__( 'Background' ) );

add_color_rule( 'link', '#1279be', array(

	// Background
	array(
		'mark,
		ins,
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		a.button,
		a.button:visited,
		a.button-minimal:hover,
		a.button-minimal:focus,
		a.button-minimal:active,
		body:not(.small-screen) .menu-toggle.open:before,
		body:not(.small-screen) .search-toggle.open:before,
		.navigation-wrapper,
		.search-wrapper,
		.widget_nav_menu .dropdown-toggle:hover,
		.hero.with-featured-image,
		body[class*="front-page"] .hero,
		#infinite-handle span,
		.large-screen.navigation-classic .primary-navigation .menu-primary > ul > li:hover,
		.large-screen.navigation-classic .primary-navigation .menu-primary > ul > li.current-menu-item > a,
		.large-screen.navigation-classic .primary-navigation .menu-primary > ul > li.current_page_item > a,
		.large-screen.navigation-classic .primary-navigation .menu-primary > ul > li > a:hover,
		.large-screen.navigation-classic .primary-navigation .menu-primary > ul > li > a:focus,
		.large-screen.navigation-classic .primary-navigation .menu-primary > ul > li > a:active,
		.large-screen.navigation-classic .primary-navigation ul ul li,
		.widget_akismet_widget .a-stats a', 'background-color', '#fff', 1.5
	),

	// Color
	array(
		'a,
		a.button-minimal,
		a.button-minimal:visited,
		.menu-toggle:focus,
		.search-toggle:focus,
		.primary-navigation .dropdown-toggle:hover,
		.footer-navigation a:hover,
		.footer-navigation a:focus,
		.footer-navigation a:active,
		.screen-reader-text:hover,
		.screen-reader-text:focus,
		.screen-reader-text:active,
		.site-footer a:hover,
		.site-footer a:focus,
		.site-footer a:active,
		.format-link .entry-title a:hover:after,
		.format-link .entry-title a:focus:after,
		.format-link .entry-title a:active:after,
		.entry-title a:hover,
		.entry-title a:focus,
		.entry-title a:active,
		.featured-page .entry-title a:hover,
		.featured-page .entry-title a:focus,
		.featured-page .entry-title a:active,
		.grid .entry-title a:hover,
		.grid .entry-title a:focus,
		.grid .entry-title a:active,
		#infinite-footer .blog-credits a:hover,
		#infinite-footer .blog-credits a:focus,
		#infinite-footer .blog-credits a:active,
		#infinite-footer .blog-info a:hover,
		#infinite-footer .blog-info a:focus,
		#infinite-footer .blog-info a:active,
		.small-screen .menu-toggle:hover:before,
		.small-screen .menu-toggle:active:before,
		.small-screen .menu-toggle.open:before,
		.small-screen .menu-toggle.open,
		.medium-screen .menu-toggle:hover,
		.medium-screen .menu-toggle:active,
		.small-screen .search-toggle:hover:before,
		.small-screen .search-toggle:active:before,
		.small-screen .search-toggle.open:before,
		.small-screen .search-toggle.open,
		.medium-screen .search-toggle:hover,
		.medium-screen .search-toggle:active,
		.large-screen.navigation-classic .primary-navigation .menu-item-has-children:before,
		.large-screen.navigation-classic .secondary-navigation a:hover,
		.large-screen.navigation-classic .secondary-navigation a:focus,
		.large-screen.navigation-classic .secondary-navigation a:active,
		.widget_goodreads div[class^="gr_custom_each_container"] a:hover,
		.widget_goodreads div[class^="gr_custom_each_container"] a:focus,
		.widget_goodreads div[class^="gr_custom_each_container"] a:active,
		.testimonial-entry-title a:hover,
		.testimonial-entry-title a:focus,
		.testimonial-entry-title a:active', 'color', '#fff', 1.5
	),

	// Border
	array(
		'button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		input[type="text"]:focus,
		input[type="email"]:focus,
		input[type="url"]:focus,
		input[type="password"]:focus,
		input[type="search"]:focus,
		textarea:focus,
		a.button,
		a.button-minimal,
		a.button:visited,
		a.button-minimal:visited,
		a.button-minimal:hover,
		a.button-minimal:focus,
		a.button-minimal:active,
		#infinite-handle span,
		.small-screen .menu-toggle:hover:before,
		.small-screen .menu-toggle:active:before,
		.small-screen .menu-toggle.open:before,
		.small-screen .search-toggle:hover:before,
		.small-screen .search-toggle:active:before,
		.small-screen .search-toggle.open:before,
		#comments #respond #comment-form-comment.active,
		#comments #respond .comment-form-fields div.comment-form-input.active,
		.widget_akismet_widget .a-stats a', 'border-color', '#fff', 1.5
	),
	array(
		'.search-wrapper .search-field', 'border-color', 'link', 2.4
	),

	// Border Right
	array(
		'.large-screen.navigation-classic .primary-navigation ul ul ul:before,
		.rtl blockquote,
		body.rtl .hentry .wpcom-reblog-snapshot .reblogger-note-content blockquote', 'border-right-color', '#fff', 1.5
	),

	// Border Bottom
	array(
		'.footer-navigation a:hover,
		.footer-navigation a:focus,
		.footer-navigation a:active,
		.large-screen.navigation-classic .primary-navigation ul ul:before,
		.large-screen.navigation-classic .primary-navigation ul ul li:last-of-type,
		.large-screen.navigation-classic .secondary-navigation a:hover,
		.large-screen.navigation-classic .secondary-navigation a:focus,
		.large-screen.navigation-classic .secondary-navigation a:active', 'border-bottom-color', '#fff', 1.5
	),

	// Border Left
	array(
		'blockquote,
		body .hentry .wpcom-reblog-snapshot .reblogger-note-content blockquote', 'border-left-color', '#fff', 1.5
	),

),
__( 'Accent' ) );

/* Additional CSS */

add_theme_support( 'custom_colors_extra_css', 'edin_extra_css' );
function edin_extra_css() { ?>
	.hero a.button-minimal {
		border-color: #fff !important;
	}
	#comments #respond .form-submit input#comment-submit,
	.widget_flickr #flickr_badge_uber_wrapper td a:last-child {
		background: #c5c5c5 !important;
		border-color: #c5c5c5 !important;
	}
	@media screen and (min-width: 1020px) {
		body.small-screen.navigation-classic .primary-navigation .menu-primary > ul > li.current-menu-item:before,
		body.small-screen.navigation-classic .primary-navigation .menu-primary > ul > li.current_page_item:before,
		body.small-screen.navigation-classic .primary-navigation .menu-item-has-children:hover:before,
		body.small-screen.navigation-classic .primary-navigation ul ul .menu-item-has-children:before {
			color: #fff;
		}
		body.large-screen.navigation-classic .primary-navigation ul ul ul:before {
			border-bottom-color: transparent;
		}
	}
	@media screen and (min-width: 1230px) {
		body.small-screen .menu-toggle.open,
		body.small-screen .menu-toggle.open:before,
		body.small-screen .menu-toggle.open:focus,
		body.small-screen .menu-toggle.open:focus:before {
			color: #fff;
		}
		body.small-screen .menu-toggle.open:before,
		body.small-screen .menu-toggle.open:focus:before {
			border-color: #fff;
		}
		body.small-screen .menu-toggle.open:before {
			background: transparent;
		}
		body.small-screen .menu-toggle.open:hover,
		body.small-screen .menu-toggle.open:active,
		body.small-screen .menu-toggle.open:hover:before,
		body.small-screen .menu-toggle.open:active:before {
			color: rgba(255, 255, 255, 0.5);
		}
		body.small-screen .menu-toggle.open:hover:before,
		body.small-screen .menu-toggle.open:active:before {
			border-color: rgba(255, 255, 255, 0.5);
		}
	}
<?php
}

/* Additional color palettes */

add_color_palette( array(
    '#a20d15',
    '',
    '#d8111b',
    '',
    '',
), __( 'Red' ) );

add_color_palette( array(
    '#5f8c30',
    '',
    '#7eb940',
    '',
    '',
), __( 'Green' ) );

add_color_palette( array(
    '#69337f',
    '',
    '#8e44ac',
    '',
    '',
), __( 'Purple' ) );

add_color_palette( array(
	'#3fb0aa',
	'',
	'#338884',
	'',
	'',
), __( 'Teal' ) );

add_color_palette( array(
	'#9e9e9e',
	'',
	'#686868',
	'',
	'',
), __( 'Grey') );

add_color_palette( array(
	'#6094b7',
	'',
	'#2d597a',
	'',
	'',
), __( 'Blue') );
