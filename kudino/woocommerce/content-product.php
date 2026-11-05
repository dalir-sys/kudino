<?php
/**
 * Shop loop item — the Kudino shelf item instead of WooCommerce's <li> card.
 *
 * @package Kudino
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

echo kudino_product_card( kudino_wc_product_data( $product ) ); // phpcs:ignore WordPress.Security.EscapeOutput
