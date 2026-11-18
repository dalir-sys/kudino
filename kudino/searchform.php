<?php
/**
 * Search form.
 *
 * @package Kudino
 */

$kudino_id = wp_unique_id( 'kd-s-' );
?>
<form role="search" method="get" class="kd-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="kd-control" for="<?php echo esc_attr( $kudino_id ); ?>">
		<?php echo kudino_icon( 'search', 18 ); // phpcs:ignore ?>
		<span class="kd-sr"><?php esc_html_e( 'جستجو', 'kudino' ); ?></span>
		<input id="<?php echo esc_attr( $kudino_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'نام کود یا عنصر…', 'kudino' ); ?>">
		<button class="kd-btn kd-btn--sm" type="submit"><?php esc_html_e( 'جستجو', 'kudino' ); ?></button>
	</label>
</form>
