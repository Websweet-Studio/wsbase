<?php
/**
 * Custom hooks
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


$navbar_type       = get_theme_mod( 'wsbase_navbar_type', 'offcanvas' );
if($navbar_type == 'offcanvas') {
	add_action( 'wsbase_navbar', 'wsbase_navbar_offcanvas' );
} else {
	add_action( 'wsbase_navbar', 'wsbase_navbar_collapse' );
}

add_action( 'wsbase_header', 'wsbase_add_navbar' );	// Add navbar.

if (!function_exists('wsbase_add_wp_store_cart_to_navbar')) {
	function wsbase_add_wp_store_cart_to_navbar()
	{
		if (!function_exists('do_shortcode') || !function_exists('shortcode_exists')) {
			return;
		}
		if (!shortcode_exists('wp_store_cart')) {
			return;
		}
		echo do_shortcode('[wp_store_cart]');
	}
}

add_action('wsbase_navbar_cart', 'wsbase_add_wp_store_cart_to_navbar');

add_action( 'wsbase_footer', 'wsbase_add_footer' );	// Add footer.

add_action( 'wsbase_site_info', 'wsbase_add_site_info' );

