<?php
/**
 * Journal entry.
 *
 * @package Kudino
 */

get_header();
?>
<div class="kd-wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		$kudino_cat = get_the_category();
		?>
		<header class="kd-pagehead">
			<?php kudino_breadcrumb(); ?>
			<h1 class="kd-pagehead__title" style="max-width:22ch"><?php the_title(); ?></h1>
			<div class="kd-meta" style="margin-block-start:16px">
				<?php if ( $kudino_cat ) : ?>
					<b><?php echo esc_html( $kudino_cat[0]->name ); ?></b>
				<?php endif; ?>
				<span><?php echo esc_html( get_the_author() ); ?></span>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( kudino_fa( get_the_date() ) ); ?></time>
			</div>
		</header>
		<div class="kd-layout<?php echo is_active_sidebar( 'sidebar-blog' ) ? '' : ' kd-layout--full'; ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="kd-cover" style="margin-inline:0"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="kd-prose">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>
			<?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
				<aside><?php dynamic_sidebar( 'sidebar-blog' ); ?></aside>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</div>
<?php
get_footer();
