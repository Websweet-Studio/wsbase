<?php

/**
 * The template part for displaying a message that posts cannot be found
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<section class="no-results not-found">

	<div class="card text-center py-5">
		<div class="card-body">
			<header class="page-header mb-4">
				<h1 class="page-title display-4 text-muted"><?php esc_html_e('Nothing Found', 'wsbase'); ?></h1>
			</header><!-- .page-header -->

			<div class="page-content">
				<?php
				if (is_home() && current_user_can('publish_posts')) :

					$kses = array('a' => array('href' => array()));
					printf(
						/* translators: 1: Link to WP admin new post page. */
						'<div class="alert alert-info">' . wp_kses(__('Ready to publish your first post? <a href="%1$s" class="alert-link">Get started here</a>.', 'wsbase'), $kses) . '</div>',
						esc_url(admin_url('post-new.php'))
					);

				elseif (is_search()) :
				?>
					<div class="mb-4">
						<p class="lead text-muted"><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wsbase'); ?></p>
					</div>
					<div class="search-form-wrapper">
						<?php get_search_form(); ?>
					</div>
				<?php
				else :
				?>
					<div class="mb-4">
						<p class="lead text-muted"><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wsbase'); ?></p>
					</div>
					<div class="search-form-wrapper">
						<?php get_search_form(); ?>
					</div>
				<?php
				endif;
				?>
			</div><!-- .page-content -->

</section><!-- .no-results -->