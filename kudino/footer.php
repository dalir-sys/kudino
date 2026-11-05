<?php
/**
 * Footer + mobile tab bar.
 *
 * @package Kudino
 */

?>
</main>

<footer class="kd-footer">
	<div class="kd-footer__strata" aria-hidden="true"></div>
	<div class="kd-wrap">
		<div class="kd-footer__top">
			<div class="kd-footer__about">
				<?php kudino_logo( true ); ?>
				<p><?php esc_html_e( 'کودینو کود و نهاده کشاورزی را با آنالیز شفاف، قیمت منصفانه و مشاوره کارشناس به دست کشاورز، باغدار و گل‌دوست می‌رساند.', 'kudino' ); ?></p>
				<div class="kd-footer__social">
					<a href="<?php echo esc_url( kudino_mod( 'instagram' ) ); ?>">Instagram</a>
					<a href="<?php echo esc_url( kudino_mod( 'telegram' ) ); ?>">Telegram</a>
				</div>
			</div>

			<div>
				<h4><?php esc_html_e( 'فروشگاه', 'kudino' ); ?></h4>
				<ul>
					<?php foreach ( kudino_categories() as $c ) : ?>
						<li><a href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['short'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h4><?php esc_html_e( 'راهنما', 'kudino' ); ?></h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => function () {
							echo '<ul>';
							echo '<li><a href="' . esc_url( home_url( '/#kd-almanac' ) ) . '">تقویم کوددهی</a></li>';
							echo '<li><a href="' . esc_url( kudino_blog_url() ) . '">دفترچه کشت</a></li>';
							foreach ( array( 'شیوه ارسال', 'بازگشت کالا', 'پرسش‌های رایج' ) as $l ) {
								echo '<li><a href="#">' . esc_html( $l ) . '</a></li>';
							}
							echo '</ul>';
						},
					)
				);
				?>
			</div>

			<div>
				<h4><?php esc_html_e( 'تماس', 'kudino' ); ?></h4>
				<ul class="kd-footer__contact">
					<li><small><?php esc_html_e( 'مشاوره خرید', 'kudino' ); ?></small><span dir="ltr" style="text-align:end"><?php echo esc_html( kudino_mod( 'phone' ) ); ?></span></li>
					<li><small><?php esc_html_e( 'ایمیل', 'kudino' ); ?></small><span><?php echo esc_html( kudino_mod( 'email' ) ); ?></span></li>
					<li><small><?php esc_html_e( 'پاسخ‌گویی', 'kudino' ); ?></small><span><?php echo esc_html( kudino_mod( 'hours' ) ); ?></span></li>
				</ul>
			</div>

			<div class="kd-footer__news">
				<h4><?php esc_html_e( 'یادآور ماهانه کوددهی', 'kudino' ); ?></h4>
				<p style="margin-block-end:12px"><?php esc_html_e( 'اول هر ماه، کارهای همان ماه برای محصول خودت.', 'kudino' ); ?></p>
				<form class="kd-control" action="#" method="post" data-kd-fake-submit>
					<label class="kd-sr" for="kd-news-email"><?php esc_html_e( 'ایمیل', 'kudino' ); ?></label>
					<input id="kd-news-email" type="email" required placeholder="<?php esc_attr_e( 'ایمیل شما', 'kudino' ); ?>">
					<button class="kd-btn" type="submit"><?php esc_html_e( 'عضویت', 'kudino' ); ?></button>
				</form>
				<div class="kd-footer__seals">
					<span class="kd-footer__seal"><?php esc_html_e( 'نماد اعتماد الکترونیکی', 'kudino' ); ?></span>
					<span class="kd-footer__seal"><?php esc_html_e( 'نشان ساماندهی', 'kudino' ); ?></span>
				</div>
			</div>
		</div>

		<div class="kd-footer__bottom">
			<span><?php echo esc_html( sprintf( '© %s %s', kudino_fa( kudino_today()['y'] ), get_bloginfo( 'name' ) ) ); ?></span>
			<span><?php echo esc_html( kudino_mod( 'address' ) ); ?></span>
			<span><?php esc_html_e( 'همه قیمت‌ها به تومان است.', 'kudino' ); ?></span>
		</div>
	</div>
</footer>

<nav class="kd-tabbar" aria-label="<?php esc_attr_e( 'ناوبری موبایل', 'kudino' ); ?>">
	<div class="kd-tabbar__inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'is-active' : ''; ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><?php echo kudino_icon( 'home', 22 ); // phpcs:ignore ?><?php esc_html_e( 'خانه', 'kudino' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/#kd-index' ) ); ?>"><?php echo kudino_icon( 'grid', 22 ); // phpcs:ignore ?><?php esc_html_e( 'دسته‌ها', 'kudino' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/#kd-almanac' ) ); ?>"><?php echo kudino_icon( 'clock', 22 ); // phpcs:ignore ?><?php esc_html_e( 'تقویم', 'kudino' ); ?></a>
		<a class="kd-tabbar__cart" href="<?php echo esc_url( kudino_cart_url() ); ?>"><?php echo kudino_icon( 'bag', 22 ); // phpcs:ignore ?><?php esc_html_e( 'سبد', 'kudino' ); ?><?php echo kudino_cart_count_html(); // phpcs:ignore ?></a>
		<a href="<?php echo esc_url( kudino_account_url() ); ?>"><?php echo kudino_icon( 'user', 22 ); // phpcs:ignore ?><?php esc_html_e( 'حساب', 'kudino' ); ?></a>
	</div>
</nav>

<?php wp_footer(); ?>
</body>
</html>
