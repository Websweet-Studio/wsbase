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

			<!-- Do the left sidebar check -->
			<?php get_template_part('global-templates/left-sidebar-check'); ?>

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

			<?php
			// Display the pagination component.
			wsbase_pagination();
			// Do the right sidebar check.
			get_template_part('global-templates/right-sidebar-check');
			?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();
