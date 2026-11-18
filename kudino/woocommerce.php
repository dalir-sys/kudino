<?php
/**
 * WooCommerce wrapper (shop, product, cart, checkout, account).
 *
 * @package Kudino
 */

get_header();
$kudino_listing = is_shop() || is_product_taxonomy();
$kudino_sidebar = $kudino_listing && is_active_sidebar( 'sidebar-shop' );
?>
<div class="kd-wrap kd-woo">
	<header class="kd-pagehead"<?php echo is_product() ? ' style="border:0;margin:0;padding-block-end:0"' : ''; ?>>
		<?php
		woocommerce_breadcrumb(
			array(
				'wrap_before' => '<nav class="kd-crumbs" aria-label="مسیر">',
				'wrap_after'  => '</nav>',
				'delimiter'   => '<span aria-hidden="true">/</span>',
				'home'        => 'کودینو',
			)
		);
		?>
		<?php if ( ! is_product() && apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
			<h1 class="kd-pagehead__title"><?php $kudino_listing ? woocommerce_page_title() : the_title(); ?></h1>
		<?php endif; ?>
	</header>
	<div class="kd-layout<?php echo $kudino_sidebar ? '' : ' kd-layout--full'; ?>">
		<div><?php woocommerce_content(); ?></div>
		<?php if ( $kudino_sidebar ) : ?>
			<aside><?php dynamic_sidebar( 'sidebar-shop' ); ?></aside>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
