<?php
/**
 * Homepage.
 *
 * Iranian agri-store layout: slider → category circles → weekly offer →
 * split banners → best sellers → fertilizing almanac → deficiency table →
 * brands → consult → reviews → journal.
 *
 * @package Kudino
 */

get_header();

$kd_today    = kudino_today();
$kd_months   = kudino_months();
$kd_products = function_exists( 'kudino_home_products' ) ? kudino_home_products( 10 ) : kudino_demo_products();
$kd_offers   = kudino_offer_products( $kd_products );
$kd_guides   = kudino_crop_guides();
$kd_cats     = kudino_categories();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only view switch.
$kd_crop     = isset( $_GET['crop'] ) ? sanitize_key( wp_unslash( $_GET['crop'] ) ) : '';
$kd_crop     = isset( $kd_guides[ $kd_crop ] ) ? $kd_crop : array_key_first( $kd_guides );
$kd_feature  = kudino_featured_label( $kd_products );
$kd_end      = current_datetime()->setTime( 23, 59, 59 )->modify( '+' . max( 0, (int) kudino_mod( 'offer_days' ) - 1 ) . ' days' );
$kd_left     = max( 0, $kd_end->getTimestamp() - time() );
$kd_count    = array(
	array( intdiv( $kd_left, 86400 ), 'روز' ),
	array( intdiv( $kd_left % 86400, 3600 ), 'ساعت' ),
	array( intdiv( $kd_left % 3600, 60 ), 'دقیقه' ),
	array( $kd_left % 60, 'ثانیه' ),
);
$kd_countdown = function () use ( $kd_count, $kd_end ) {
	echo '<span class="kd-count" data-kd-countdown="' . esc_attr( $kd_end->format( DATE_ATOM ) ) . '" role="timer" aria-label="زمان باقی‌مانده پیشنهاد">';
	foreach ( $kd_count as $c ) {
		echo '<span><b>' . esc_html( kudino_fa( str_pad( (string) $c[0], 2, '0', STR_PAD_LEFT ) ) ) . '</b><small>' . esc_html( $c[1] ) . '</small></span>';
	}
	echo '</span>';
};
?>

<!-- ======================================================= Slider -->
<section class="kd-hero">
	<div class="kd-wrap">
		<div class="kd-slider" data-kd-slider aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'معرفی و پیشنهادها', 'kudino' ); ?>">
			<div class="kd-slider__track">

				<div class="kd-slide" role="group" aria-roledescription="slide" aria-label="۱ از ۳">
					<div class="kd-slide__art"><?php echo kudino_mod_image( 'img_hero', 'full', array( 'loading' => 'eager' ) ) ? kudino_mod_image( 'img_hero', 'full', array( 'loading' => 'eager' ) ) : kudino_scene_field(); // phpcs:ignore ?></div>
					<div class="kd-slide__shade"></div>
					<div class="kd-slide__body">
						<span class="kd-slide__kicker"><?php echo kudino_icon( 'sprout', 16 ); // phpcs:ignore ?><?php echo esc_html( kudino_mod( 'hero_kicker' ) ); ?></span>
						<h1 class="kd-slide__title"><?php echo wp_kses( kudino_mod( 'hero_title' ), array( 'em' => array(), 'br' => array() ) ); ?></h1>
						<p class="kd-slide__text"><?php echo esc_html( kudino_mod( 'hero_text' ) ); ?></p>
						<div class="kd-slide__cta">
							<a class="kd-btn kd-btn--cta kd-btn--lg" href="<?php echo esc_url( kudino_shop_url() ); ?>"><?php esc_html_e( 'خرید کود', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 18, 'kd-icon--lead' ); // phpcs:ignore ?></a>
							<a class="kd-btn kd-btn--glass kd-btn--lg" href="#kd-almanac"><?php echo kudino_icon( 'calendar', 18 ); // phpcs:ignore ?><?php esc_html_e( 'تقویم کوددهی محصولم', 'kudino' ); ?></a>
						</div>
					</div>
				</div>

				<div class="kd-slide" role="group" aria-roledescription="slide" aria-label="۲ از ۳">
					<div class="kd-slide__art">
						<svg viewBox="0 0 1280 440" preserveAspectRatio="xMinYMid slice" aria-hidden="true">
							<rect width="1280" height="440" fill="#13432a"/>
							<circle cx="300" cy="230" r="230" fill="#1a6b3c"/>
							<circle cx="300" cy="230" r="160" fill="#22834a" opacity=".5"/>
							<path d="M600 0c0 150-110 250-300 250 0-150 110-250 300-250Z" fill="#1a6b3c" opacity=".5"/>
							<?php echo kudino_pack_at( 120, 40, 380, array( 'pouch', '#1a6b3c', '20-20-20', 'کود کامل' ), '' ); // phpcs:ignore ?>
							<?php echo kudino_pack_at( 40, 210, 200, array( 'pouch', '#c2500a', '10-52-10', 'گلدهی' ), '' ); // phpcs:ignore ?>
							<circle cx="470" cy="110" r="56" fill="#e8711a"/>
							<text x="470" y="122" text-anchor="middle" font-family="Estedad, sans-serif" font-weight="900" font-size="34" fill="#1d2a22"><?php echo esc_html( kudino_fa( round( ( 1 - $kd_feature['price'] / max( 1, $kd_feature['old_price'] ) ) * 100 ) ) . '٪' ); ?></text>
						</svg>
					</div>
					<div class="kd-slide__shade" style="background:linear-gradient(to left, rgba(12,43,26,.85), rgba(12,43,26,0) 60%)"></div>
					<div class="kd-slide__body">
						<span class="kd-slide__kicker"><?php echo kudino_icon( 'percent', 16 ); // phpcs:ignore ?><?php esc_html_e( 'پیشنهاد پاییزه — تعداد محدود', 'kudino' ); ?></span>
						<h2 class="kd-slide__title"><?php esc_html_e( 'کود کامل', 'kudino' ); ?> <em class="kd-mono" style="font-family:var(--kd-mono)">20-20-20</em></h2>
						<p class="kd-slide__text"><?php esc_html_e( 'پودری، ۱۰۰٪ محلول در آب؛ برای کودآبیاری و محلول‌پاشی باغ و صیفی.', 'kudino' ); ?></p>
						<div class="kd-slide__price">
							<del><?php echo esc_html( kudino_price( $kd_feature['old_price'] ) ); ?></del>
							<b><?php echo esc_html( kudino_price( $kd_feature['price'] ) ); ?><small><?php esc_html_e( 'تومان', 'kudino' ); ?></small></b>
						</div>
						<div class="kd-slide__timer"><?php $kd_countdown(); ?></div>
						<div class="kd-slide__cta">
							<a class="kd-btn kd-btn--cta" href="<?php echo esc_url( $kd_feature['add_url'] ); ?>"><?php echo kudino_icon( 'bag', 18 ); // phpcs:ignore ?><?php esc_html_e( 'افزودن به سبد', 'kudino' ); ?></a>
						</div>
					</div>
				</div>

				<div class="kd-slide" role="group" aria-roledescription="slide" aria-label="۳ از ۳">
					<div class="kd-slide__art">
						<svg viewBox="0 0 1280 440" preserveAspectRatio="xMinYMid slice" aria-hidden="true">
							<rect width="1280" height="440" fill="#1a6b3c"/>
							<path d="M0 340C240 300 420 330 640 320V440H0Z" fill="#13432a"/>
							<g transform="translate(110 60) rotate(-4)">
								<rect width="380" height="300" rx="20" fill="#fff"/>
								<rect width="380" height="64" rx="20" fill="#e8711a"/><rect y="40" width="380" height="24" fill="#e8711a"/>
								<circle cx="90" cy="18" r="8" fill="#13432a"/><circle cx="290" cy="18" r="8" fill="#13432a"/>
								<?php
								$kd_colors = array( '#3c7a57', '', '#a87720', '', '#9b4630', '#4b6a82', '', '#6a5741', '#3c7a57', '', '#9b4630', '' );
								for ( $i = 0; $i < 12; $i++ ) {
									$x = 24 + ( $i % 4 ) * 86;
									$y = 84 + intdiv( $i, 4 ) * 70;
									echo '<rect x="' . (int) $x . '" y="' . (int) $y . '" width="74" height="58" rx="10" fill="#f3f8f1"/>';
									if ( $kd_colors[ $i ] ) {
										echo '<rect x="' . (int) ( $x + 22 ) . '" y="' . (int) ( $y + 17 ) . '" width="30" height="24" rx="6" fill="' . esc_attr( $kd_colors[ $i ] ) . '"/>';
									}
								}
								?>
							</g>
							<?php echo kudino_pack_at( 400, 150, 220, array( 'sack', '#9b4630', 'K2O 50%', 'سولفات پتاسیم' ), '' ); // phpcs:ignore ?>
						</svg>
					</div>
					<div class="kd-slide__shade" style="background:linear-gradient(to left, rgba(12,43,26,.8), rgba(12,43,26,0) 60%)"></div>
					<div class="kd-slide__body">
						<span class="kd-slide__kicker"><?php echo kudino_icon( 'calendar', 16 ); // phpcs:ignore ?><?php esc_html_e( 'رایگان و بدون ثبت‌نام', 'kudino' ); ?></span>
						<h2 class="kd-slide__title"><?php esc_html_e( 'برنامه ۱۲ ماهه کوددهی', 'kudino' ); ?> <em><?php esc_html_e( 'محصولت را ببین', 'kudino' ); ?></em></h2>
						<p class="kd-slide__text"><?php esc_html_e( 'باغ و مرکبات، پسته، گندم، برنج، صیفی یا گلدان؛ بدان هر ماه چه کودی، چقدر و برای چه مرحله‌ای.', 'kudino' ); ?></p>
						<div class="kd-slide__cta">
							<a class="kd-btn kd-btn--cta kd-btn--lg" href="#kd-almanac"><?php esc_html_e( 'دیدن تقویم کوددهی', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 18, 'kd-icon--lead' ); // phpcs:ignore ?></a>
						</div>
					</div>
				</div>

			</div>
			<button class="kd-slider__arrow kd-slider__arrow--prev" type="button" data-kd-prev aria-label="<?php esc_attr_e( 'اسلاید قبلی', 'kudino' ); ?>"><?php echo kudino_icon( 'chevron-right', 22 ); // phpcs:ignore ?></button>
			<button class="kd-slider__arrow kd-slider__arrow--next" type="button" data-kd-next aria-label="<?php esc_attr_e( 'اسلاید بعدی', 'kudino' ); ?>"><?php echo kudino_icon( 'chevron-left', 22 ); // phpcs:ignore ?></button>
			<div class="kd-slider__ui"><div class="kd-slider__dots" data-kd-dots></div></div>
		</div>
	</div>
</section>

<!-- ======================================================= Category circles -->
<section class="kd-band" id="kd-cats">
	<div class="kd-wrap">
		<div class="kd-cats">
			<?php foreach ( $kd_cats as $c ) : ?>
				<a class="kd-cat" href="<?php echo esc_url( $c['url'] ); ?>">
					<span class="kd-cat__icon"><?php echo kudino_icon( $c['icon'], 38 ); // phpcs:ignore ?></span>
					<span class="kd-cat__name"><?php echo esc_html( $c['name'] ); ?></span>
					<span class="kd-cat__count"><?php echo esc_html( kudino_fa( $c['count'] ) . ' کالا' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ======================================================= Weekly offer -->
<section class="kd-band" id="kd-offer">
	<div class="kd-wrap">
		<div class="kd-offer">
			<div class="kd-offer__intro">
				<h2 class="kd-offer__title"><?php esc_html_e( 'پیشنهاد ویژه', 'kudino' ); ?><em><?php esc_html_e( 'این هفته', 'kudino' ); ?></em></h2>
				<?php $kd_countdown(); ?>
				<p><?php esc_html_e( 'تخفیف تا پایان زمان یا تمام شدن موجودی', 'kudino' ); ?></p>
				<a class="kd-btn kd-btn--white kd-btn--sm" href="<?php echo esc_url( kudino_shop_url() ); ?>"><?php esc_html_e( 'مشاهده همه', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a>
			</div>
			<div class="kd-rail" data-kd-rail>
				<?php
				foreach ( $kd_offers as $p ) {
					echo kudino_product_card( $p ); // phpcs:ignore
				}
				?>
			</div>
		</div>
	</div>
</section>

<!-- ======================================================= Split banners -->
<section class="kd-band">
	<div class="kd-wrap kd-banners">
		<a class="kd-banner" href="<?php echo esc_url( $kd_cats[0]['url'] ); ?>">
			<div class="kd-banner__body">
				<span class="kd-banner__kicker"><?php esc_html_e( 'اثر سریع، دوز دقیق', 'kudino' ); ?></span>
				<h2 class="kd-banner__title"><?php esc_html_e( 'کودهای شیمیایی و معدنی', 'kudino' ); ?></h2>
				<p class="kd-banner__text"><?php esc_html_e( 'NPK، اوره، سولفات پتاسیم و فسفات‌ها با آنالیز تضمین‌شده.', 'kudino' ); ?></p>
				<span class="kd-btn kd-btn--cta kd-btn--sm"><?php esc_html_e( 'خرید', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16, 'kd-icon--lead' ); // phpcs:ignore ?></span>
			</div>
			<div class="kd-banner__art">
				<?php
				$img = kudino_mod_image( 'img_chemical', 'large' );
				echo $img ? $img : kudino_banner_art( array( array( 'pouch', '#c2500a', '10-52-10', 'گلدهی' ), array( 'sack', '#9b4630', 'K2O 50%', 'سولفات پتاسیم' ), array( 'pouch', '#1a6b3c', '20-20-20', 'کود کامل' ) ) ); // phpcs:ignore
				?>
			</div>
		</a>
		<a class="kd-banner kd-banner--soil" href="<?php echo esc_url( $kd_cats[1]['url'] ); ?>">
			<div class="kd-banner__body">
				<span class="kd-banner__kicker"><?php esc_html_e( 'خاک زنده، محصول سالم', 'kudino' ); ?></span>
				<h2 class="kd-banner__title"><?php esc_html_e( 'کودهای آلی و ارگانیک', 'kudino' ); ?></h2>
				<p class="kd-banner__text"><?php esc_html_e( 'ورمی‌کمپوست، کود مرغی پلت‌شده و هیومیک برای بافت بهتر خاک.', 'kudino' ); ?></p>
				<span class="kd-btn kd-btn--cta kd-btn--sm"><?php esc_html_e( 'خرید', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16, 'kd-icon--lead' ); // phpcs:ignore ?></span>
			</div>
			<div class="kd-banner__art">
				<?php
				$img = kudino_mod_image( 'img_organic', 'large' );
				echo $img ? $img : kudino_banner_art( array( array( 'pouch', '#3b2f25', 'HUMIC 85', 'هیومیک' ), array( 'sack', '#7a5a35', 'VERMI', 'ورمی‌کمپوست' ), array( 'pouch', '#13432a', 'BIO-P', 'فسفات بارور' ) ), '#ead8bf' ); // phpcs:ignore
				?>
			</div>
		</a>
	</div>
</section>

<!-- ======================================================= Best sellers -->
<section class="kd-band" id="kd-shelf">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-head__title"><?php esc_html_e( 'پرفروش‌ترین کودها', 'kudino' ); ?></h2>
			<div class="kd-head__tools">
				<a class="kd-more" href="<?php echo esc_url( kudino_shop_url() ); ?>"><?php esc_html_e( 'مشاهده همه', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a>
				<button class="kd-arrow" type="button" data-kd-rail-prev="#kd-shelf-rail" aria-label="<?php esc_attr_e( 'قبلی', 'kudino' ); ?>"><?php echo kudino_icon( 'chevron-right', 18 ); // phpcs:ignore ?></button>
				<button class="kd-arrow" type="button" data-kd-rail-next="#kd-shelf-rail" aria-label="<?php esc_attr_e( 'بعدی', 'kudino' ); ?>"><?php echo kudino_icon( 'chevron-left', 18 ); // phpcs:ignore ?></button>
			</div>
		</header>
		<div class="kd-chips" role="tablist" aria-label="<?php esc_attr_e( 'فیلتر نوع کود', 'kudino' ); ?>" data-kd-filter="#kd-shelf-rail" style="margin-block-end:16px">
			<?php
			$kd_groups = array(
				''         => 'همه',
				'chemical' => 'شیمیایی',
				'organic'  => 'آلی و زیستی',
				'liquid'   => 'مایع و محلول‌پاشی',
			);
			foreach ( $kd_groups as $key => $label ) :
				$n = '' === $key ? count( $kd_products ) : count( wp_list_filter( $kd_products, array( 'group' => $key ) ) );
				?>
				<button class="kd-chip" type="button" role="tab" aria-selected="<?php echo '' === $key ? 'true' : 'false'; ?>" data-group="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?> <sup><?php echo esc_html( kudino_fa( $n ) ); ?></sup></button>
			<?php endforeach; ?>
		</div>
		<div class="kd-rail" id="kd-shelf-rail" data-kd-rail aria-live="polite">
			<?php
			foreach ( $kd_products as $p ) {
				echo kudino_product_card( $p ); // phpcs:ignore
			}
			?>
		</div>
	</div>
</section>

<!-- ======================================================= Almanac -->
<section class="kd-band" id="kd-almanac">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-head__title"><?php esc_html_e( 'تقویم کوددهی محصولات', 'kudino' ); ?> <small><?php echo esc_html( 'امروز ' . $kd_today['label'] ); ?></small></h2>
		</header>
		<div class="kd-almanac">
			<div class="kd-almanac__top">
				<p style="font-size:var(--kd-t-sm);color:var(--kd-ink-2)"><?php esc_html_e( 'محصولت را از ستون راست انتخاب کن؛ ستون نارنجی ماه جاری است.', 'kudino' ); ?></p>
				<div class="kd-almanac__legend" aria-hidden="true">
					<span><span class="kd-mark" style="--c:var(--kd-n)">N</span> نیتروژن</span>
					<span><span class="kd-mark" style="--c:var(--kd-p)">P</span> فسفر</span>
					<span><span class="kd-mark" style="--c:var(--kd-k)">K</span> پتاسیم</span>
					<span><span class="kd-mark" style="--c:var(--kd-o)">O</span> کود آلی</span>
					<span><span class="kd-mark" style="--c:var(--kd-m)">Fe</span> ریزمغذی</span>
				</div>
			</div>
			<div class="kd-almanac__scroll">
				<table class="kd-cal">
					<caption class="kd-sr"><?php esc_html_e( 'تقویم کوددهی محصولات در ماه‌های سال', 'kudino' ); ?></caption>
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'محصول', 'kudino' ); ?></th>
							<?php foreach ( $kd_months as $mi => $mn ) : ?>
								<th scope="col" class="<?php echo ( $mi + 1 ) === $kd_today['m'] ? 'is-now' : ''; ?>"><?php echo esc_html( $mn ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $kd_guides as $key => $g ) : ?>
							<tr class="<?php echo $key === $kd_crop ? 'is-active' : ''; ?>" data-crop="<?php echo esc_attr( $key ); ?>">
								<th scope="row"><button class="kd-cal__crop" type="button" aria-controls="kd-plan-<?php echo esc_attr( $key ); ?>" aria-expanded="<?php echo $key === $kd_crop ? 'true' : 'false'; ?>"><?php echo esc_html( $g['label'] ); ?><?php echo kudino_icon( 'chevron-left', 16 ); // phpcs:ignore ?></button></th>
								<?php
								for ( $m = 1; $m <= 12; $m++ ) :
									$cell = wp_list_filter( $g['steps'], array( 0 => $m ) );
									?>
									<td class="<?php echo $m === $kd_today['m'] ? 'is-now' : ''; ?>">
										<?php foreach ( $cell as $s ) : ?>
											<span class="kd-mark" style="--c:<?php echo esc_attr( kudino_nutrient_color( $s[3] ) ); ?>" title="<?php echo esc_attr( $s[1] . ' — ' . $s[2] ); ?>"><?php echo esc_html( $s[3] ); ?><span class="kd-sr"><?php echo esc_html( ': ' . $s[1] . '، ' . $s[2] ); ?></span></span>
										<?php endforeach; ?>
									</td>
								<?php endfor; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php
			foreach ( $kd_guides as $key => $g ) :
				$ord = $g['steps'];
				usort(
					$ord,
					function ( $a, $b ) use ( $kd_today ) {
						return ( ( $a[0] - $kd_today['m'] + 12 ) % 12 ) - ( ( $b[0] - $kd_today['m'] + 12 ) % 12 );
					}
				);
				?>
				<div class="kd-almanac__detail" id="kd-plan-<?php echo esc_attr( $key ); ?>" <?php echo $key === $kd_crop ? '' : 'hidden'; ?>>
					<div class="kd-almanac__note">
						<h3><?php echo esc_html( 'برنامه ' . $g['label'] ); ?></h3>
						<p><?php echo esc_html( $g['note'] ); ?></p>
						<a class="kd-btn kd-btn--sm" href="#kd-consult"><?php echo kudino_icon( 'headset', 16 ); // phpcs:ignore ?><?php esc_html_e( 'برنامه اختصاصی مزرعه من', 'kudino' ); ?></a>
					</div>
					<ol class="kd-plan">
						<?php foreach ( $ord as $i => $s ) : ?>
							<li class="<?php echo 0 === $i ? 'is-next' : ''; ?>">
								<span class="kd-plan__month"><?php echo esc_html( $kd_months[ $s[0] - 1 ] ); ?></span>
								<span><span class="kd-mark" style="--c:<?php echo esc_attr( kudino_nutrient_color( $s[3] ) ); ?>"><?php echo esc_html( $s[3] ); ?></span></span>
								<span class="kd-plan__stage"><?php echo esc_html( $s[1] ); ?></span>
								<a class="kd-plan__product" href="<?php echo esc_url( kudino_search_url( $s[2] ) ); ?>"><?php echo esc_html( $s[2] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ======================================================= Deficiency table -->
<section class="kd-band" id="kd-symptoms">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-head__title"><?php esc_html_e( 'کمبود را از روی برگ بشناس', 'kudino' ); ?> <small><?php esc_html_e( 'روی عنصر بزن تا کودهای جبرانی را ببینی', 'kudino' ); ?></small></h2>
		</header>
		<div class="kd-elements">
			<?php foreach ( kudino_elements() as $e ) : ?>
				<a class="kd-el" href="<?php echo esc_url( kudino_search_url( 'کود ' . $e[2] ) ); ?>"<?php echo $e[4] ? ' style="--c:' . esc_attr( $e[4] ) . '"' : ''; ?>>
					<span class="kd-el__sym"><small><?php echo esc_html( $e[1] ); ?></small><?php echo esc_html( $e[0] ); ?></span>
					<span><span class="kd-el__name"><?php echo esc_html( $e[2] ); ?></span><span class="kd-el__sign"><?php echo esc_html( $e[3] ); ?></span></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ======================================================= Brands -->
<section class="kd-band">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-head__title"><?php esc_html_e( 'برندهای معتبر', 'kudino' ); ?></h2>
			<a class="kd-more" href="<?php echo esc_url( kudino_shop_url() ); ?>"><?php esc_html_e( 'همه برندها', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a>
		</header>
		<div class="kd-brands">
			<?php foreach ( kudino_brands() as $b ) : ?>
				<a class="kd-brand<?php echo $b[1] ? ' kd-brand--' . esc_attr( $b[1] ) : ''; ?>" href="<?php echo esc_url( kudino_search_url( $b[0] ) ); ?>"><?php echo esc_html( $b[0] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ======================================================= Consult -->
<section class="kd-band" id="kd-consult">
	<div class="kd-wrap">
		<div class="kd-consult">
			<svg class="kd-consult__leaf" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10Z"/></svg>
			<div>
				<span class="kd-tag kd-tag--orange"><?php esc_html_e( 'رایگان', 'kudino' ); ?></span>
				<h2 class="kd-consult__title" style="margin-block-start:12px"><?php esc_html_e( 'مشاوره کوددهی با کارشناس کشاورزی', 'kudino' ); ?></h2>
				<p class="kd-consult__text"><?php esc_html_e( 'نمی‌دانی چه کودی، چقدر و کی بدهی؟ شماره‌ات را بگذار یا در واتساپ پیام بده. کارشناس ما بر اساس محصول، سن گیاه و آزمایش خاک راهنمایی‌ات می‌کند.', 'kudino' ); ?></p>
				<ul class="kd-consult__list">
					<li><?php echo kudino_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'تماس در کمتر از ۲ ساعت کاری', 'kudino' ); ?></li>
					<li><?php echo kudino_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'تفسیر رایگان آزمایش خاک و آب', 'kudino' ); ?></li>
					<li><?php echo kudino_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'برنامه کوددهی فصلی مخصوص مزرعه شما', 'kudino' ); ?></li>
				</ul>
				<div class="kd-consult__ways">
					<a class="kd-btn kd-btn--white" href="<?php echo esc_url( 'https://wa.me/' . kudino_mod( 'whatsapp' ) ); ?>"><?php echo kudino_icon( 'whatsapp', 18 ); // phpcs:ignore ?><?php esc_html_e( 'پیام در واتساپ', 'kudino' ); ?></a>
					<span class="kd-btn kd-btn--glass" dir="ltr"><?php echo kudino_icon( 'phone', 18 ); // phpcs:ignore ?><?php echo esc_html( kudino_mod( 'phone' ) ); ?></span>
				</div>
			</div>
			<form class="kd-form" action="#" method="post" data-kd-fake-submit>
				<h3 class="kd-form__title"><?php esc_html_e( 'درخواست تماس کارشناس', 'kudino' ); ?></h3>
				<div class="kd-field">
					<label class="kd-field__label" for="kd-c-phone"><?php esc_html_e( 'شماره موبایل', 'kudino' ); ?></label>
					<span class="kd-control"><?php echo kudino_icon( 'phone', 18 ); // phpcs:ignore ?><input id="kd-c-phone" type="tel" inputmode="numeric" autocomplete="tel" required placeholder="۰۹۱۲ ۰۰۰ ۰۰۰۰"></span>
				</div>
				<div class="kd-form__row">
					<div class="kd-field">
						<label class="kd-field__label" for="kd-c-crop"><?php esc_html_e( 'محصول', 'kudino' ); ?></label>
						<span class="kd-control"><select id="kd-c-crop">
							<?php foreach ( $kd_guides as $key => $g ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $kd_crop ); ?>><?php echo esc_html( $g['label'] ); ?></option>
							<?php endforeach; ?>
						</select><?php echo kudino_icon( 'chevron-down', 16 ); // phpcs:ignore ?></span>
					</div>
					<div class="kd-field">
						<label class="kd-field__label" for="kd-c-area"><?php esc_html_e( 'سطح زیر کشت', 'kudino' ); ?></label>
						<span class="kd-control"><input id="kd-c-area" type="text" inputmode="decimal" placeholder="۲"><span class="kd-control__suffix"><?php esc_html_e( 'هکتار', 'kudino' ); ?></span></span>
					</div>
				</div>
				<button class="kd-btn kd-btn--cta kd-btn--block kd-btn--lg" type="submit"><?php esc_html_e( 'درخواست مشاوره رایگان', 'kudino' ); ?></button>
				<p class="kd-form__fine" data-kd-status><?php echo kudino_icon( 'shield', 15 ); // phpcs:ignore ?><?php esc_html_e( 'شماره شما فقط برای همین تماس استفاده می‌شود.', 'kudino' ); ?></p>
			</form>
		</div>
	</div>
</section>

<!-- ======================================================= Reviews -->
<section class="kd-band--tint kd-reviews">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-head__title"><?php esc_html_e( 'نظر مشتری‌های کودینو', 'kudino' ); ?></h2>
			<div class="kd-head__tools">
				<button class="kd-arrow" type="button" data-kd-rail-prev="#kd-reviews-rail" aria-label="<?php esc_attr_e( 'قبلی', 'kudino' ); ?>"><?php echo kudino_icon( 'chevron-right', 18 ); // phpcs:ignore ?></button>
				<button class="kd-arrow" type="button" data-kd-rail-next="#kd-reviews-rail" aria-label="<?php esc_attr_e( 'بعدی', 'kudino' ); ?>"><?php echo kudino_icon( 'chevron-left', 18 ); // phpcs:ignore ?></button>
			</div>
		</header>
		<div class="kd-rail" id="kd-reviews-rail" data-kd-rail>
			<?php foreach ( kudino_demo_reviews() as $r ) : ?>
				<figure class="kd-review" style="margin:0">
					<span class="kd-review__stars" aria-label="۵ از ۵"><?php echo str_repeat( kudino_icon( 'star', 15, 'is-filled' ), 5 ); // phpcs:ignore ?></span>
					<blockquote style="margin:0"><p><?php echo esc_html( $r[0] ); ?></p></blockquote>
					<figcaption class="kd-review__who"><span class="kd-review__avatar"><?php echo esc_html( mb_substr( $r[2], 0, 1 ) ); ?></span><span><b><?php echo esc_html( $r[1] ); ?></b><small><?php echo esc_html( $r[2] ); ?></small></span></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ======================================================= Journal -->
<section class="kd-band">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-head__title"><?php esc_html_e( 'مجله کودینو', 'kudino' ); ?> <small><?php esc_html_e( 'آموزش کوددهی و تقویم باغبانی', 'kudino' ); ?></small></h2>
			<a class="kd-more" href="<?php echo esc_url( kudino_blog_url() ); ?>"><?php esc_html_e( 'همه مطالب', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a>
		</header>
		<div class="kd-posts">
			<?php
			$kd_q = new WP_Query(
				array(
					'posts_per_page'      => 4,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			if ( $kd_q->post_count >= 4 ) :
				while ( $kd_q->have_posts() ) :
					$kd_q->the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				wp_reset_postdata();
			else :
				foreach ( kudino_demo_posts() as $i => $dp ) :
					?>
					<article class="kd-post">
						<a class="kd-post__media" href="<?php echo esc_url( kudino_blog_url() ); ?>" tabindex="-1" aria-hidden="true"><?php echo kudino_post_art( $i ); // phpcs:ignore ?></a>
						<div class="kd-post__meta"><span class="kd-tag"><?php echo esc_html( $dp['cat'] ); ?></span><span><?php echo esc_html( $dp['time'] . ' مطالعه' ); ?></span></div>
						<h3 class="kd-post__title"><a href="<?php echo esc_url( kudino_blog_url() ); ?>"><?php echo esc_html( $dp['title'] ); ?></a></h3>
						<span class="kd-post__more"><?php esc_html_e( 'ادامه مطلب', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 14 ); // phpcs:ignore ?></span>
					</article>
					<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
</section>

<?php
get_footer();
