<?php
/**
 * Footer: promises row, links, contact cards, seals; WhatsApp + mobile tab bar.
 *
 * @package Kudino
 */

$kudino_wa = kudino_mod( 'whatsapp' );
?>
</main>

<footer class="kd-footer">
	<div class="kd-wrap">
		<div class="kd-trust">
			<?php
			$kudino_trust = array(
				array( 'truck', 'ارسال به سراسر ایران', 'پست برای بسته‌های کوچک، باربری برای کیسه‌ها' ),
				array( 'badge', 'ضمانت اصالت کالا', 'همه کودها با برچسب و شماره ثبت' ),
				array( 'headset', 'مشاوره رایگان', 'کارشناس کشاورزی، پیش و پس از خرید' ),
				array( 'card', 'پرداخت امن و اقساطی', 'درگاه بانکی، پرداخت در محل، اقساط' ),
			);
			foreach ( $kudino_trust as $t ) :
				?>
				<div class="kd-trust__item">
					<span class="kd-trust__icon"><?php echo kudino_icon( $t[0], 26 ); // phpcs:ignore ?></span>
					<div><b><?php echo esc_html( $t[1] ); ?></b><span><?php echo esc_html( $t[2] ); ?></span></div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="kd-footer__main">
			<div class="kd-footer__about">
				<?php kudino_logo(); ?>
				<p><?php esc_html_e( 'کودینو فروشگاه اینترنتی تخصصی کود و نهاده‌های کشاورزی است. کودهای شیمیایی، آلی و زیستی اصل را با آنالیز روشن، قیمت منصفانه و مشاوره رایگان کارشناس به دست کشاورزها، باغدارها و گل‌دوست‌ها می‌رسانیم.', 'kudino' ); ?></p>
				<div class="kd-social">
					<a href="<?php echo esc_url( kudino_mod( 'instagram' ) ); ?>" aria-label="Instagram"><?php echo kudino_icon( 'instagram', 18 ); // phpcs:ignore ?></a>
					<a href="<?php echo esc_url( kudino_mod( 'telegram' ) ); ?>" aria-label="Telegram"><?php echo kudino_icon( 'send', 18 ); // phpcs:ignore ?></a>
					<a href="<?php echo esc_url( 'https://wa.me/' . $kudino_wa ); ?>" aria-label="WhatsApp"><?php echo kudino_icon( 'whatsapp', 18 ); // phpcs:ignore ?></a>
					<a href="#" aria-label="<?php esc_attr_e( 'ایتا', 'kudino' ); ?>"><?php esc_html_e( 'ایتا', 'kudino' ); ?></a>
					<a href="#" aria-label="<?php esc_attr_e( 'بله', 'kudino' ); ?>"><?php esc_html_e( 'بله', 'kudino' ); ?></a>
				</div>
			</div>

			<div>
				<h4><?php esc_html_e( 'دسته‌بندی‌ها', 'kudino' ); ?></h4>
				<ul>
					<?php foreach ( kudino_categories() as $c ) : ?>
						<li><a href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['name'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h4><?php esc_html_e( 'دسترسی سریع', 'kudino' ); ?></h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => function () {
							echo '<ul>';
							echo '<li><a href="' . esc_url( home_url( '/#kd-almanac' ) ) . '">تقویم کوددهی</a></li>';
							echo '<li><a href="' . esc_url( kudino_blog_url() ) . '">مجله کودینو</a></li>';
							foreach ( array( 'شیوه‌های ارسال', 'خرید اقساطی', 'بازگشت کالا', 'سوالات متداول', 'درباره ما' ) as $l ) {
								echo '<li><a href="#">' . esc_html( $l ) . '</a></li>';
							}
							echo '</ul>';
						},
					)
				);
				?>
			</div>

			<div>
				<h4><?php esc_html_e( 'ارتباط با ما', 'kudino' ); ?></h4>
				<div class="kd-contact">
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', strtr( kudino_mod( 'phone' ), array( '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9' ) ) ) ); ?>"><?php echo kudino_icon( 'phone', 18 ); // phpcs:ignore ?><span><small><?php esc_html_e( 'تلفن مشاوره و فروش', 'kudino' ); ?></small><b dir="ltr"><?php echo esc_html( kudino_mod( 'phone' ) ); ?></b></span></a>
					<a href="mailto:<?php echo esc_attr( kudino_mod( 'email' ) ); ?>"><?php echo kudino_icon( 'mail', 18 ); // phpcs:ignore ?><span><small><?php esc_html_e( 'ایمیل', 'kudino' ); ?></small><b><?php echo esc_html( kudino_mod( 'email' ) ); ?></b></span></a>
					<div><?php echo kudino_icon( 'pin', 18 ); // phpcs:ignore ?><span><small><?php echo esc_html( kudino_mod( 'hours' ) ); ?></small><b><?php echo esc_html( kudino_mod( 'address' ) ); ?></b></span></div>
				</div>
				<form class="kd-news" action="#" method="post" data-kd-fake-submit>
					<label class="kd-sr" for="kd-news-phone"><?php esc_html_e( 'شماره موبایل برای خبرنامه', 'kudino' ); ?></label>
					<span class="kd-control"><input id="kd-news-phone" type="tel" inputmode="numeric" required placeholder="<?php esc_attr_e( 'موبایل برای تخفیف‌ها', 'kudino' ); ?>"></span>
					<button class="kd-btn kd-btn--cta" type="submit"><?php esc_html_e( 'عضویت', 'kudino' ); ?></button>
				</form>
				<div class="kd-seals">
					<span class="kd-seal"><?php echo kudino_icon( 'shield', 28 ); // phpcs:ignore ?><?php esc_html_e( 'نماد اعتماد الکترونیکی', 'kudino' ); ?></span>
					<span class="kd-seal"><?php echo kudino_icon( 'badge', 28 ); // phpcs:ignore ?><?php esc_html_e( 'نشان ساماندهی', 'kudino' ); ?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="kd-footer__bar">
		<div class="kd-wrap">
			<span><?php echo esc_html( sprintf( '© %s — تمامی حقوق برای %s محفوظ است.', kudino_fa( kudino_today()['y'] ), get_bloginfo( 'name' ) ) ); ?></span>
			<span><?php esc_html_e( 'قیمت‌ها به تومان است.', 'kudino' ); ?></span>
		</div>
	</div>
</footer>

<?php if ( $kudino_wa ) : ?>
	<a class="kd-wa" href="<?php echo esc_url( 'https://wa.me/' . $kudino_wa ); ?>" aria-label="<?php esc_attr_e( 'مشاوره در واتساپ', 'kudino' ); ?>" target="_blank" rel="noopener"><?php echo kudino_icon( 'whatsapp', 28 ); // phpcs:ignore ?></a>
<?php endif; ?>

<nav class="kd-tabbar" aria-label="<?php esc_attr_e( 'ناوبری موبایل', 'kudino' ); ?>">
	<div class="kd-tabbar__inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'is-active' : ''; ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><?php echo kudino_icon( 'home', 22 ); // phpcs:ignore ?><?php esc_html_e( 'خانه', 'kudino' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/#kd-cats' ) ); ?>"><?php echo kudino_icon( 'grid', 22 ); // phpcs:ignore ?><?php esc_html_e( 'دسته‌ها', 'kudino' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/#kd-almanac' ) ); ?>"><?php echo kudino_icon( 'calendar', 22 ); // phpcs:ignore ?><?php esc_html_e( 'تقویم', 'kudino' ); ?></a>
		<a class="kd-tabbar__cart" href="<?php echo esc_url( kudino_cart_url() ); ?>"><?php echo kudino_icon( 'bag', 22 ); // phpcs:ignore ?><?php esc_html_e( 'سبد خرید', 'kudino' ); ?><?php echo kudino_cart_count_html(); // phpcs:ignore ?></a>
		<a href="<?php echo esc_url( kudino_account_url() ); ?>"><?php echo kudino_icon( 'user', 22 ); // phpcs:ignore ?><?php esc_html_e( 'حساب من', 'kudino' ); ?></a>
	</div>
</nav>

<?php wp_footer(); ?>
</body>
</html>
