<?php
/**
 * WooCommerce integration: wrappers, Kudino product card in loops, NPK field.
 *
 * @package Kudino
 */

defined( 'ABSPATH' ) || exit;

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_filter( 'woocommerce_enqueue_styles', function ( $styles ) {
	unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
	return $styles;
} );

add_filter( 'loop_shop_columns', function () {
	return 4;
} );

/* Shop loop: a ruled shelf instead of WooCommerce's float grid. */
add_filter( 'woocommerce_product_loop_start', function () {
	return '<div class="kd-grid products">';
} );
add_filter( 'woocommerce_product_loop_end', function () {
	return '</div>';
} );

add_filter( 'loop_shop_per_page', function () {
	return 16;
} );

/**
 * NPK field on the product edit screen (General tab), e.g. "20-20-20".
 */
add_action( 'woocommerce_product_options_general_product_data', function () {
	woocommerce_wp_text_input(
		array(
			'id'          => '_kudino_npk',
			'label'       => __( 'آنالیز NPK', 'kudino' ),
			'placeholder' => '20-20-20',
			'desc_tip'    => true,
			'description' => __( 'درصد نیتروژن-فسفر-پتاسیم، با خط تیره جدا شود. برای کودهای بدون NPK خالی بگذارید.', 'kudino' ),
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => '_kudino_weight',
			'label'       => __( 'وزن/حجم بسته', 'kudino' ),
			'placeholder' => '۱ کیلوگرم',
		)
	);
} );

add_action( 'woocommerce_admin_process_product_object', function ( $product ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the nonce before this hook.
	if ( isset( $_POST['_kudino_npk'] ) ) {
		$product->update_meta_data( '_kudino_npk', sanitize_text_field( wp_unslash( $_POST['_kudino_npk'] ) ) );
	}
	if ( isset( $_POST['_kudino_weight'] ) ) {
		$product->update_meta_data( '_kudino_weight', sanitize_text_field( wp_unslash( $_POST['_kudino_weight'] ) ) );
	}
	// phpcs:enable
} );

/**
 * Map a WC_Product to the array kudino_product_card() expects.
 *
 * @param WC_Product $product Product.
 */
function kudino_wc_product_data( $product ) {
	$npk  = array_filter( array_map( 'trim', explode( '-', (string) $product->get_meta( '_kudino_npk' ) ) ), 'strlen' );
	$cats = wc_get_product_category_list( $product->get_id() );
	$cat  = $cats ? wp_strip_all_tags( explode( ',', $cats )[0] ) : '';

	$stock = 'in';
	$qty   = 0;
	if ( ! $product->is_in_stock() ) {
		$stock = 'out';
	} elseif ( $product->managing_stock() && $product->get_stock_quantity() !== null && $product->get_stock_quantity() <= 5 ) {
		$stock = 'low';
		$qty   = (int) $product->get_stock_quantity();
	}

	$attrs = sprintf(
		'data-quantity="1" data-product_id="%d" data-product_sku="%s" class="kd-add %s"',
		$product->get_id(),
		esc_attr( $product->get_sku() ),
		$product->is_purchasable() && $product->is_in_stock() && $product->supports( 'ajax_add_to_cart' ) ? 'add_to_cart_button ajax_add_to_cart' : ''
	);

	$terms = get_the_terms( $product->get_id(), 'product_cat' );

	return array(
		'group'     => ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : '',
		'title'     => $product->get_name(),
		'url'       => $product->get_permalink(),
		'cat'       => $cat,
		'weight'    => $product->get_meta( '_kudino_weight' ),
		'npk'       => count( $npk ) === 3 ? array_values( $npk ) : array(),
		'spec'      => $product->get_short_description() ? wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 8, '…' ) : '',
		'price'     => (float) wc_get_price_to_display( $product ),
		'old_price' => $product->is_on_sale() ? (float) wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ) : 0,
		'rating'    => $product->get_average_rating() > 0 ? number_format( (float) $product->get_average_rating(), 1 ) : '',
		'image'     => $product->get_image_id() ? wp_get_attachment_image( $product->get_image_id(), 'kudino-card', false, array( 'class' => 'is-photo' ) ) : '',
		'stock'     => $stock,
		'stock_qty' => $qty,
		'add_url'   => $product->add_to_cart_url(),
		'add_attrs' => $attrs,
	);
}

/**
 * Products for the homepage: real ones if any exist, else demo data.
 */
function kudino_home_products( $limit = 8 ) {
	$products = wc_get_products(
		array(
			'limit'   => $limit,
			'status'  => 'publish',
			'orderby' => 'popularity',
		)
	);
	return $products ? array_map( 'kudino_wc_product_data', $products ) : kudino_demo_products();
}

/* Loop items are rendered by woocommerce/content-product.php (the Kudino shelf item). */

/**
 * Keep the header cart count fresh after AJAX add-to-cart.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['span.kd-cart-count'] = kudino_cart_count_html();
	return $fragments;
} );

/**
 * Single product: show NPK tiles under the price.
 */
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	$npk = array_filter( array_map( 'trim', explode( '-', (string) $product->get_meta( '_kudino_npk' ) ) ), 'strlen' );
	if ( count( $npk ) === 3 ) {
		echo '<div class="kd-single-npk">' . kudino_npk( array_values( $npk ) ) . '</div>'; // phpcs:ignore
	}
}, 15 );
