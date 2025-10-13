<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('wsbase_container_type');
?>

<?php if (is_front_page() && is_home()) : ?>
	<?php get_template_part('global-templates/hero'); ?>
<?php endif; ?>

<div class="wrapper" id="index-wrapper">

	<div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Check for homepage (front page) -->
			<?php if (is_front_page()) : ?>
				<!-- Do the left sidebar check for homepage -->
				<?php get_template_part('global-templates/left-sidebar-check'); ?>
			<?php else : ?>
				<!-- Full width for blog pages -->
				<div class="col-lg-10 col-xl-8 mx-auto">
			<?php endif; ?>

			<main class="site-main" id="main">

				<?php
				if (have_posts()) {
				?>
					<?php if (is_home() && ! is_front_page()) : ?>
						<header class="page-header mb-4">
							<h1 class="page-title display-4 mb-3"><?php single_post_title(); ?></h1>
						</header>
					<?php endif; ?>

					<div class="blog-posts">
						<div class="row">
							<?php
							// Start the Loop.
							while (have_posts()) {
								the_post();

								/*
								 * Use grid template for blog layout similar to archive
								 */
								get_template_part('loop-templates/content-blog');
							}
							?>
						</div><!-- .row -->
					</div><!-- .blog-posts -->
				<?php
				} else {
					get_template_part('loop-templates/content', 'none');
				}
				?>

			</main><!-- #main -->

			<!-- The pagination component -->
			<?php wsbase_pagination(); ?>

			<?php if (is_front_page()) : ?>
				<!-- Do the right sidebar check for homepage -->
				<?php get_template_part('global-templates/right-sidebar-check'); ?>
			<?php else : ?>
				<!-- Close full width blog layout -->
				</div><!-- .col-lg-10 col-xl-8 -->
			<?php endif; ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
