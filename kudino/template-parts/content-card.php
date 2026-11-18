<?php
/**
 * Journal card (homepage and archives).
 *
 * @package Kudino
 */

$kudino_cat = get_the_category();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'kd-post' ); ?>>
	<a class="kd-post__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'kudino-wide' );
		} else {
			echo kudino_post_art( get_the_ID() ); // phpcs:ignore
		}
		?>
	</a>
	<div class="kd-post__meta">
		<?php if ( $kudino_cat ) : ?>
			<span class="kd-tag"><?php echo esc_html( $kudino_cat[0]->name ); ?></span>
		<?php endif; ?>
		<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( kudino_fa( get_the_date() ) ); ?></time>
	</div>
	<h3 class="kd-post__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<span class="kd-post__more"><?php esc_html_e( 'ادامه مطلب', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 14 ); // phpcs:ignore ?></span>
</article>
