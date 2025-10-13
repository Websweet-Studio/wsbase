<?php

/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class('card mb-4 shadow-sm'); ?> id="post-<?php the_ID(); ?>">

	<?php if (has_post_thumbnail()) : ?>
		<div class="card-img-wrapper">
			<a href="<?php the_permalink(); ?>" class="d-block">
				<?php the_post_thumbnail('large', ['class' => 'card-img-top img-fluid', 'alt' => the_title_attribute(['echo' => false])]); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="card-body">
		<header class="entry-header mb-3">
			<?php
			the_title(
				sprintf('<h2 class="entry-title card-title h4"><a href="%s" rel="bookmark" class="text-decoration-none">', esc_url(get_permalink())),
				'</a></h2>'
			);
			?>

			<?php if ('post' === get_post_type()) : ?>
				<div class="entry-meta small text-muted mb-2">
					<?php wsbase_posted_on(); ?>
				</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
			the_excerpt();
			?>
		</div><!-- .entry-content -->

		<footer class="entry-footer mt-3">
			<?php wsbase_entry_footer(); ?>
		</footer><!-- .entry-footer -->
	</div><!-- .card-body -->

</article><!-- #post-## -->