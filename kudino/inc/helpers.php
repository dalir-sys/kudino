<?php
/**
 * Rendering helpers: digits & dates, logo, pack illustrations, analysis, shelf item.
 *
 * @package Kudino
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------ digits & dates */

/**
 * Latin → Persian digits (and decimal point → Persian decimal separator).
 */
function kudino_fa( $value ) {
	$value = (string) $value;
	$value = preg_replace( '/(\d)\.(\d)/u', '$1٫$2', $value );
	return strtr( $value, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
}

/**
 * Toman price with Persian digits and the Persian thousands separator.
 */
function kudino_price( $amount ) {
	return kudino_fa( number_format( (float) $amount, 0, '.', '٬' ) );
}

/**
 * Gregorian → Jalali.
 *
 * @return int[] [year, month (1-12), day]
 */
function kudino_jalali( $gy, $gm, $gd ) {
	$g_d_m = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
	$gy2   = ( $gm > 2 ) ? ( $gy + 1 ) : $gy;
	$days  = 355666 + ( 365 * $gy ) + intdiv( $gy2 + 3, 4 ) - intdiv( $gy2 + 99, 100 ) + intdiv( $gy2 + 399, 400 ) + $gd + $g_d_m[ $gm - 1 ];
	$jy    = -1595 + ( 33 * intdiv( $days, 12053 ) );
	$days %= 12053;
	$jy   += 4 * intdiv( $days, 1461 );
	$days %= 1461;
	if ( $days > 365 ) {
		$jy  += intdiv( $days - 1, 365 );
		$days = ( $days - 1 ) % 365;
	}
	if ( $days < 186 ) {
		$jm = 1 + intdiv( $days, 31 );
		$jd = 1 + ( $days % 31 );
	} else {
		$jm = 7 + intdiv( $days - 186, 30 );
		$jd = 1 + ( ( $days - 186 ) % 30 );
	}
	return array( $jy, $jm, $jd );
}

function kudino_months() {
	return array( 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند' );
}

/**
 * Today in the Persian calendar, using the site timezone.
 */
function kudino_today() {
	$now               = current_datetime();
	list( $y, $m, $d ) = kudino_jalali( (int) $now->format( 'Y' ), (int) $now->format( 'n' ), (int) $now->format( 'j' ) );
	$seasons           = array( 'بهار', 'تابستان', 'پاییز', 'زمستان' );
	$month             = kudino_months()[ $m - 1 ];
	return array(
		'y'      => $y,
		'm'      => $m,
		'd'      => $d,
		'month'  => $month,
		'season' => $seasons[ intdiv( $m - 1, 3 ) ],
		'label'  => kudino_fa( $d ) . ' ' . $month . ' ' . kudino_fa( $y ),
	);
}

/* ------------------------------------------------------------------ brand */

/**
 * Mark: a sprout rising from two fertilizer granules, on a field-green tile.
 */
function kudino_logo_mark( $class = 'kd-logo__mark' ) {
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect width="48" height="48" rx="13" fill="#1a6b3c"/><path d="M24 36.5V23" stroke="#fff" stroke-width="2.8" stroke-linecap="round"/><path d="M24 26.5c0-6.6-4.3-11-11.8-11 0 6.6 4.3 11 11.8 11Z" fill="#9cc98a"/><path d="M24 23.2c0-6.2 4-10.6 11.6-10.6 0 6.2-4 10.6-11.6 10.6Z" fill="#fff"/><circle cx="17" cy="37" r="2.6" fill="#e8711a"/><circle cx="31" cy="37" r="2.6" fill="#e8711a"/></svg>';
}

function kudino_logo( $inverse = false ) {
	if ( ! $inverse && has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf(
		'<a class="kd-logo%1$s" href="%2$s" rel="home">%3$s<span class="kd-logo__text"><span class="kd-logo__name">%4$s</span><span class="kd-logo__tag">%5$s</span></span></a>',
		$inverse ? ' kd-logo--inverse' : '',
		esc_url( home_url( '/' ) ),
		kudino_logo_mark(), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'کودینو' ),
		esc_html__( 'فروشگاه تخصصی کود', 'kudino' )
	);
}

/* ------------------------------------------------------------------ illustrations */

/**
 * Product-style packaging drawing used until a product has a photo.
 *
 * @param string $type  pouch | sack | jug.
 * @param string $color Brand colour of the pack.
 * @param string $code  Analysis or short code (Latin, e.g. "20-20-20").
 * @param string $name  Short Persian name.
 */
function kudino_pack_svg( $type = 'pouch', $color = '#1a6b3c', $code = '', $name = '' ) {
	$c    = sanitize_hex_color( $color ) ? $color : '#1a6b3c';
	$code = esc_html( $code );
	$name = esc_html( $name );
	$mono = 'font-family="IBM Plex Mono, monospace" font-weight="500"';
	$fa   = 'font-family="Estedad, Tahoma, sans-serif"';
	$leaf = '<path d="M0 6c0-3.6-2.4-6-6.4-6 0 3.6 2.4 6 6.4 6Zm0-1.8C0 .8 2.2-1.6 6.3-1.6 6.3 1.8 4.1 4.2 0 4.2Z" fill="' . $c . '"/>';

	switch ( $type ) {
		case 'sack':
			$weave = '';
			for ( $y = 50; $y < 172; $y += 7 ) {
				$weave .= '<line x1="42" y1="' . $y . '" x2="158" y2="' . $y . '"/>';
			}
			$svg = '<path d="M42 38c0-4 2-6 6-6h104c4 0 6 2 6 6l4 132c0 5-3 8-8 8H46c-5 0-8-3-8-8Z" fill="#fbfaf6"/>'
				. '<g stroke="#000" stroke-opacity=".04">' . $weave . '</g>'
				. '<path d="M40 40h120" stroke="#000" stroke-opacity=".12" stroke-dasharray="3 3"/>'
				. '<rect x="40" y="46" width="120" height="22" fill="' . $c . '"/>'
				. '<text x="100" y="61.5" text-anchor="middle" ' . $fa . ' font-weight="800" font-size="11" fill="#fff">کودینو</text>'
				. '<circle cx="100" cy="106" r="30" fill="' . $c . '" opacity=".12"/>'
				. '<text x="100" y="112" text-anchor="middle" ' . $mono . ' font-size="17" fill="' . $c . '" direction="ltr">' . $code . '</text>'
				. '<text x="100" y="152" text-anchor="middle" ' . $fa . ' font-weight="800" font-size="12" fill="#1d2a22">' . $name . '</text>'
				. '<rect x="40" y="164" width="122" height="8" fill="' . $c . '"/>'
				. '<path d="M152 34v142" stroke="#000" stroke-opacity=".05" stroke-width="10"/>';
			break;
		case 'jug':
			$svg = '<rect x="85" y="24" width="30" height="16" rx="3" fill="' . $c . '"/>'
				. '<rect x="85" y="34" width="30" height="3" fill="#000" opacity=".15"/>'
				. '<path d="M82 40h36v12l20 10v106a10 10 0 0 1-10 10H72a10 10 0 0 1-10-10V62l20-10Z" fill="#fbfaf6"/>'
				. '<path d="M118 62h14v26h-14" fill="none" stroke="#e3e1d8" stroke-width="5" stroke-linejoin="round"/>'
				. '<rect x="62" y="92" width="76" height="70" fill="' . $c . '"/>'
				. '<g transform="translate(100 108)">' . str_replace( $c, '#fff', $leaf ) . '</g>'
				. '<text x="100" y="134" text-anchor="middle" ' . $mono . ' font-size="12.5" fill="#fff" direction="ltr">' . $code . '</text>'
				. '<text x="100" y="152" text-anchor="middle" ' . $fa . ' font-weight="700" font-size="10" fill="#fff">' . $name . '</text>'
				. '<path d="M70 66v104" stroke="#000" stroke-opacity=".05" stroke-width="8"/>';
			break;
		default: // pouch.
			$svg = '<path d="M54 30h92l8 142c0 5-3 8-8 8H54c-5 0-8-3-8-8Z" fill="' . $c . '"/>'
				. '<rect x="54" y="30" width="92" height="12" fill="#000" opacity=".16"/>'
				. '<path d="M62 48h8l-4 124h-8Z" fill="#fff" opacity=".14"/>'
				. '<rect x="66" y="66" width="68" height="88" rx="10" fill="#fff"/>'
				. '<g transform="translate(100 80)">' . $leaf . '</g>'
				. '<text x="100" y="99" text-anchor="middle" ' . $fa . ' font-weight="800" font-size="8.5" fill="' . $c . '">کودینو</text>'
				. '<text x="100" y="121" text-anchor="middle" ' . $mono . ' font-size="12.5" fill="#1d2a22" direction="ltr">' . $code . '</text>'
				. '<text x="100" y="141" text-anchor="middle" ' . $fa . ' font-weight="700" font-size="9.5" fill="#5f6b63">' . $name . '</text>';
	}

	return '<svg viewBox="0 0 200 200" role="img" aria-label="' . $name . '" xmlns="http://www.w3.org/2000/svg">'
		. '<ellipse cx="100" cy="182" rx="60" ry="6" fill="#0c2b1a" opacity=".1"/>'
		. $svg . '</svg>';
}

/**
 * Place a pack drawing inside another SVG.
 */
function kudino_pack_at( $x, $y, $size, $args, $class = 'kd-float' ) {
	$svg = kudino_pack_svg( $args[0], $args[1], $args[2], $args[3] );
	return str_replace( '<svg ', '<svg class="' . esc_attr( $class ) . '" x="' . (int) $x . '" y="' . (int) $y . '" width="' . (int) $size . '" height="' . (int) $size . '" ', $svg );
}

/**
 * Golden-hour field scene for the hero (stands in for a photo until one is uploaded).
 */
function kudino_scene_field() {
	$rows = '';
	for ( $i = -11; $i <= 11; $i++ ) {
		$rows .= sprintf( '<line x1="%d" y1="300" x2="%d" y2="470"/>', 330 + $i * 26, 330 + $i * 150 );
	}
	$trees = '';
	foreach ( array( array( 150, 232, 22 ), array( 182, 238, 16 ), array( 560, 214, 24 ), array( 594, 222, 17 ), array( 1020, 226, 20 ), array( 1052, 232, 14 ) ) as $t ) {
		$trees .= sprintf( '<rect x="%d" y="%d" width="4" height="%d" fill="#3d5a2c"/><circle cx="%d" cy="%d" r="%d" fill="#2f6b3a"/><circle cx="%d" cy="%d" r="%d" fill="#3f7f43"/>', $t[0] - 2, $t[1], $t[2], $t[0], $t[1] - 2, $t[2], $t[0] - $t[2] / 3, $t[1] - $t[2] / 2, $t[2] * 0.6 );
	}
	$sprouts = '';
	foreach ( array( array( 70, 452, 1.4 ), array( 150, 460, 1.0 ), array( 230, 450, 1.25 ), array( 310, 462, .9 ), array( 400, 456, 1.1 ) ) as $sp ) {
		$sprouts .= sprintf( '<g transform="translate(%d %d) scale(%s)"><path d="M0 0v-34" stroke="#2f6b3a" stroke-width="3" stroke-linecap="round"/><path d="M0-22c0-12-8-20-22-20 0 12 8 20 22 20Z" fill="#4f9a4a"/><path d="M0-28c0-12 8-20 22-20 0 12-8 20-22 20Z" fill="#6fb35e"/></g>', $sp[0], $sp[1], $sp[2] );
	}
	return '<svg viewBox="0 0 1280 470" preserveAspectRatio="xMinYMax slice" aria-hidden="true" focusable="false">'
		. '<defs><linearGradient id="kdSky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f6dfae"/><stop offset=".55" stop-color="#f3e9cf"/><stop offset="1" stop-color="#dfead0"/></linearGradient>'
		. '<linearGradient id="kdField" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7cb35f"/><stop offset="1" stop-color="#3f8a42"/></linearGradient>'
		. '<clipPath id="kdFieldClip"><path d="M0 300C260 286 640 284 1280 300V470H0Z"/></clipPath></defs>'
		. '<rect width="1280" height="470" fill="url(#kdSky)"/>'
		. '<circle cx="250" cy="150" r="70" fill="#fbe3a6"/><circle cx="250" cy="150" r="46" fill="#f8cf72"/>'
		. '<path d="M0 238C180 196 380 214 560 226S900 188 1280 222V470H0Z" fill="#b8d39b"/>'
		. $trees
		. '<rect x="430" y="232" width="56" height="34" fill="#efe2c4"/><path d="M424 234l34-22 34 22Z" fill="#b5543a"/><rect x="452" y="246" width="12" height="20" fill="#7a4a2f"/>'
		. '<path d="M0 270C240 246 520 262 760 256S1100 238 1280 252V470H0Z" fill="#94c27a"/>'
		. '<path d="M0 300C260 286 640 284 1280 300V470H0Z" fill="url(#kdField)"/>'
		. '<g clip-path="url(#kdFieldClip)" stroke="#2f7a36" stroke-width="7" stroke-linecap="round" opacity=".55">' . $rows . '</g>'
		. '<path d="M0 440C200 428 420 432 640 444S1000 436 1280 446V470H0Z" fill="#6b4a2c"/>'
		. $sprouts
		. '</svg>';
}

/**
 * Banner art: three packs standing on a ground line, with leaves behind.
 */
function kudino_banner_art( $packs, $tint = '#cfe5c7' ) {
	$leaves = '<g fill="#1a6b3c" opacity=".14"><path d="M300 40c0 70-50 120-140 120 0-70 50-120 140-120Z"/><path d="M40 190c0-50 36-86 100-86 0 50-36 86-100 86Z"/></g>';
	$out    = '<svg viewBox="0 0 340 260" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false"><rect width="340" height="260" fill="' . esc_attr( $tint ) . '"/>' . $leaves;
	$spots  = array( array( 20, 76, 150 ), array( 170, 70, 156 ), array( 92, 92, 170 ) );
	foreach ( $packs as $i => $pk ) {
		if ( isset( $spots[ $i ] ) ) {
			$out .= kudino_pack_at( $spots[ $i ][0], $spots[ $i ][1], $spots[ $i ][2], $pk );
		}
	}
	return $out . '</svg>';
}

/**
 * Journal thumbnails (until a post has a featured image).
 */
function kudino_post_art( $i ) {
	switch ( $i % 4 ) {
		case 1:
			return '<svg viewBox="0 0 320 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect width="320" height="200" fill="#f6e7c8"/><circle cx="70" cy="60" r="26" fill="#f8cf72"/><path d="M0 120C80 100 200 110 320 104V200H0Z" fill="#94c27a"/><path d="M0 150C100 138 220 140 320 150V200H0Z" fill="#4f9a4a"/><g stroke="#2f7a36" stroke-width="4" opacity=".5"><line x1="160" y1="150" x2="60" y2="200"/><line x1="170" y1="150" x2="140" y2="200"/><line x1="180" y1="150" x2="220" y2="200"/><line x1="190" y1="150" x2="300" y2="200"/></g></svg>';
		case 2:
			return '<svg viewBox="0 0 320 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect width="320" height="200" fill="#e6f2e3"/><rect x="0" y="150" width="320" height="50" fill="#8a6440"/><circle cx="160" cy="175" r="48" fill="none" stroke="#e8711a" stroke-width="3" stroke-dasharray="6 6"/><rect x="154" y="80" width="12" height="80" fill="#6b4a2c"/><circle cx="160" cy="70" r="46" fill="#2f6b3a"/><circle cx="130" cy="88" r="26" fill="#3f7f43"/><circle cx="190" cy="86" r="28" fill="#3f7f43"/><circle cx="148" cy="60" r="5" fill="#e8711a"/><circle cx="182" cy="74" r="5" fill="#e8711a"/></svg>';
		case 3:
			return '<svg viewBox="0 0 320 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect width="320" height="200" fill="#fdeedd"/>' . kudino_pack_at( 40, 20, 160, array( 'jug', '#4b6a82', 'Fe 6%', 'کلات آهن' ), '' ) . kudino_pack_at( 140, 30, 150, array( 'pouch', '#a87720', '10-52-10', 'گلدهی' ), '' ) . '</svg>';
		default:
			return '<svg viewBox="0 0 320 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect width="320" height="200" fill="#13432a"/><g fill="#1a6b3c"><path d="M290 10c0 60-40 100-110 100 0-60 40-100 110-100Z"/></g><text x="160" y="118" text-anchor="middle" font-family="IBM Plex Mono, monospace" font-size="46" font-weight="500" fill="#fff" direction="ltr">10-<tspan fill="#e8711a">52</tspan>-10</text><g transform="translate(64 150)"><rect width="60" height="6" rx="3" fill="#3c7a57"/><rect x="66" width="120" height="6" rx="3" fill="#a87720"/><rect x="192" width="60" height="6" rx="3" fill="#9b4630"/></g></svg>';
	}
}

/* ------------------------------------------------------------------ analysis */

function kudino_nutrient_color( $code ) {
	$map = array(
		'N' => 'var(--kd-n)',
		'P' => 'var(--kd-p)',
		'K' => 'var(--kd-k)',
		'O' => 'var(--kd-o)',
	);
	return isset( $map[ $code ] ) ? $map[ $code ] : 'var(--kd-m)';
}

function kudino_npk_bar( $npk ) {
	$cols = array( 'var(--kd-n)', 'var(--kd-p)', 'var(--kd-k)' );
	$out  = '<span class="kd-npk__bar" aria-hidden="true">';
	foreach ( array_values( $npk ) as $i => $v ) {
		$out .= (int) $v > 0
			? '<i style="--v:' . (int) $v . ';--c:' . $cols[ $i ] . '"></i>'
			: '<i data-zero style="--c:' . $cols[ $i ] . '"></i>';
	}
	return $out . '</span>';
}

function kudino_npk( $npk ) {
	if ( empty( $npk ) || count( $npk ) !== 3 ) {
		return '';
	}
	$npk   = array_values( $npk );
	$label = sprintf( 'نیتروژن %s٪، فسفر %s٪، پتاسیم %s٪', kudino_fa( $npk[0] ), kudino_fa( $npk[1] ), kudino_fa( $npk[2] ) );
	return '<div class="kd-npk" role="img" aria-label="' . esc_attr( $label ) . '"><span class="kd-npk__code">' . esc_html( implode( '-', $npk ) ) . '</span>' . kudino_npk_bar( $npk ) . '</div>';
}

/* ------------------------------------------------------------------ product card */

function kudino_product_card( $p ) {
	$p = wp_parse_args(
		$p,
		array(
			'group'     => '',
			'title'     => '',
			'url'       => '#',
			'cat'       => '',
			'weight'    => '',
			'npk'       => array(),
			'spec'      => '',
			'price'     => 0,
			'old_price' => 0,
			'rating'    => '',
			'pack'      => array( 'pouch', '#1a6b3c', '', '' ),
			'badge'     => '',
			'stock'     => 'in',
			'stock_qty' => 0,
			'image'     => '',
			'add_url'   => '#',
			'add_attrs' => '',
		)
	);

	$discount = ( $p['old_price'] > $p['price'] && $p['price'] > 0 ) ? round( ( 1 - $p['price'] / $p['old_price'] ) * 100 ) : 0;
	ob_start();
	?>
	<article class="kd-card" data-group="<?php echo esc_attr( $p['group'] ); ?>">
		<a class="kd-card__media" href="<?php echo esc_url( $p['url'] ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo $p['image'] ? $p['image'] : kudino_pack_svg( $p['pack'][0], $p['pack'][1], $p['pack'][2], $p['pack'][3] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $p['badge'] ) : ?>
				<span class="kd-card__tags"><span class="kd-tag"><?php echo esc_html( $p['badge'] ); ?></span></span>
			<?php endif; ?>
		</a>
		<button class="kd-card__fav" type="button" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( 'افزودن «%s» به علاقه‌مندی‌ها', $p['title'] ) ); ?>"><?php echo kudino_icon( 'heart', 17 ); // phpcs:ignore ?></button>
		<div class="kd-card__meta">
			<span><?php echo esc_html( $p['cat'] ); ?><?php echo $p['weight'] ? ' · ' . esc_html( $p['weight'] ) : ''; ?></span>
			<?php if ( $p['rating'] ) : ?>
				<span class="kd-card__rate" aria-label="<?php echo esc_attr( 'امتیاز ' . kudino_fa( $p['rating'] ) . ' از ۵' ); ?>"><?php echo kudino_icon( 'star', 13, 'is-filled' ); // phpcs:ignore ?><?php echo esc_html( kudino_fa( $p['rating'] ) ); ?></span>
			<?php endif; ?>
		</div>
		<h3 class="kd-card__title"><a href="<?php echo esc_url( $p['url'] ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
		<?php if ( $p['npk'] ) : ?>
			<?php echo kudino_npk( $p['npk'] ); // phpcs:ignore ?>
		<?php elseif ( $p['spec'] ) : ?>
			<div class="kd-card__spec"><?php echo esc_html( $p['spec'] ); ?></div>
		<?php endif; ?>
		<div class="kd-card__foot">
			<div class="kd-price">
				<?php if ( 'low' === $p['stock'] ) : ?>
					<span class="kd-stock kd-stock--low"><?php echo esc_html( $p['stock_qty'] ? 'فقط ' . kudino_fa( $p['stock_qty'] ) . ' عدد مانده' : 'رو به اتمام' ); ?></span>
				<?php elseif ( 'out' === $p['stock'] ) : ?>
					<span class="kd-stock kd-stock--out">ناموجود</span>
				<?php endif; ?>
				<?php if ( $discount ) : ?>
					<span class="kd-price__was"><span class="kd-off"><?php echo esc_html( kudino_fa( $discount ) ); ?>٪</span><del><?php echo esc_html( kudino_price( $p['old_price'] ) ); ?></del></span>
				<?php endif; ?>
				<b><?php echo esc_html( kudino_price( $p['price'] ) ); ?><small>تومان</small></b>
			</div>
			<a class="kd-add" href="<?php echo esc_url( $p['add_url'] ); ?>" <?php echo $p['add_attrs']; // phpcs:ignore ?> aria-label="<?php echo esc_attr( sprintf( 'افزودن «%s» به سبد خرید', $p['title'] ) ); ?>"><?php echo kudino_icon( 'bag', 19 ); // phpcs:ignore ?></a>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/* ------------------------------------------------------------------ navigation helpers */

function kudino_breadcrumb() {
	$sep = '<span aria-hidden="true">/</span>';
	echo '<nav class="kd-crumbs" aria-label="مسیر"><a href="' . esc_url( home_url( '/' ) ) . '">کودینو</a>' . $sep; // phpcs:ignore
	if ( is_singular( 'post' ) ) {
		$cat = get_the_category();
		echo '<a href="' . esc_url( kudino_blog_url() ) . '">مجله کودینو</a>' . $sep; // phpcs:ignore
		if ( $cat ) {
			echo '<a href="' . esc_url( get_category_link( $cat[0] ) ) . '">' . esc_html( $cat[0]->name ) . '</a>' . $sep; // phpcs:ignore
		}
		echo '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_page() ) {
		echo '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_archive() ) {
		echo '<span>' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</span>';
	} elseif ( is_search() ) {
		echo '<span>جستجو</span>';
	} else {
		echo '<span>مجله کودینو</span>';
	}
	echo '</nav>';
}

function kudino_menu_fallback() {
	echo '<ul>';
	foreach ( array_slice( kudino_categories(), 0, 4 ) as $c ) {
		echo '<li><a href="' . esc_url( $c['url'] ) . '">' . esc_html( $c['name'] ) . '</a></li>';
	}
	echo '<li><a href="' . esc_url( home_url( '/#kd-almanac' ) ) . '">تقویم کوددهی</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/#kd-offer' ) ) . '">پیشنهاد ویژه</a></li>';
	echo '<li><a href="' . esc_url( kudino_blog_url() ) . '">مجله کودینو</a></li>';
	echo '</ul>';
}

function kudino_blog_url() {
	$id = (int) get_option( 'page_for_posts' );
	return $id ? get_permalink( $id ) : home_url( '/?post_type=post' );
}

function kudino_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/#kd-shelf' );
}

function kudino_cart_count() {
	return ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
}

function kudino_cart_count_html() {
	$n = kudino_cart_count();
	return '<span class="kd-tool__count kd-cart-count' . ( $n ? '' : ' is-zero' ) . '">' . esc_html( kudino_fa( $n ) ) . '</span>';
}

function kudino_cart_url() {
	return function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
}

function kudino_account_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
}

function kudino_search_url( $q ) {
	$args = array( 's' => $q );
	if ( class_exists( 'WooCommerce' ) ) {
		$args['post_type'] = 'product';
	}
	return add_query_arg( $args, home_url( '/' ) );
}
