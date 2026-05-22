<?php

/**
 * Ws enqueue scripts
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

if (! function_exists('wsbase_scripts')) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function wsbase_scripts()
	{
		// Get the theme data.
		$the_theme         = wp_get_theme();
		$theme_version     = $the_theme->get('Version');
		$suffix            = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		// Grab asset urls.
		$theme_styles  = "/css/theme{$suffix}.css";
		$theme_scripts = "/js/theme{$suffix}.js";

		$css_version = $theme_version;
		$css_path    = get_template_directory() . $theme_styles;
		if ( file_exists( $css_path ) ) {
			$css_version .= '.' . filemtime( $css_path );
		}
		wp_enqueue_style('wsbase-styles', get_template_directory_uri() . $theme_styles, array(), $css_version);
		if ( function_exists( 'shortcode_exists' ) && shortcode_exists( 'wp_store_cart' ) ) {
			wp_add_inline_style(
				'wsbase-styles',
				'@media (max-width: 767.98px){#main-nav .container,#main-nav .container-fluid{display:flex;flex-wrap:wrap;align-items:center}#main-nav .navbar-toggler{order:0;margin-right:.5rem}#main-nav .navbar-brand{order:1}#main-nav .wsbase-navbar-cart{order:2;margin-left:auto}#main-nav .offcanvas,#main-nav .collapse.navbar-collapse{order:3;width:100%}}'
			);
		}

		wp_enqueue_script('jquery');

		$js_version = $theme_version;
		$js_path    = get_template_directory() . $theme_scripts;
		if ( file_exists( $js_path ) ) {
			$js_version .= '.' . filemtime( $js_path );
		}
		wp_enqueue_script('wsbase-scripts', get_template_directory_uri() . $theme_scripts, array(), $js_version, true);
		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}
} // End of if function_exists( 'wsbase_scripts' ).

add_action('wp_enqueue_scripts', 'wsbase_scripts');

if (! function_exists('wsbase_google_fonts')) {
	/**
	 * Load Google Fonts for Space Grotesk and Inter.
	 */
	function wsbase_google_fonts()
	{
		// Enqueue Space Grotesk font from Google Fonts (for headings & navigation)
		wp_enqueue_style(
			'wsbase-space-grotesk-font',
			'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap',
			array(),
			null
		);

		// Enqueue Inter font from Google Fonts (for body text)
		wp_enqueue_style(
			'wsbase-inter-font',
			'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
			array(),
			null
		);
	}
}

add_action('wp_enqueue_scripts', 'wsbase_google_fonts');
