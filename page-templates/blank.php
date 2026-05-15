<?php
/**
 * Template Name: Blank Page Template
 *
 * Template for displaying a blank page.
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php wsbase_color_scheme(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> <?php wsbase_body_attributes(); ?>>
	<?php do_action( 'wp_body_open' ); ?>
	<?php
	while ( have_posts() ) {
		the_post();
		get_template_part( 'loop-templates/content', 'blank' );
	}
	?>
	<?php wp_footer(); ?>
</body>
</html>
