<?php

/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

if (!function_exists('wsbase_posted_on')) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function wsbase_posted_on()
	{
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if (get_the_time('U') !== get_the_modified_time('U')) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s"> (%4$s) </time>';
		}
		$time_string = sprintf(
			$time_string,
			esc_attr(get_the_date('c')),
			esc_html(get_the_date()),
			esc_attr(get_the_modified_date('c')),
			esc_html(get_the_modified_date())
		);
		$posted_on   = apply_filters(
			'wsbase_posted_on',
			sprintf(
				'<span class="posted-on">%1$s <a href="%2$s" rel="bookmark">%3$s</a></span>',
				esc_html_x('Posted on', 'post date', 'wsbase'),
				esc_url(get_permalink()),
				apply_filters('wsbase_posted_on_time', $time_string)
			)
		);
		$byline      = apply_filters(
			'wsbase_posted_by',
			sprintf(
				'<span class="byline"> %1$s<span class="author vcard"> <a class="url fn n" href="%2$s">%3$s</a></span></span>',
				$posted_on ? esc_html_x('by', 'post author', 'wsbase') : esc_html_x('Posted by', 'post author', 'wsbase'),
				esc_url(get_author_posts_url(get_the_author_meta('ID'))),
				esc_html(get_the_author())
			)
		);
		echo $posted_on . $byline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if (!function_exists('wsbase_entry_footer')) {
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function wsbase_entry_footer()
	{
		// Hide category and tag text for pages.
		if ('post' === get_post_type()) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list(esc_html__(', ', 'wsbase'));
			if ($categories_list && wsbase_categorized_blog()) {
				/* translators: %s: Categories of current post */
				printf('<span class="cat-links">' . esc_html__('Posted in %s', 'wsbase') . '</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list('', esc_html__(', ', 'wsbase'));
			if ($tags_list) {
				/* translators: %s: Tags of current post */
				printf('<span class="tags-links">' . esc_html__('Tagged %s', 'wsbase') . '</span>', $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
		if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
			echo '<span class="comments-link">';
			comments_popup_link(esc_html__('Leave a comment', 'wsbase'), esc_html__('1 Comment', 'wsbase'), esc_html__('% Comments', 'wsbase'));
			echo '</span>';
		}
		wsbase_edit_post_link();
	}
}

if (!function_exists('wsbase_categorized_blog')) {
	/**
	 * Returns true if a blog has more than 1 category.
	 *
	 * @return bool
	 */
	function wsbase_categorized_blog()
	{
		$all_the_cool_cats = get_transient('wsbase_categories');
		if (false === $all_the_cool_cats) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories(
				array(
					'fields'     => 'ids',
					'hide_empty' => 1,
					// We only need to know if there is more than one category.
					'number'     => 2,
				)
			);
			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count($all_the_cool_cats);
			set_transient('wsbase_categories', $all_the_cool_cats);
		}
		if ($all_the_cool_cats > 1) {
			// This blog has more than 1 category so wsbase_categorized_blog should return true.
			return true;
		}
		// This blog has only 1 category so wsbase_categorized_blog should return false.
		return false;
	}
}

add_action('edit_category', 'wsbase_category_transient_flusher');
add_action('save_post', 'wsbase_category_transient_flusher');

if (!function_exists('wsbase_category_transient_flusher')) {
	/**
	 * Flush out the transients used in wsbase_categorized_blog.
	 */
	function wsbase_category_transient_flusher()
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		// Like, beat it. Dig?
		delete_transient('wsbase_categories');
	}
}

if (!function_exists('wsbase_body_attributes')) {
	/**
	 * Displays the attributes for the body element.
	 */
	function wsbase_body_attributes()
	{
		/**
		 * Filters the body attributes.
		 *
		 * @param array $atts An associative array of attributes.
		 */
		$atts = array_unique(apply_filters('wsbase_body_attributes', $atts = array()));
		if (!is_array($atts) || empty($atts)) {
			return;
		}
		$attributes = '';
		foreach ($atts as $name => $value) {
			if ($value) {
				$attributes .= sanitize_key($name) . '="' . esc_attr($value) . '" ';
			} else {
				$attributes .= sanitize_key($name) . ' ';
			}
		}
		echo trim($attributes); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

if (!function_exists('wsbase_comment_navigation')) {
	/**
	 * Displays the comment navigation.
	 *
	 * @param string $nav_id The ID of the comment navigation.
	 */
	function wsbase_comment_navigation($nav_id)
	{
		if (get_comment_pages_count() <= 1) {
			// Return early if there are no comments to navigate through.
			return;
		}
?>
		<nav class="comment-navigation" id="<?php echo esc_attr($nav_id); ?>">

			<h1 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'wsbase'); ?></h1>

			<?php if (get_previous_comments_link()) { ?>
				<div class="nav-previous">
					<?php previous_comments_link(__('&larr; Older Comments', 'wsbase')); ?>
				</div>
			<?php } ?>

			<?php if (get_next_comments_link()) { ?>
				<div class="nav-next">
					<?php next_comments_link(__('Newer Comments &rarr;', 'wsbase')); ?>
				</div>
			<?php } ?>

		</nav><!-- #<?php echo esc_attr($nav_id); ?> -->
	<?php
	}
}

if (!function_exists('wsbase_edit_post_link')) {
	/**
	 * Displays the edit post link for post.
	 */
	function wsbase_edit_post_link()
	{
		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__('Edit %s', 'wsbase'),
				the_title('<span class="screen-reader-text">"', '"</span>', false)
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
}

if (!function_exists('wsbase_post_nav')) {
	/**
	 * Display navigation to next/previous post when applicable.
	 */
	function wsbase_post_nav()
	{
		// Don't print empty markup if there's nowhere to navigate.
		$previous = (is_attachment()) ? get_post(get_post()->post_parent) : get_adjacent_post(false, '', true);
		$next     = get_adjacent_post(false, '', false);
		if (!$next && !$previous) {
			return;
		}
		?>
		<nav class="modern-post-navigation">
			<div class="container">
				<h2 class="screen-reader-text"><?php esc_html_e('Post navigation', 'wsbase'); ?></h2>
				<div class="nav-links">
					<?php if ($previous) : ?>
						<div class="nav-item nav-previous">
							<a href="<?php echo esc_url(get_permalink($previous->ID)); ?>" class="nav-link">
								<?php if (has_post_thumbnail($previous->ID)) : ?>
									<div class="nav-image" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url($previous->ID, 'thumbnail')); ?>);"></div>
								<?php endif; ?>
								<div class="nav-content">
									<div class="nav-label">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
											<path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
										</svg>
										<?php esc_html_e('Previous Post', 'wsbase'); ?>
									</div>
									<div class="nav-title"><?php echo get_the_title($previous->ID); ?></div>
									<div class="nav-date"><?php echo get_the_date('M j, Y', $previous->ID); ?></div>
								</div>
							</a>
						</div>
					<?php endif; ?>

					<?php if ($next) : ?>
						<div class="nav-item nav-next">
							<a href="<?php echo esc_url(get_permalink($next->ID)); ?>" class="nav-link">
								<?php if (has_post_thumbnail($next->ID)) : ?>
									<div class="nav-image" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url($next->ID, 'thumbnail')); ?>);"></div>
								<?php endif; ?>
								<div class="nav-content">
									<div class="nav-label">
										<?php esc_html_e('Next Post', 'wsbase'); ?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
											<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
										</svg>
									</div>
									<div class="nav-title"><?php echo get_the_title($next->ID); ?></div>
									<div class="nav-date"><?php echo get_the_date('M j, Y', $next->ID); ?></div>
								</div>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</nav><!-- .modern-post-navigation -->
<?php
	}
}

if (!function_exists('wsbase_link_pages')) {
	/**
	 * Displays/retrieves page links for paginated posts (i.e. including the
	 * `<!--nextpage-->` Quicktag one or more times). This tag must be
	 * within The Loop. Default: echo.
	 *
	 * @return void|string Formatted output in HTML.
	 */
	function wsbase_link_pages()
	{
		$args = apply_filters(
			'wsbase_link_pages_args',
			array(
				'before' => '<div class="page-links">' . esc_html__('Pages:', 'wsbase'),
				'after'  => '</div>',
			)
		);
		wp_link_pages($args);
	}
}
