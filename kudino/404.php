<?php
/**
 * Not found.
 *
 * @package Kudino
 */

get_header();
?>
<div class="kd-wrap">
	<div class="kd-empty" style="padding-block:96px">
		<div class="kd-empty__big">4-0-<span>4</span></div>
		<h1 class="kd-title"><?php esc_html_e( 'این صفحه در خاک ما نرویید.', 'kudino' ); ?></h1>
		<p class="kd-lede"><?php esc_html_e( 'آدرس را بررسی کنید یا نام کود و عنصر مورد نظر را جستجو کنید.', 'kudino' ); ?></p>
		<?php get_search_form(); ?>
		<a class="kd-more" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به صفحه اصلی', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a>
	</div>
</div>
<?php
get_footer();
