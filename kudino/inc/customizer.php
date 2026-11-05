<?php
/**
 * Customizer: store contact info & homepage copy.
 *
 * @package Kudino
 */

defined( 'ABSPATH' ) || exit;

function kudino_mod_defaults() {
	return array(
		'shipping'     => 'ارسال رایگان سفارش‌های بالای ۲ میلیون تومان',
		'phone'        => '۰۲۱-۹۱۰۰۰۰۰۰',
		'email'        => 'hello@kudino.ir',
		'address'      => 'تهران، خیابان کشاورز، پلاک ۱۲',
		'hours'        => 'شنبه تا پنجشنبه، ۸ تا ۱۸',
		'hero_kicker'  => 'فروشگاه تخصصی کود و نهاده‌های کشاورزی',
		'hero_title'   => 'هر خاک، کودِ <em>خودش</em> را می‌خواهد.',
		'hero_text'    => 'کود شیمیایی، آلی و زیستی با آنالیز روشن و مشاوره کارشناس زراعت. بگویید چه می‌کارید؛ کود و زمان مصرفش را ما پیشنهاد می‌دهیم.',
		'instagram'    => '#',
		'telegram'     => '#',
	);
}

function kudino_mod( $key ) {
	$d = kudino_mod_defaults();
	return get_theme_mod( 'kudino_' . $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

function kudino_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'kudino_store',
		array(
			'title'    => __( 'تنظیمات کودینو', 'kudino' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'shipping'     => __( 'پیام ارسال (نوار بالا)', 'kudino' ),
		'phone'        => __( 'تلفن مشاوره', 'kudino' ),
		'email'        => __( 'ایمیل', 'kudino' ),
		'address'      => __( 'آدرس', 'kudino' ),
		'hours'        => __( 'ساعت پاسخ‌گویی', 'kudino' ),
		'hero_kicker'  => __( 'برچسب بالای تیتر', 'kudino' ),
		'hero_title'   => __( 'تیتر هیرو (تگ em مجاز است)', 'kudino' ),
		'hero_text'    => __( 'متن هیرو', 'kudino' ),
		'instagram'    => __( 'لینک اینستاگرام', 'kudino' ),
		'telegram'     => __( 'لینک تلگرام', 'kudino' ),
	);
	$d      = kudino_mod_defaults();

	foreach ( $fields as $key => $label ) {
		$is_url = in_array( $key, array( 'instagram', 'telegram' ), true );
		$wp_customize->add_setting(
			'kudino_' . $key,
			array(
				'default'           => $d[ $key ],
				'sanitize_callback' => $is_url ? 'esc_url_raw' : 'wp_kses_post',
			)
		);
		$wp_customize->add_control(
			'kudino_' . $key,
			array(
				'label'   => $label,
				'section' => 'kudino_store',
				'type'    => 'hero_text' === $key ? 'textarea' : ( $is_url ? 'url' : 'text' ),
			)
		);
	}
}
add_action( 'customize_register', 'kudino_customize_register' );
