<?php

/**
 * The template for displaying archive pages
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('wsbase_container_type');
?>

<div class="wrapper" id="archive-wrapper">

	<div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Check if archive should have sidebar -->
			<?php
			$sidebar_pos = get_theme_mod('wsbase_sidebar_position');
			$has_sidebar = in_array($sidebar_pos, ['left', 'right', 'both']) && !is_front_page();
			?>

			<?php if ($has_sidebar) : ?>
				<!-- Do the left sidebar check -->
				<?php get_template_part('global-templates/left-sidebar-check'); ?>
			<?php else : ?>
				<!-- Full width for archive -->
				<div class="col-lg-10 col-xl-8 mx-auto">
			<?php endif; ?>

			<main class="site-main" id="main">

				<?php
				if (have_posts()) {
				?>
					<header class="page-header mb-4">
						<?php
						the_archive_title('<h1 class="page-title display-4 mb-3">', '</h1>');
						the_archive_description('<div class="taxonomy-description lead text-muted">', '</div>');
						?>
					</header><!-- .page-header -->

					<div class="archive-posts">
						<div class="row">
							<?php
							// Start the loop.
							while (have_posts()) {
								the_post();

								/*
								 * Use the grid template for archive layout
								 */
								get_template_part('loop-templates/content-archive');
							}
							?>
						</div><!-- .row -->
					</div><!-- .archive-posts -->
				<?php
				} else {
					get_template_part('loop-templates/content', 'none');
				}
				?>

			</main><!-- #main -->

			<!-- The pagination component -->
			<?php wsbase_pagination(); ?>

			<?php if ($has_sidebar) : ?>
				<!-- Do the right sidebar check -->
				<?php get_template_part('global-templates/right-sidebar-check'); ?>
			<?php else : ?>
				<!-- Close full width archive layout -->
				</div><!-- .col-lg-10 col-xl-8 -->
			<?php endif; ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();
