<?php
/**
 * Partial template for content in page.php
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class( 'modern-single-post' ); ?> id="post-<?php the_ID(); ?>">

	<!-- Featured Image -->
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-hero-image">
			<?php
			the_post_thumbnail( 'large', array(
				'class' => 'img-fluid w-100',
				'alt' => get_the_title()
			) );
			?>
		</div>
	<?php endif; ?>

	<div class="post-content-wrapper">

		<!-- Page Header -->
		<header class="post-header">

			<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>

		</header><!-- .post-header -->

		<!-- Page Content -->
		<div class="post-content">

			<?php
			the_content();
			wsbase_link_pages();
			?>

		</div><!-- .post-content -->

		<!-- Page Footer -->
		<footer class="post-footer">

			<?php wsbase_edit_post_link(); ?>

		</footer><!-- .post-footer -->

	</div><!-- .post-content-wrapper -->

</article><!-- #post-## -->
