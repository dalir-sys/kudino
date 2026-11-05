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
 * Mark: three bars — an N·P·K analysis drawn as a bar chart.
 */
function kudino_logo_mark( $class = 'kd-logo__mark' ) {
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 26 26" aria-hidden="true" focusable="false"><rect x="1" y="1" width="6" height="24" fill="#3c7a57"/><rect x="10" y="10" width="6" height="15" fill="#a87720"/><rect x="19" y="5" width="6" height="20" fill="#9b4630"/></svg>';
}

function kudino_logo( $inverse = false ) {
	if ( ! $inverse && has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf(
		'<a class="kd-logo%1$s" href="%2$s" rel="home">%3$s<span class="kd-logo__name">%4$s</span></a>',
		$inverse ? ' kd-logo--inverse' : '',
		esc_url( home_url( '/' ) ),
		kudino_logo_mark(), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'کودینو' )
	);
}

/* ------------------------------------------------------------------ illustrations */

/**
 * Flat, print-like packaging drawing used until a product has a photo.
 *
 * @param string $type  sack | pouch | jug.
 * @param string $color Print colour (category / nutrient colour).
 * @param string $code  Analysis or short code printed large (Latin, e.g. "20-20-20").
 * @param string $name  Short Persian name printed under the code.
 */
function kudino_pack_svg( $type = 'pouch', $color = '#3c7a57', $code = '', $name = '' ) {
	$c    = sanitize_hex_color( $color ) ? $color : '#3c7a57';
	$code = esc_html( $code );
	$name = esc_html( $name );
	$ink  = '#171814';
	$mono = 'font-family="IBM Plex Mono, monospace" font-weight="500"';
	$ui   = 'font-family="IBM Plex Sans Arabic, sans-serif" font-weight="600"';

	switch ( $type ) {
		case 'sack': // woven polypropylene sack.
			$weave = '';
			for ( $y = 52; $y < 168; $y += 6 ) {
				$weave .= '<line x1="44" y1="' . $y . '" x2="156" y2="' . $y . '"/>';
			}
			$svg = '<path d="M44 40h112l4 136H40Z" fill="#f4f2ea" stroke="' . $ink . '" stroke-width="1.5"/>'
				. '<g stroke="' . $ink . '" stroke-opacity=".06">' . $weave . '</g>'
				. '<path d="M44 47h112M42 169h116" stroke="' . $ink . '" stroke-width="1" stroke-dasharray="3 3"/>'
				. '<rect x="43" y="74" width="114" height="44" fill="' . $c . '"/>'
				. '<text x="100" y="103" text-anchor="middle" ' . $mono . ' font-size="18" fill="#fff" direction="ltr">' . $code . '</text>'
				. '<text x="100" y="140" text-anchor="middle" ' . $ui . ' font-size="12" fill="' . $ink . '">' . $name . '</text>'
				. '<rect x="88" y="152" width="24" height="3" fill="' . $ink . '"/>';
			break;
		case 'jug': // HDPE jug with handle.
			$svg = '<rect x="86" y="30" width="28" height="14" fill="' . $c . '" stroke="' . $ink . '" stroke-width="1.5"/>'
				. '<path d="M84 44h32v10l18 8v110a6 6 0 0 1-6 6H72a6 6 0 0 1-6-6V62l18-8Z" fill="#f7f6f1" stroke="' . $ink . '" stroke-width="1.5"/>'
				. '<path d="M116 62h12v22h-12" fill="none" stroke="' . $ink . '" stroke-width="1.5"/>'
				. '<rect x="66" y="96" width="68" height="62" fill="#fff" stroke="' . $ink . '" stroke-width="1"/>'
				. '<rect x="66" y="96" width="68" height="8" fill="' . $c . '"/>'
				. '<text x="100" y="128" text-anchor="middle" ' . $mono . ' font-size="12" fill="' . $ink . '" direction="ltr">' . $code . '</text>'
				. '<text x="100" y="147" text-anchor="middle" ' . $ui . ' font-size="10" fill="' . $ink . '">' . $name . '</text>';
			break;
		default: // kraft stand-up pouch.
			$svg = '<path d="M56 36h88l6 140H50Z" fill="#d8c6a3" stroke="' . $ink . '" stroke-width="1.5"/>'
				. '<path d="M56 48h88" stroke="' . $ink . '" stroke-width="1" stroke-dasharray="2 3"/>'
				. '<rect x="64" y="74" width="72" height="78" fill="#fbfbf8" stroke="' . $ink . '" stroke-width="1"/>'
				. '<rect x="64" y="74" width="72" height="10" fill="' . $c . '"/>'
				. '<text x="100" y="114" text-anchor="middle" ' . $mono . ' font-size="12.5" fill="' . $ink . '" direction="ltr">' . $code . '</text>'
				. '<line x1="76" y1="124" x2="124" y2="124" stroke="' . $ink . '" stroke-opacity=".2"/>'
				. '<text x="100" y="140" text-anchor="middle" ' . $ui . ' font-size="10" fill="' . $ink . '">' . $name . '</text>';
	}

	return '<svg viewBox="0 0 200 200" role="img" aria-label="' . $name . '" xmlns="http://www.w3.org/2000/svg">'
		. '<rect x="34" y="180" width="132" height="2" fill="' . $ink . '" opacity=".12"/>'
		. $svg . '</svg>';
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

/**
 * Proportional N/P/K bar.
 */
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

/**
 * Compact analysis: code + bar. Used on shelf items.
 */
function kudino_npk( $npk ) {
	if ( empty( $npk ) || count( $npk ) !== 3 ) {
		return '';
	}
	$npk   = array_values( $npk );
	$label = sprintf( 'نیتروژن %s٪، فسفر %s٪، پتاسیم %s٪', kudino_fa( $npk[0] ), kudino_fa( $npk[1] ), kudino_fa( $npk[2] ) );
	return '<div class="kd-npk" role="img" aria-label="' . esc_attr( $label ) . '"><span class="kd-npk__code">' . esc_html( implode( '-', $npk ) ) . '</span>' . kudino_npk_bar( $npk ) . '</div>';
}

/* ------------------------------------------------------------------ shelf item */

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
			'pack'      => array( 'pouch', '#3c7a57', '', '' ),
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
	<article class="kd-item" data-group="<?php echo esc_attr( $p['group'] ); ?>">
		<a class="kd-item__media" href="<?php echo esc_url( $p['url'] ); ?>" tabindex="-1" aria-hidden="true">
			<?php
			echo $p['image'] ? $p['image'] : kudino_pack_svg( $p['pack'][0], $p['pack'][1], $p['pack'][2], $p['pack'][3] ); // phpcs:ignore WordPress.Security.EscapeOutput
			?>
			<?php if ( $discount || $p['badge'] ) : ?>
				<span class="kd-item__tags">
					<?php if ( $discount ) : ?>
						<span class="kd-tag kd-tag--ink"><?php echo esc_html( kudino_fa( $discount ) ); ?>٪ تخفیف</span>
					<?php endif; ?>
					<?php if ( $p['badge'] ) : ?>
						<span class="kd-tag"><?php echo esc_html( $p['badge'] ); ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>
		</a>
		<button class="kd-item__fav" type="button" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( 'نشان کردن «%s»', $p['title'] ) ); ?>"><?php echo kudino_icon( 'heart', 18 ); // phpcs:ignore ?></button>
		<div class="kd-item__head">
			<span><?php echo esc_html( $p['cat'] ); ?><?php echo $p['weight'] ? ' · ' . esc_html( $p['weight'] ) : ''; ?></span>
			<?php if ( $p['rating'] ) : ?>
				<span class="kd-item__rate" aria-label="<?php echo esc_attr( 'امتیاز ' . kudino_fa( $p['rating'] ) . ' از ۵' ); ?>">★ <?php echo esc_html( kudino_fa( $p['rating'] ) ); ?></span>
			<?php endif; ?>
		</div>
		<h3 class="kd-item__title"><a href="<?php echo esc_url( $p['url'] ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
		<?php if ( $p['npk'] ) : ?>
			<?php echo kudino_npk( $p['npk'] ); // phpcs:ignore ?>
		<?php elseif ( $p['spec'] ) : ?>
			<div class="kd-item__spec"><?php echo esc_html( $p['spec'] ); ?></div>
		<?php endif; ?>
		<div class="kd-item__foot">
			<div class="kd-price">
				<?php if ( 'low' === $p['stock'] ) : ?>
					<span class="kd-stock kd-stock--low"><?php echo esc_html( $p['stock_qty'] ? 'فقط ' . kudino_fa( $p['stock_qty'] ) . ' عدد مانده' : 'رو به اتمام' ); ?></span>
				<?php elseif ( 'out' === $p['stock'] ) : ?>
					<span class="kd-stock kd-stock--out">ناموجود</span>
				<?php endif; ?>
				<?php if ( $discount ) : ?>
					<del><?php echo esc_html( kudino_price( $p['old_price'] ) ); ?></del>
				<?php endif; ?>
				<b><?php echo esc_html( kudino_price( $p['price'] ) ); ?><small>تومان</small></b>
			</div>
			<a class="kd-add" href="<?php echo esc_url( $p['add_url'] ); ?>" <?php echo $p['add_attrs']; // phpcs:ignore ?> aria-label="<?php echo esc_attr( sprintf( 'افزودن «%s» به سبد', $p['title'] ) ); ?>"><?php echo kudino_icon( 'plus', 18 ); // phpcs:ignore ?></a>
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
		echo '<a href="' . esc_url( kudino_blog_url() ) . '">دفترچه کشت</a>' . $sep; // phpcs:ignore
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
		echo '<span>دفترچه کشت</span>';
	}
	echo '</nav>';
}

function kudino_menu_fallback() {
	echo '<ul>';
	foreach ( array_slice( kudino_categories(), 0, 5 ) as $c ) {
		echo '<li><a href="' . esc_url( $c['url'] ) . '">' . esc_html( $c['short'] ) . '</a></li>';
	}
	echo '<li><a href="' . esc_url( home_url( '/#kd-almanac' ) ) . '">تقویم کوددهی</a></li>';
	echo '<li><a href="' . esc_url( kudino_blog_url() ) . '">دفترچه کشت</a></li>';
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
