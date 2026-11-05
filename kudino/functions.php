<?php
/**
 * Kudino theme bootstrap.
 *
 * @package Kudino
 */

defined( 'ABSPATH' ) || exit;

define( 'KUDINO_VERSION', '2.0.0' );
define( 'KUDINO_DIR', get_template_directory() );
define( 'KUDINO_URI', get_template_directory_uri() );

require KUDINO_DIR . '/inc/icons.php';
require KUDINO_DIR . '/inc/helpers.php';
require KUDINO_DIR . '/inc/demo-data.php';
require KUDINO_DIR . '/inc/customizer.php';

if ( class_exists( 'WooCommerce' ) ) {
	require KUDINO_DIR . '/inc/woocommerce.php';
}

/**
 * Theme supports & menus.
 */
function kudino_setup() {
	load_theme_textdomain( 'kudino', KUDINO_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_image_size( 'kudino-card', 600, 600, true );
	add_image_size( 'kudino-wide', 960, 600, true );

	register_nav_menus(
		array(
			'primary' => __( 'منوی اصلی', 'kudino' ),
			'footer'  => __( 'منوی فوتر', 'kudino' ),
		)
	);
}
add_action( 'after_setup_theme', 'kudino_setup' );

/**
 * Kudino is a Persian (RTL) store theme. Force RTL even when the
 * Persian language pack is not installed yet.
 */
function kudino_force_rtl() {
	global $wp_locale;
	if ( $wp_locale ) {
		$wp_locale->text_direction = 'rtl';
	}
}
add_action( 'init', 'kudino_force_rtl', 1 );
add_action( 'admin_init', 'kudino_force_rtl', 1 );

function kudino_language_attributes( $output ) {
	if ( false === strpos( $output, 'dir=' ) ) {
		$output = 'dir="rtl" ' . $output;
	}
	return $output;
}
add_filter( 'language_attributes', 'kudino_language_attributes' );

/**
 * Assets.
 */
function kudino_assets() {
	wp_enqueue_style( 'kudino-tokens', KUDINO_URI . '/assets/css/tokens.css', array(), KUDINO_VERSION );
	wp_enqueue_style( 'kudino-main', KUDINO_URI . '/assets/css/main.css', array( 'kudino-tokens' ), KUDINO_VERSION );

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'kudino-woocommerce', KUDINO_URI . '/assets/css/woocommerce.css', array( 'kudino-main' ), KUDINO_VERSION );
	}

	wp_enqueue_script( 'kudino-main', KUDINO_URI . '/assets/js/main.js', array(), KUDINO_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'kudino_assets' );

function kudino_preload_fonts() {
	foreach ( array( 'markazi-text-arabic-wght-normal', 'ibm-plex-sans-arabic-arabic-400-normal', 'ibm-plex-sans-arabic-arabic-600-normal' ) as $file ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( KUDINO_URI . '/assets/fonts/' . $file . '.woff2' )
		);
	}
	echo '<meta name="theme-color" content="#f3f2ed">' . "\n";
}
add_action( 'wp_head', 'kudino_preload_fonts', 1 );

/**
 * Widgets.
 */
function kudino_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'سایدبار وبلاگ', 'kudino' ),
			'id'            => 'sidebar-blog',
			'before_widget' => '<section id="%1$s" class="kd-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="kd-widget__title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'سایدبار فروشگاه', 'kudino' ),
			'id'            => 'sidebar-shop',
			'before_widget' => '<section id="%1$s" class="kd-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="kd-widget__title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'kudino_widgets_init' );

function kudino_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'kudino_excerpt_length' );

function kudino_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'kudino_excerpt_more' );

function kudino_body_classes( $classes ) {
	$classes[] = 'kd';
	return $classes;
}
add_filter( 'body_class', 'kudino_body_classes' );
