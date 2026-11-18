<?php
/**
 * Journal index, archives and search results.
 *
 * @package Kudino
 */

get_header();
?>
<div class="kd-wrap">
	<header class="kd-pagehead">
		<?php kudino_breadcrumb(); ?>
		<h1 class="kd-pagehead__title">
			<?php
			if ( is_search() ) {
				/* translators: %s: search query */
				printf( esc_html__( 'نتیجه‌ها برای «%s»', 'kudino' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
			} else {
				esc_html_e( 'دفترچه کشت', 'kudino' );
			}
			?>
		</h1>
		<?php if ( is_archive() && get_the_archive_description() ) : ?>
			<div class="kd-pagehead__desc"><?php the_archive_description(); ?></div>
		<?php elseif ( ! is_search() && ! is_archive() ) : ?>
			<p class="kd-pagehead__desc"><?php esc_html_e( 'یادداشت‌های کارشناس‌های ما درباره کوددهی، آزمایش خاک و تقویم باغ و مزرعه.', 'kudino' ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="kd-posts">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'card' );
			endwhile;
			?>
		</div>
		<nav class="kd-pager" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'kudino' ); ?>">
			<?php
			echo wp_kses_post(
				kudino_fa(
					paginate_links(
						array(
							'prev_text' => '→',
							'next_text' => '←',
						)
					)
				)
			);
			?>
		</nav>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
