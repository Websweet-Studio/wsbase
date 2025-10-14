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
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
						</svg>
					</a>
					<a href="#" class="share-button twitter" onclick="window.open('https://twitter.com/intent/tweet?text=<?php echo urlencode( get_the_title() ); ?>&url=<?php echo urlencode( get_permalink() ); ?>', 'twitter-share', 'width=550,height=235');return false;">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
						</svg>
					</a>
					<a href="#" class="share-button linkedin" onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode( get_permalink() ); ?>', 'linkedin-share', 'width=550,height=235');return false;">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
							<rect x="2" y="9" width="4" height="12"></rect>
							<circle cx="4" cy="4" r="2"></circle>
						</svg>
					</a>
					<a href="#" class="share-button whatsapp" onclick="window.open('https://wa.me/?text=<?php echo urlencode( get_the_title() . ' ' . get_permalink() ); ?>', 'whatsapp-share', 'width=550,height=235');return false;">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
							<line x1="9" y1="10" x2="15" y2="10"></line>
							<line x1="9" y1="14" x2="15" y2="14"></line>
						</svg>
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
