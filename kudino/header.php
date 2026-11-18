<?php
/**
 * Site header: promo strip, masthead with search, category navigation.
 *
 * @package Kudino
 */

$kudino_today = kudino_today();
$kudino_cats  = kudino_categories();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="kd-sr" href="#kd-main"><?php esc_html_e( 'رفتن به محتوا', 'kudino' ); ?></a>

<div class="kd-strip">
	<div class="kd-wrap kd-strip__inner">
		<p class="kd-strip__day">
			<b><?php echo esc_html( $kudino_today['label'] ); ?></b>
			<span><?php echo esc_html( '· کار این ماه: ' . kudino_month_task( $kudino_today['m'] ) ); ?></span>
		</p>
		<p class="kd-strip__offer"><?php echo kudino_icon( 'truck', 16 ); // phpcs:ignore ?><span><?php echo esc_html( kudino_mod( 'shipping' ) ); ?></span><b><?php echo esc_html( '· ' . kudino_mod( 'installment' ) ); ?></b></p>
		<nav class="kd-strip__links" aria-label="<?php esc_attr_e( 'لینک‌های سریع', 'kudino' ); ?>">
			<a href="<?php echo esc_url( kudino_account_url() ); ?>"><?php esc_html_e( 'پیگیری سفارش', 'kudino' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/#kd-consult' ) ); ?>"><?php esc_html_e( 'مشاوره رایگان', 'kudino' ); ?></a>
		</nav>
	</div>
</div>

<header class="kd-header" id="kd-header">
	<div class="kd-wrap kd-header__row">
		<button class="kd-tool kd-header__menu" type="button" data-kd-drawer-open aria-controls="kd-drawer" aria-expanded="false" aria-label="<?php esc_attr_e( 'منو', 'kudino' ); ?>"><?php echo kudino_icon( 'menu', 22 ); // phpcs:ignore ?></button>
		<?php kudino_logo(); ?>

		<form class="kd-header__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="kd-control">
				<?php echo kudino_icon( 'search', 20 ); // phpcs:ignore ?>
				<span class="kd-sr"><?php esc_html_e( 'جستجو', 'kudino' ); ?></span>
				<input type="search" name="s" id="kd-search-input" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جستجوی کود، محصول یا عنصر (مثلاً کلات آهن)…', 'kudino' ); ?>">
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<input type="hidden" name="post_type" value="product">
				<?php endif; ?>
			</label>
		</form>

		<div class="kd-header__tools">
			<a class="kd-tool" href="<?php echo esc_url( kudino_account_url() ); ?>" aria-label="<?php esc_attr_e( 'حساب کاربری', 'kudino' ); ?>">
				<?php echo kudino_icon( 'user', 20 ); // phpcs:ignore ?>
				<span class="kd-tool__label"><?php echo is_user_logged_in() ? esc_html__( 'حساب من', 'kudino' ) : esc_html__( 'ورود | ثبت‌نام', 'kudino' ); ?></span>
			</a>
			<a class="kd-tool kd-tool--cart" href="<?php echo esc_url( kudino_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'سبد خرید', 'kudino' ); ?>">
				<?php echo kudino_icon( 'bag', 20 ); // phpcs:ignore ?>
				<span class="kd-tool__label"><?php esc_html_e( 'سبد خرید', 'kudino' ); ?></span>
				<?php echo kudino_cart_count_html(); // phpcs:ignore ?>
			</a>
		</div>
	</div>

	<nav class="kd-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'kudino' ); ?>">
		<div class="kd-wrap kd-nav__row">
			<div class="kd-nav__main">
				<div class="kd-nav__cats">
					<button type="button" aria-haspopup="true" aria-expanded="false"><?php echo kudino_icon( 'grid', 18 ); // phpcs:ignore ?><?php esc_html_e( 'دسته‌بندی محصولات', 'kudino' ); ?></button>
					<div class="kd-mega">
						<?php foreach ( $kudino_cats as $c ) : ?>
							<a href="<?php echo esc_url( $c['url'] ); ?>">
								<span><?php echo kudino_icon( $c['icon'], 22 ); // phpcs:ignore ?></span>
								<span><b><?php echo esc_html( $c['name'] ); ?></b><small><?php echo esc_html( $c['desc'] ); ?></small></span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'depth'          => 2,
						'fallback_cb'    => 'kudino_menu_fallback',
					)
				);
				?>
			</div>
			<span class="kd-nav__help"><?php echo kudino_icon( 'headset', 18 ); // phpcs:ignore ?><?php esc_html_e( 'مشاوره رایگان خرید:', 'kudino' ); ?> <b><?php echo esc_html( kudino_mod( 'phone' ) ); ?></b></span>
		</div>
	</nav>
</header>

<div class="kd-drawer" id="kd-drawer" aria-hidden="true">
	<div class="kd-drawer__scrim" data-kd-drawer-close></div>
	<div class="kd-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منو', 'kudino' ); ?>">
		<div class="kd-drawer__head">
			<?php kudino_logo(); ?>
			<button class="kd-tool" type="button" data-kd-drawer-close aria-label="<?php esc_attr_e( 'بستن منو', 'kudino' ); ?>" style="width:46px;padding:0;justify-content:center"><?php echo kudino_icon( 'close', 22 ); // phpcs:ignore ?></button>
		</div>
		<div class="kd-drawer__body">
			<?php foreach ( $kudino_cats as $c ) : ?>
				<a href="<?php echo esc_url( $c['url'] ); ?>"><span><?php echo kudino_icon( $c['icon'], 18 ); // phpcs:ignore ?></span><?php echo esc_html( $c['name'] ); ?></a>
			<?php endforeach; ?>
			<a href="<?php echo esc_url( home_url( '/#kd-almanac' ) ); ?>"><span><?php echo kudino_icon( 'calendar', 18 ); // phpcs:ignore ?></span><?php esc_html_e( 'تقویم کوددهی', 'kudino' ); ?></a>
			<a href="<?php echo esc_url( kudino_blog_url() ); ?>"><span><?php echo kudino_icon( 'leaf', 18 ); // phpcs:ignore ?></span><?php esc_html_e( 'مجله کودینو', 'kudino' ); ?></a>
		</div>
		<div class="kd-drawer__foot">
			<p style="font-size:var(--kd-t-xs);color:var(--kd-ink-3)"><?php esc_html_e( 'مشاوره رایگان خرید', 'kudino' ); ?></p>
			<p dir="ltr" style="font-size:1.25rem;font-weight:900;text-align:end;color:var(--kd-green-900)"><?php echo esc_html( kudino_mod( 'phone' ) ); ?></p>
		</div>
	</div>
</div>

<main id="kd-main" class="kd-main">
