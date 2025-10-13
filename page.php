<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'wsbase_container_type' );

?>

<div class="wrapper" id="page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Check for static pages that might need sidebar -->
			<?php
			// Check if this page should have sidebar based on customizer setting
			$sidebar_pos = get_theme_mod( 'wsbase_sidebar_position' );
			$has_sidebar = in_array($sidebar_pos, ['left', 'both']) && !is_front_page();
			?>

			<?php if ($has_sidebar) : ?>
				<!-- Do the left sidebar check -->
				<?php get_template_part('global-templates/left-sidebar-check'); ?>
			<?php else : ?>
				<!-- Full width for pages without sidebar -->
				<div class="col-lg-10 col-xl-8 mx-auto">
			<?php endif; ?>

			<main class="site-main" id="main">

				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'loop-templates/content', 'page' );

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				}
				?>

			</main><!-- #main -->

			<?php if ($has_sidebar) : ?>
				<!-- Do the right sidebar check -->
				<?php get_template_part('global-templates/right-sidebar-check'); ?>
			<?php else : ?>
				<!-- Close full width page layout -->
				</div><!-- .col-lg-10 col-xl-8 -->
			<?php endif; ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #page-wrapper -->

<?php
get_footer();
