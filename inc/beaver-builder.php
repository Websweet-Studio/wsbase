<?php
/**
 * Ws Beaver Builder functions.
 * 
 * @package Wsbase
 */

 // Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


function wsbase_header_footer_render() {

	if ( ! class_exists( 'FLThemeBuilderLayoutData' ) ) {
		return;
	}

	// Get the header ID.
	$header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();

	// If we have a header, remove the theme header and hook in Theme Builder's.
	if ( ! empty( $header_ids ) ) {		 
		remove_all_actions( 'wsbase_header');		
		add_action( 'wsbase_header', 'FLThemeBuilderLayoutRenderer::render_header' );
	}

	// Get the footer ID.
	$footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();

	// If we have a footer, remove the theme footer and hook in Theme Builder's.
	if ( ! empty( $footer_ids ) ) {
		remove_all_actions( 'wsbase_footer');		
		add_action( 'wsbase_footer', 'FLThemeBuilderLayoutRenderer::render_footer' );
	}
}
add_action( 'wp', 'wsbase_header_footer_render' );


add_filter( 'fl_theme_builder_part_hooks', 'wsbase_register_part_hooks' );
function wsbase_register_part_hooks() {
  return array(
    array(
      'label' => 'Header',
      'hooks' => array(
        'wsbase_left_sidebar' => 'Left Sidebar',
        'wsbase_right_sidebar'  => 'Right Sidebar',
      )
    ),
  );
}