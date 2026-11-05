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
		'hero_title'   => 'همراه هر جوانه،<br><em>از کاشت تا برداشت</em>',
		'hero_text'    => 'کود شیمیایی، آلی و زیستی اصل با آنالیز روشن روی بسته، مشاوره رایگان کارشناس کشاورزی و ارسال تا درِ مزرعه.',
		'installment'  => 'خرید اقساطی کود و نهاده',
		'whatsapp'     => '989120000000',
		'offer_days'   => '3',
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
		'installment'  => __( 'پیام خرید اقساطی (نوار بالا)', 'kudino' ),
		'whatsapp'     => __( 'شماره واتساپ (بین‌المللی، بدون +)', 'kudino' ),
		'offer_days'   => __( 'مدت پیشنهاد ویژه (روز، از امروز)', 'kudino' ),
		'instagram'    => __( 'لینک اینستاگرام', 'kudino' ),
		'telegram'     => __( 'لینک تلگرام', 'kudino' ),
	);
	$d      = kudino_mod_defaults();

	// Photos: upload real field / product photos; illustrations show until then.
	$images = array(
		'img_hero'     => __( 'عکس اسلاید اول (پیشنهاد ۱۹۲۰×۸۰۰)', 'kudino' ),
		'img_chemical' => __( 'عکس بنر کودهای شیمیایی', 'kudino' ),
		'img_organic'  => __( 'عکس بنر کودهای آلی', 'kudino' ),
	);
	foreach ( $images as $key => $label ) {
		$wp_customize->add_setting( 'kudino_' . $key, array( 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'kudino_' . $key,
				array(
					'label'     => $label,
					'section'   => 'kudino_store',
					'mime_type' => 'image',
				)
			)
		);
	}

	foreach ( $fields as $key => $label ) {
		$is_url = in_array( $key, array( 'instagram', 'telegram' ), true );
		$is_num = in_array( $key, array( 'whatsapp', 'offer_days' ), true );
		$wp_customize->add_setting(
			'kudino_' . $key,
			array(
				'default'           => $d[ $key ],
				'sanitize_callback' => $is_url ? 'esc_url_raw' : ( $is_num ? 'absint' : 'wp_kses_post' ),
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

/**
 * An uploaded Customizer photo, or '' when none is set.
 */
function kudino_mod_image( $key, $size = 'full', $attr = array() ) {
	$id = (int) get_theme_mod( 'kudino_' . $key );
	return $id ? wp_get_attachment_image( $id, $size, false, $attr ) : '';
}
