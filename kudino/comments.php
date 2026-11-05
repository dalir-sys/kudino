<?php
/**
 * Comments.
 *
 * @package Kudino
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="kd-band" style="max-width:70ch">
	<?php if ( have_comments() ) : ?>
		<h2 class="kd-title" style="font-size:2rem;margin-block-end:16px">
			<?php
			/* translators: %s: comment count */
			printf( esc_html__( '%s دیدگاه', 'kudino' ), esc_html( kudino_fa( get_comments_number() ) ) );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'avatar_size' => 0,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
