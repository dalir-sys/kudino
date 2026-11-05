<?php
/**
 * Homepage — "Field Almanac".
 *
 * Three ways in: by crop (hero → almanac), by fertilizer family (index),
 * by deficiency sign (element table). Then the season's shelf.
 *
 * @package Kudino
 */

get_header();

$kd_today    = kudino_today();
$kd_months   = kudino_months();
$kd_products = function_exists( 'kudino_home_products' ) ? kudino_home_products( 8 ) : kudino_demo_products();
$kd_guides   = kudino_crop_guides();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only view switch.
$kd_crop     = isset( $_GET['crop'] ) ? sanitize_key( wp_unslash( $_GET['crop'] ) ) : '';
$kd_crop     = isset( $kd_guides[ $kd_crop ] ) ? $kd_crop : array_key_first( $kd_guides );
$kd_feature  = kudino_featured_label( $kd_products );
$kd_cats     = kudino_categories();
?>

<!-- ======================================================= Hero -->
<section class="kd-hero">
	<div class="kd-wrap kd-hero__grid">
		<div class="kd-hero__copy">
			<span class="kd-kicker"><span class="kd-mono">N·P·K</span><?php echo esc_html( kudino_mod( 'hero_kicker' ) ); ?></span>
			<h1 class="kd-hero__title"><?php echo wp_kses( kudino_mod( 'hero_title' ), array( 'em' => array(), 'br' => array() ) ); ?></h1>
			<p class="kd-lede"><?php echo esc_html( kudino_mod( 'hero_text' ) ); ?></p>

			<form class="kd-finder" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>#kd-almanac" data-kd-finder>
				<label class="kd-finder__label" for="kd-finder-crop"><?php esc_html_e( 'برای چه محصولی کود می‌خواهید؟', 'kudino' ); ?></label>
				<div class="kd-finder__row">
					<span class="kd-control">
						<select id="kd-finder-crop" name="crop">
							<?php foreach ( $kd_guides as $key => $g ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $kd_crop ); ?>><?php echo esc_html( $g['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo kudino_icon( 'chevron-down', 18 ); // phpcs:ignore ?>
					</span>
					<button class="kd-btn kd-btn--lg" type="submit"><?php esc_html_e( 'برنامه کوددهی', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 18, 'kd-icon--lead' ); // phpcs:ignore ?></button>
				</div>
				<p class="kd-finder__quick">
					<span><?php esc_html_e( 'یا مستقیم:', 'kudino' ); ?></span>
					<?php foreach ( $kd_cats as $c ) : ?>
						<a href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['short'] ); ?></a>
					<?php endforeach; ?>
				</p>
			</form>
		</div>

		<aside class="kd-hero__aside" aria-label="<?php esc_attr_e( 'کالای این هفته', 'kudino' ); ?>">
			<div class="kd-hero__aside-cap">
				<span><?php echo esc_html( 'پیشنهاد کارشناس برای ' . $kd_today['month'] ); ?></span>
				<span><?php esc_html_e( 'برچسب واقعی روی بسته', 'kudino' ); ?></span>
			</div>
			<article class="kd-label">
				<div class="kd-label__top">
					<span><?php esc_html_e( 'آنالیز تضمین‌شده', 'kudino' ); ?></span>
					<span class="kd-mono"><?php echo esc_html( $kd_feature['sku'] ); ?></span>
				</div>
				<div class="kd-label__body">
					<h2 class="kd-label__name"><?php echo esc_html( $kd_feature['name'] ); ?></h2>
					<p class="kd-label__sub"><?php echo esc_html( $kd_feature['sub'] ); ?></p>
					<div class="kd-label__code"><?php echo esc_html( implode( '-', $kd_feature['npk'] ) ); ?></div>
					<?php echo kudino_npk_bar( $kd_feature['npk'] ); // phpcs:ignore ?>
					<table class="kd-label__table">
						<?php
						$kd_rows = array(
							array( 'نیتروژن کل', 'N', 'var(--kd-n)' ),
							array( 'فسفر قابل جذب', 'P<sub>2</sub>O<sub>5</sub>', 'var(--kd-p)' ),
							array( 'پتاسیم محلول', 'K<sub>2</sub>O', 'var(--kd-k)' ),
						);
						foreach ( $kd_rows as $i => $r ) :
							?>
							<tr>
								<th scope="row"><i class="kd-dot" style="--_c:<?php echo esc_attr( $r[2] ); ?>"></i><?php echo esc_html( $r[0] ); ?></th>
								<td class="kd-mono"><?php echo wp_kses( $r[1], array( 'sub' => array() ) ); ?></td>
								<td><b class="kd-mono"><?php echo esc_html( $kd_feature['npk'][ $i ] ); ?>%</b></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<dl class="kd-label__dose">
						<?php foreach ( $kd_feature['dose'] as $d ) : ?>
							<dt><?php echo esc_html( $d[0] ); ?></dt><dd style="margin:0"><b><?php echo esc_html( $d[1] ); ?></b></dd>
						<?php endforeach; ?>
					</dl>
				</div>
				<div class="kd-label__foot">
					<div class="kd-label__price">
						<?php if ( $kd_feature['old_price'] ) : ?>
							<del><?php echo esc_html( kudino_price( $kd_feature['old_price'] ) ); ?></del>
						<?php endif; ?>
						<b><?php echo esc_html( kudino_price( $kd_feature['price'] ) ); ?><small><?php echo esc_html( 'تومان · ' . $kd_feature['weight'] ); ?></small></b>
					</div>
					<a class="kd-btn" href="<?php echo esc_url( $kd_feature['add_url'] ); ?>"><?php esc_html_e( 'افزودن به سبد', 'kudino' ); ?></a>
				</div>
			</article>
		</aside>
	</div>
</section>

<!-- ======================================================= Index: by family -->
<section class="kd-band" id="kd-index">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-title"><?php esc_html_e( 'قفسه‌ها', 'kudino' ); ?></h2>
			<a class="kd-more" href="<?php echo esc_url( kudino_shop_url() ); ?>"><?php esc_html_e( 'همه محصولات', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a>
		</header>
		<div class="kd-index">
			<?php foreach ( $kd_cats as $i => $c ) : ?>
				<a class="kd-index__row" href="<?php echo esc_url( $c['url'] ); ?>">
					<span class="kd-index__no"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<span>
						<span class="kd-index__name"><?php echo esc_html( $c['name'] ); ?></span>
						<span class="kd-index__desc"><?php echo esc_html( $c['desc'] ); ?></span>
					</span>
					<span class="kd-index__count"><?php echo esc_html( kudino_fa( $c['count'] ) . ' کالا' ); ?><?php echo kudino_icon( 'arrow-left', 18 ); // phpcs:ignore ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ======================================================= Elements: by deficiency -->
<section class="kd-band" id="kd-symptoms">
	<div class="kd-wrap kd-split">
		<div>
			<h2 class="kd-title"><?php esc_html_e( 'برگ‌ها خبر می‌دهند.', 'kudino' ); ?></h2>
			<p class="kd-lede" style="margin-block:16px 20px"><?php esc_html_e( 'زردی، لکه یا سوختگی برگ معمولاً کمبود یک عنصر است. عنصر را انتخاب کن تا کودهایی را ببینی که همان را جبران می‌کنند.', 'kudino' ); ?></p>
			<p class="kd-note"><?php esc_html_e( 'تشخیص قطعی با آزمایش خاک و برگ است؛ تفسیرش را رایگان انجام می‌دهیم.', 'kudino' ); ?> <a class="kd-more" href="#kd-rx"><?php esc_html_e( 'درخواست تفسیر', 'kudino' ); ?></a></p>
		</div>
		<div class="kd-elements">
			<?php foreach ( kudino_elements() as $e ) : ?>
				<a class="kd-el" href="<?php echo esc_url( kudino_search_url( 'کود ' . $e[2] ) ); ?>" style="<?php echo $e[4] ? '--c:' . esc_attr( $e[4] ) : ''; ?>">
					<span class="kd-el__top"><span class="kd-mono"><?php echo esc_html( $e[1] ); ?></span><span><?php echo $e[4] ? 'پرمصرف' : 'کم‌مصرف'; ?></span></span>
					<span class="kd-el__sym"><?php echo esc_html( $e[0] ); ?></span>
					<span>
						<span class="kd-el__name"><?php echo esc_html( $e[2] ); ?></span>
						<span class="kd-el__sign" style="display:block"><?php echo esc_html( $e[3] ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ======================================================= Shelf -->
<section class="kd-band" id="kd-shelf">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-title"><?php echo esc_html( 'انتخاب‌های ' . $kd_today['season'] ); ?></h2>
			<div class="kd-tabs" role="tablist" aria-label="<?php esc_attr_e( 'فیلتر نوع کود', 'kudino' ); ?>" data-kd-filter="#kd-shelf-grid">
				<?php
				$kd_groups = array(
					''         => 'همه',
					'chemical' => 'شیمیایی',
					'organic'  => 'آلی و زیستی',
					'liquid'   => 'مایع',
				);
				foreach ( $kd_groups as $key => $label ) :
					$n = '' === $key ? count( $kd_products ) : count( wp_list_filter( $kd_products, array( 'group' => $key ) ) );
					?>
					<button class="kd-tab" type="button" role="tab" aria-selected="<?php echo '' === $key ? 'true' : 'false'; ?>" data-group="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?><sup><?php echo esc_html( $n ); ?></sup></button>
				<?php endforeach; ?>
			</div>
		</header>
		<div class="kd-shelf" id="kd-shelf-grid" aria-live="polite">
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
			<h2 class="kd-title"><?php esc_html_e( 'تقویم کوددهی', 'kudino' ); ?></h2>
			<span class="kd-note"><?php echo esc_html( 'امروز ' . $kd_today['label'] . ' — ستون پررنگ، ماه جاری است' ); ?></span>
		</header>
		<div class="kd-almanac">
			<div class="kd-almanac__legend" aria-hidden="true">
				<span><span class="kd-mark" style="--c:var(--kd-n)">N</span> نیتروژن</span>
				<span><span class="kd-mark" style="--c:var(--kd-p)">P</span> فسفر</span>
				<span><span class="kd-mark" style="--c:var(--kd-k)">K</span> پتاسیم</span>
				<span><span class="kd-mark" style="--c:var(--kd-o)">O</span> کود آلی</span>
				<span><span class="kd-mark" style="--c:var(--kd-m)">Fe</span> ریزمغذی</span>
			</div>
			<div class="kd-almanac__scroll">
				<table class="kd-cal">
					<caption class="kd-sr"><?php esc_html_e( 'تقویم کوددهی محصولات در ماه‌های سال', 'kudino' ); ?></caption>
					<thead>
						<tr>
							<th scope="col"><span class="kd-sr"><?php esc_html_e( 'محصول', 'kudino' ); ?></span></th>
							<?php foreach ( $kd_months as $mi => $mn ) : ?>
								<th scope="col" class="<?php echo ( $mi + 1 ) === $kd_today['m'] ? 'is-now' : ''; ?>"><?php echo esc_html( $mn ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $kd_guides as $key => $g ) : ?>
							<tr class="<?php echo $key === $kd_crop ? 'is-active' : ''; ?>" data-crop="<?php echo esc_attr( $key ); ?>">
								<th scope="row">
									<button class="kd-cal__crop" type="button" aria-controls="kd-plan-<?php echo esc_attr( $key ); ?>" aria-expanded="<?php echo $key === $kd_crop ? 'true' : 'false'; ?>"><?php echo esc_html( $g['label'] ); ?><?php echo kudino_icon( 'chevron-down', 16 ); // phpcs:ignore ?></button>
								</th>
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
				$next = kudino_next_step_index( $g['steps'], $kd_today['m'] );
				$ord  = $g['steps'];
				usort(
					$ord,
					function ( $a, $b ) use ( $kd_today ) {
						return ( ( $a[0] - $kd_today['m'] + 12 ) % 12 ) - ( ( $b[0] - $kd_today['m'] + 12 ) % 12 );
					}
				);
				?>
				<div class="kd-almanac__detail" id="kd-plan-<?php echo esc_attr( $key ); ?>" <?php echo $key === $kd_crop ? '' : 'hidden'; ?>>
					<div class="kd-almanac__note">
						<span class="kd-kicker"><?php esc_html_e( 'برنامه از همین ماه', 'kudino' ); ?></span>
						<h3><?php echo esc_html( $g['label'] ); ?></h3>
						<p><?php echo esc_html( $g['note'] ); ?></p>
						<a class="kd-btn kd-btn--line kd-btn--sm" href="#kd-rx"><?php esc_html_e( 'برنامه اختصاصی مزرعه من', 'kudino' ); ?></a>
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

<!-- ======================================================= Two routes -->
<section class="kd-band">
	<div class="kd-wrap">
		<div class="kd-routes">
			<?php
			$kd_routes = array(
				array(
					'kicker' => 'از راه خاک',
					'title'  => 'خاکِ زنده، ریشه‌ی سالم',
					'text'   => 'کودهای آلی و زیستی کند اثر می‌کنند اما بافت خاک را می‌سازند و ماندگارند. برای باغ و زمینی که سال‌ها با آن کار داری.',
					'cat'    => $kd_cats[1],
					'items'  => array( array( 'ورمی‌کمپوست غنی‌شده، ۱۰ کیلو', 190000 ), array( 'هیومیک اسید ۸۵٪، ۱ کیلو', 340000 ), array( 'کود مرغی پلت‌شده، ۲۵ کیلو', 420000 ) ),
				),
				array(
					'kicker' => 'از راه برگ',
					'title'  => 'محلول‌پاشی، وقتی فرصت کم است',
					'text'   => 'برگ عنصر را در چند ساعت جذب می‌کند. برای رفع سریع کمبود و مراحل حساس گلدهی و میوه‌دهی، کنار کود خاکی.',
					'cat'    => $kd_cats[3],
					'items'  => array( array( 'عصاره جلبک دریایی، ۱ لیتر', 260000 ), array( 'کلات آهن EDDHA ۶٪، ۱ کیلو', 380000 ), array( 'کلسیم و بُر مایع، ۱ لیتر', 175000 ) ),
				),
			);
			foreach ( $kd_routes as $r ) :
				?>
				<div class="kd-route">
					<span class="kd-kicker"><?php echo esc_html( $r['kicker'] ); ?></span>
					<h2 class="kd-route__title"><?php echo esc_html( $r['title'] ); ?></h2>
					<p class="kd-route__text"><?php echo esc_html( $r['text'] ); ?></p>
					<div class="kd-route__list">
						<?php foreach ( $r['items'] as $it ) : ?>
							<a href="<?php echo esc_url( kudino_search_url( $it[0] ) ); ?>"><span><?php echo esc_html( $it[0] ); ?></span><b><?php echo esc_html( kudino_price( $it[1] ) ); ?> <small class="kd-note">تومان</small></b></a>
						<?php endforeach; ?>
					</div>
					<a class="kd-more" href="<?php echo esc_url( $r['cat']['url'] ); ?>"><?php echo esc_html( 'همه ' . $r['cat']['name'] ); ?><?php echo kudino_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="kd-promises" style="margin-block-start:48px">
			<div class="kd-promise"><b><?php esc_html_e( 'آنالیز روی هر بسته', 'kudino' ); ?></b><p><?php esc_html_e( 'درصد عناصر و شماره ثبت را پیش از خرید می‌بینی.', 'kudino' ); ?></p></div>
			<div class="kd-promise"><b><?php esc_html_e( 'کارشناس، نه فروشنده', 'kudino' ); ?></b><p><?php esc_html_e( 'مشاوره را مهندس زراعت و باغبانی می‌دهد، رایگان.', 'kudino' ); ?></p></div>
			<div class="kd-promise"><b><?php esc_html_e( 'تا درِ مزرعه', 'kudino' ); ?></b><p><?php esc_html_e( 'بسته‌های کوچک با پست، کیسه‌های ۲۵ کیلویی با باربری.', 'kudino' ); ?></p></div>
			<div class="kd-promise"><b><?php esc_html_e( 'پرداخت در محل', 'kudino' ); ?></b><p><?php esc_html_e( 'برای سفارش‌های باربری، هزینه را هنگام تحویل بده.', 'kudino' ); ?></p></div>
		</div>
	</div>
</section>

<!-- ======================================================= Prescription (consult) -->
<section class="kd-rx" id="kd-rx">
	<div class="kd-wrap kd-rx__grid">
		<div>
			<span class="kd-kicker"><?php esc_html_e( 'مشاوره رایگان', 'kudino' ); ?></span>
			<h2 class="kd-rx__title"><?php esc_html_e( 'نسخه‌ی کود، پیش از خرید.', 'kudino' ); ?></h2>
			<p class="kd-lede"><?php esc_html_e( 'شماره‌ات را بگذار. کارشناس ما با توجه به محصول، سن گیاه، آب و آزمایش خاک، کود و دوز درست را برایت می‌نویسد؛ خریدش با خودت.', 'kudino' ); ?></p>
			<div class="kd-rx__facts">
				<div><b><?php esc_html_e( '۲ ساعت', 'kudino' ); ?></b><span><?php esc_html_e( 'تا اولین تماس در ساعت کاری', 'kudino' ); ?></span></div>
				<div><b><?php esc_html_e( 'رایگان', 'kudino' ); ?></b><span><?php esc_html_e( 'تفسیر آزمایش خاک و آب', 'kudino' ); ?></span></div>
				<div><b><?php esc_html_e( 'فصلی', 'kudino' ); ?></b><span><?php esc_html_e( 'برنامه کوددهی مکتوب', 'kudino' ); ?></span></div>
			</div>
		</div>

		<form class="kd-slip" action="#" method="post" data-kd-fake-submit>
			<div class="kd-slip__head">
				<b><?php esc_html_e( 'درخواست نسخه', 'kudino' ); ?></b>
				<span class="kd-mono">RX / <?php echo esc_html( $kd_today['y'] . '-' . str_pad( (string) $kd_today['m'], 2, '0', STR_PAD_LEFT ) ); ?></span>
			</div>
			<div class="kd-field">
				<label class="kd-field__label" for="kd-rx-phone"><?php esc_html_e( 'شماره موبایل', 'kudino' ); ?></label>
				<span class="kd-control kd-control--ruled"><input id="kd-rx-phone" type="tel" inputmode="numeric" autocomplete="tel" required placeholder="۰۹۱۲ ۰۰۰ ۰۰۰۰"></span>
			</div>
			<div class="kd-slip__row">
				<div class="kd-field">
					<label class="kd-field__label" for="kd-rx-crop"><?php esc_html_e( 'محصول', 'kudino' ); ?></label>
					<span class="kd-control kd-control--ruled">
						<select id="kd-rx-crop">
							<?php foreach ( $kd_guides as $key => $g ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $kd_crop ); ?>><?php echo esc_html( $g['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo kudino_icon( 'chevron-down', 16 ); // phpcs:ignore ?>
					</span>
				</div>
				<div class="kd-field">
					<label class="kd-field__label" for="kd-rx-area"><?php esc_html_e( 'سطح زیر کشت', 'kudino' ); ?></label>
					<span class="kd-control kd-control--ruled"><input id="kd-rx-area" type="text" inputmode="decimal" placeholder="۲"><span class="kd-control__suffix"><?php esc_html_e( 'هکتار', 'kudino' ); ?></span></span>
				</div>
			</div>
			<button class="kd-btn kd-btn--lg kd-btn--block" type="submit"><?php esc_html_e( 'تماس کارشناس را می‌خواهم', 'kudino' ); ?></button>
			<p class="kd-slip__fine" data-kd-status><?php esc_html_e( 'شماره فقط برای همین تماس استفاده می‌شود و به کسی داده نمی‌شود.', 'kudino' ); ?></p>
		</form>
	</div>
</section>

<!-- ======================================================= Journal -->
<section class="kd-band">
	<div class="kd-wrap">
		<header class="kd-head">
			<h2 class="kd-title"><?php esc_html_e( 'دفترچه کشت', 'kudino' ); ?></h2>
			<a class="kd-more" href="<?php echo esc_url( kudino_blog_url() ); ?>"><?php esc_html_e( 'همه یادداشت‌ها', 'kudino' ); ?><?php echo kudino_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a>
		</header>
		<?php
		$kd_q     = new WP_Query(
			array(
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		$kd_posts = array();
		if ( $kd_q->post_count >= 3 ) {
			foreach ( $kd_q->posts as $post_obj ) {
				$cat        = get_the_category( $post_obj->ID );
				$kd_posts[] = array(
					'title'   => get_the_title( $post_obj ),
					'url'     => get_permalink( $post_obj ),
					'cat'     => $cat ? $cat[0]->name : '',
					'time'    => kudino_fa( max( 1, (int) round( count( preg_split( '/\s+/u', wp_strip_all_tags( $post_obj->post_content ), -1, PREG_SPLIT_NO_EMPTY ) ) / 200 ) ) ) . ' دقیقه',
					'excerpt' => get_the_excerpt( $post_obj ),
					'thumb'   => get_the_post_thumbnail( $post_obj, 'kudino-wide' ),
				);
			}
		} else {
			foreach ( kudino_demo_posts() as $dp ) {
				$kd_posts[] = $dp + array(
					'url'   => kudino_blog_url(),
					'thumb' => '',
				);
			}
		}
		$kd_lead = array_shift( $kd_posts );
		?>
		<div class="kd-journal">
			<article class="kd-lead">
				<a href="<?php echo esc_url( $kd_lead['url'] ); ?>" tabindex="-1" aria-hidden="true">
					<figure class="kd-lead__cover" style="margin:0">
						<?php if ( $kd_lead['thumb'] ) : ?>
							<?php echo $kd_lead['thumb']; // phpcs:ignore ?>
						<?php else : ?>
							<span class="kd-lead__fig">10-<span>52</span>-10</span>
							<figcaption><?php esc_html_e( 'عدد میانی: درصد فسفر', 'kudino' ); ?></figcaption>
						<?php endif; ?>
					</figure>
				</a>
				<div class="kd-meta"><b><?php echo esc_html( $kd_lead['cat'] ); ?></b><span><?php echo esc_html( $kd_lead['time'] . ' مطالعه' ); ?></span></div>
				<h3 class="kd-lead__title"><a href="<?php echo esc_url( $kd_lead['url'] ); ?>"><?php echo esc_html( $kd_lead['title'] ); ?></a></h3>
				<p class="kd-note" style="font-size:var(--kd-t-body);max-width:60ch"><?php echo esc_html( $kd_lead['excerpt'] ); ?></p>
			</article>
			<div class="kd-entries">
				<?php foreach ( $kd_posts as $ep ) : ?>
					<article class="kd-entry">
						<div class="kd-meta"><b><?php echo esc_html( $ep['cat'] ); ?></b><span><?php echo esc_html( $ep['time'] . ' مطالعه' ); ?></span></div>
						<h3 class="kd-entry__title"><a href="<?php echo esc_url( $ep['url'] ); ?>"><?php echo esc_html( $ep['title'] ); ?></a></h3>
						<p><?php echo esc_html( $ep['excerpt'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
