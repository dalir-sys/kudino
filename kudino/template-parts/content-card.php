<?php
/**
 * Journal entry in archive lists.
 *
 * @package Kudino
 */

$kudino_cat = get_the_category();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'kd-entry' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="kd-entry__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'kudino-wide' ); ?></a>
	<?php endif; ?>
	<div class="kd-meta">
		<?php if ( $kudino_cat ) : ?>
			<b><?php echo esc_html( $kudino_cat[0]->name ); ?></b>
		<?php endif; ?>
		<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( kudino_fa( get_the_date() ) ); ?></time>
	</div>
	<h2 class="kd-entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	<p><?php echo esc_html( get_the_excerpt() ); ?></p>
</article>
