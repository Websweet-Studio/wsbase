<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package Wsbase
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div class="comments-area modern-comments" id="comments">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>

		<div class="comments-header">
			<h2 class="comments-title">
				<?php
				$comments_number = get_comments_number();
				if ( 1 === (int) $comments_number ) {
					printf(
						/* translators: %s: post title */
						esc_html_x( 'One Comment', 'comments title', 'wsbase' )
					);
				} else {
					printf(
						esc_html(
							/* translators: 1: number of comments */
							_nx(
								'%1$s Comment',
								'%1$s Comments',
								$comments_number,
								'comments title',
								'wsbase'
							)
						),
						number_format_i18n( $comments_number )
					);
				}
				?>
			</h2>

			<div class="comments-count">
				<?php
				if ( 1 === (int) $comments_number ) {
					echo '<span class="count-badge">1</span>';
				} else {
					echo '<span class="count-badge">' . number_format_i18n( $comments_number ) . '</span>';
				}
				?>
			</div>
		</div>

		<?php wsbase_comment_navigation( 'comment-nav-above' ); ?>

		<div class="comment-list-wrapper">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'callback'   => 'wsbase_comment_callback',
				)
			);
			?>
		</div>

		<?php wsbase_comment_navigation( 'comment-nav-below' ); ?>

	<?php endif; // End of if have_comments(). ?>

	<?php if ( ! comments_open() ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'wsbase' ); ?></p>
	<?php endif; ?>

	<?php
	// Custom comment form with modern styling
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );

	$fields = array(
		'author' => '<div class="form-group"><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245"' . $aria_req . ' placeholder="' . esc_attr__( 'Your Name', 'wsbase' ) . '" required /></div>',
		'email'  => '<div class="form-group"><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . ' placeholder="' . esc_attr__( 'Your Email', 'wsbase' ) . '" required /></div>',
		'url'    => '<div class="form-group"><input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" maxlength="200" placeholder="' . esc_attr__( 'Your Website (optional)', 'wsbase' ) . '" /></div>',
	);

	$comment_field = '<div class="form-group"><textarea id="comment" name="comment" cols="45" rows="6" aria-required="true" placeholder="' . esc_attr__( 'Share your thoughts...', 'wsbase' ) . '" required></textarea></div>';

	$args = array(
		'fields'             => $fields,
		'comment_field'      => $comment_field,
		'must_log_in'        => '<p class="must-log-in">' . sprintf( wp_kses_post( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( get_the_ID() ) ) ) ) . '</p>',
		'logged_in_as'       => '<p class="logged-in-as">' . sprintf( wp_kses_post( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( get_the_ID() ) ) ) ) . '</p>',
		'comment_notes_before' => '',
		'comment_notes_after'  => '',
		'id_form'            => 'commentform',
		'id_submit'          => 'submit',
		'class_form'         => 'modern-comment-form',
		'name_submit'         => 'submit',
		'title_reply'         => esc_html__( 'Leave a Reply', 'wsbase' ),
		'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'wsbase' ),
		'title_reply_before'  => '<h3 id="reply-title" class="comment-reply-title">',
		'title_reply_after'   => '</h3>',
		'cancel_reply_before' => ' <small>',
		'cancel_reply_after'  => '</small>',
		'cancel_reply_link'   => esc_html__( 'Cancel reply', 'wsbase' ),
		'label_submit'        => esc_html__( 'Post Comment', 'wsbase' ),
		'submit_button'       => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
		'submit_field'        => '<div class="form-submit">%1$s %2$s</div>',
		'submit_class'        => 'btn btn-primary',
	);

	comment_form( $args );
	?>

</div><!-- #comments -->
