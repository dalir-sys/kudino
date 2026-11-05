<?php
/**
 * Static page.
 *
 * @package Kudino
 */

get_header();
?>
<div class="kd-wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<header class="kd-pagehead">
			<?php kudino_breadcrumb(); ?>
			<h1 class="kd-pagehead__title"><?php the_title(); ?></h1>
		</header>
		<div class="kd-prose"><?php the_content(); ?></div>
	<?php endwhile; ?>
</div>
<?php
get_footer();
