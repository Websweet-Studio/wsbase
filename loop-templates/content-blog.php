<?php

/**
 * Template part for displaying posts in blog/index page with card layout
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<div class="col-lg-6 col-md-6 mb-4">
  <article <?php post_class('modern-card blog-card'); ?> id="post-<?php the_ID(); ?>">

    <?php if (has_post_thumbnail()) : ?>
      <div class="card-img-wrapper">
        <a href="<?php the_permalink(); ?>" class="d-block">
          <?php the_post_thumbnail('medium_large', ['class' => 'card-img-top', 'alt' => the_title_attribute(['echo' => false])]); ?>
        </a>
      </div>
    <?php endif; ?>

    <div class="card-content">
      <div class="card-meta">
        <?php if ('post' === get_post_type()) : ?>
          <div class="post-author">
            <span class="author-name"><?php echo get_the_author(); ?></span>
            <span class="post-separator">•</span>
            <span class="post-date"><?php echo get_the_date('j M Y'); ?></span>
          </div>
        <?php endif; ?>
      </div>

      <header class="entry-header">
        <?php
        the_title(
          sprintf('<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())),
          '</a></h3>'
        );
        ?>
      </header>

      <div class="entry-content">
        <?php
        // Get custom excerpt or trimmed content
        $excerpt = get_the_excerpt();
        if (empty($excerpt)) {
          $excerpt = wp_trim_words(get_the_content(), 20, '...');
        }
        echo '<p>' . $excerpt . '</p>';
        ?>
      </div>

      <footer class="entry-footer">
        <div class="post-tags">
          <?php
          $categories = get_the_category();
          if ($categories) {
            foreach ($categories as $category) {
              echo '<span class="tag-item">' . esc_html($category->name) . '</span>';
              break; // Only show first category
            }
          }

          $tags = get_the_tags();
          if ($tags) {
            $tag_count = 0;
            foreach ($tags as $tag) {
              if ($tag_count < 2) { // Show max 2 tags
                echo '<span class="tag-item">' . esc_html($tag->name) . '</span>';
                $tag_count++;
              }
            }
          }
          ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="read-more-arrow">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7" />
          </svg>
        </a>
      </footer>
    </div>
  </article>
</div>