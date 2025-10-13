<?php
/**
 * Single post partial template
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

		<!-- Post Header -->
		<header class="post-header">

			<!-- Categories -->
			<?php if ( has_category() ) : ?>
				<div class="post-categories mb-3">
					<?php
					$categories = get_the_category();
					foreach ( $categories as $category ) :
						?>
						<span class="category-badge">
							<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
								<?php echo esc_html( $category->name ); ?>
							</a>
						</span>
						<?php
					endforeach;
					?>
				</div>
			<?php endif; ?>

			<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>

			<!-- Post Meta -->
			<div class="post-meta">
				<div class="meta-left">
					<div class="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 48, '', '', array( 'class' => 'rounded-circle' ) ); ?>
					</div>
					<div class="author-info">
						<div class="author-name">
							<?php the_author(); ?>
						</div>
						<div class="post-date">
							<?php echo get_the_date( 'F j, Y' ); ?>
						</div>
					</div>
				</div>
				<div class="meta-right">
					<div class="reading-time">
						<i class="far fa-clock"></i>
						<?php echo wsbase_reading_time(); ?>
					</div>
					<div class="post-comments">
						<i class="far fa-comment"></i>
						<?php echo get_comments_number(); ?>
					</div>
				</div>
			</div>

		</header><!-- .post-header -->

		<!-- Post Content -->
		<div class="post-content">

			<?php
			the_content();
			wsbase_link_pages();
			?>

		</div><!-- .post-content -->

		<!-- Post Footer -->
		<footer class="post-footer">

			<!-- Tags -->
			<?php if ( has_tag() ) : ?>
				<div class="post-tags">
					<h4 class="tags-title">Tags</h4>
					<div class="tag-list">
						<?php
						$tags = get_the_tags();
						foreach ( $tags as $tag ) :
							?>
							<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="tag-item">
								<?php echo esc_html( $tag->name ); ?>
							</a>
							<?php
						endforeach;
						?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Share Buttons -->
			<div class="post-share">
				<h4 class="share-title">Share this post</h4>
				<div class="share-buttons">
					<a href="#" class="share-button facebook" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>', 'facebook-share', 'width=580,height=296');return false;">
						<i class="fab fa-facebook-f"></i>
					</a>
					<a href="#" class="share-button twitter" onclick="window.open('https://twitter.com/intent/tweet?text=<?php echo urlencode( get_the_title() ); ?>&url=<?php echo urlencode( get_permalink() ); ?>', 'twitter-share', 'width=550,height=235');return false;">
						<i class="fab fa-twitter"></i>
					</a>
					<a href="#" class="share-button linkedin" onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode( get_permalink() ); ?>', 'linkedin-share', 'width=550,height=235');return false;">
						<i class="fab fa-linkedin-in"></i>
					</a>
					<a href="#" class="share-button whatsapp" onclick="window.open('https://wa.me/?text=<?php echo urlencode( get_the_title() . ' ' . get_permalink() ); ?>', 'whatsapp-share', 'width=550,height=235');return false;">
						<i class="fab fa-whatsapp"></i>
					</a>
				</div>
			</div>

			<!-- Author Bio -->
			<div class="author-bio">
				<div class="author-bio-avatar">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 80, '', '', array( 'class' => 'rounded-circle' ) ); ?>
				</div>
				<div class="author-bio-content">
					<h4 class="author-bio-name"><?php the_author(); ?></h4>
					<p class="author-bio-description"><?php echo get_the_author_meta( 'description' ); ?></p>
					<div class="author-social">
						<?php
						$author_email = get_the_author_meta( 'email' );
						if ( $author_email ) :
							?>
							<a href="mailto:<?php echo esc_attr( $author_email ); ?>" class="author-social-link">
								<i class="fas fa-envelope"></i>
							</a>
							<?php
						endif;
						?>
					</div>
				</div>
			</div>

		</footer><!-- .post-footer -->

	</div><!-- .post-content-wrapper -->

</article><!-- #post-## -->
