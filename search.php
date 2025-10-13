<?php
/**
 * The template for displaying search results pages
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'wsbase_container_type' );

?>

<div class="wrapper" id="search-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Check if search should have sidebar -->
			<?php
			$sidebar_pos = get_theme_mod('wsbase_sidebar_position');
			$has_sidebar = in_array($sidebar_pos, ['left', 'both']) && !is_front_page();
			?>

			<?php if ($has_sidebar) : ?>
				<!-- Do the left sidebar check -->
				<?php get_template_part('global-templates/left-sidebar-check'); ?>
			<?php else : ?>
				<!-- Full width for search -->
				<div class="col-lg-10 col-xl-8 mx-auto">
			<?php endif; ?>

			<main class="site-main" id="main">

				<?php if ( have_posts() ) : ?>

					<header class="page-header">

							<h1 class="page-title">
								<?php
								printf(
									/* translators: %s: query term */
									esc_html__( 'Search Results for: %s', 'wsbase' ),
									'<span>' . get_search_query() . '</span>'
								);
								?>
							</h1>

					</header><!-- .page-header -->

					<?php /* Start the Loop */ ?>
					<?php
					while ( have_posts() ) :
						the_post();

						/*
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'loop-templates/content', 'search' );
					endwhile;
					?>

				<?php else : ?>

					<?php get_template_part( 'loop-templates/content', 'none' ); ?>

				<?php endif; ?>

			</main><!-- #main -->

			<!-- The pagination component -->
			<?php wsbase_pagination(); ?>

			<?php if ($has_sidebar) : ?>
				<!-- Do the right sidebar check -->
				<?php get_template_part('global-templates/right-sidebar-check'); ?>
			<?php else : ?>
				<!-- Close full width search layout -->
				</div><!-- .col-lg-10 col-xl-8 -->
			<?php endif; ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #search-wrapper -->

<?php
get_footer();
