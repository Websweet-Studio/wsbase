<?php
/**
 * Right sidebar check
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

</div><!-- #closing the primary container from /global-templates/left-sidebar-check.php -->

<?php
$sidebar_pos = get_theme_mod( 'wsbase_sidebar_position' );
// disable sidebar in woocommerce pages
if ( ('right' === $sidebar_pos || 'both' === $sidebar_pos) && !(class_exists( 'WooCommerce' ) && (is_woocommerce() || is_cart() || is_checkout() || is_account_page())) ) {
	get_template_part( 'sidebar-templates/sidebar', 'right' );
}
