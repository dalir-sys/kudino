<?php
/**
 * Site header: almanac strip + masthead.
 *
 * @package Kudino
 */

$kudino_today = kudino_today();
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

<div class="kd-almanac-strip">
	<div class="kd-wrap kd-almanac-strip__inner">
		<p class="kd-almanac-strip__today">
			<b><?php echo esc_html( $kudino_today['label'] ); ?></b>
			<i class="kd-almanac-strip__sep" aria-hidden="true"></i>
			<span><?php echo esc_html( 'کار این ماه: ' . kudino_month_task( $kudino_today['m'] ) ); ?></span>
		</p>
		<nav class="kd-almanac-strip__links" aria-label="<?php esc_attr_e( 'لینک‌های سریع', 'kudino' ); ?>">
			<span><?php echo esc_html( kudino_mod( 'shipping' ) ); ?></span>
			<a href="<?php echo esc_url( kudino_account_url() ); ?>"><?php esc_html_e( 'پیگیری سفارش', 'kudino' ); ?></a>
		</nav>
	</div>
</div>

<header class="kd-header" id="kd-header">
	<div class="kd-wrap kd-header__row">
		<div style="display:flex;align-items:center;gap:4px">
			<button class="kd-tool kd-header__menu" type="button" data-kd-drawer-open aria-controls="kd-drawer" aria-expanded="false" aria-label="<?php esc_attr_e( 'منو', 'kudino' ); ?>"><?php echo kudino_icon( 'menu', 22 ); // phpcs:ignore ?></button>
			<?php kudino_logo(); ?>
		</div>

		<nav class="kd-header__nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'kudino' ); ?>">
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
		</nav>

		<div class="kd-header__tools">
			<form class="kd-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="kd-control">
					<?php echo kudino_icon( 'search', 18 ); // phpcs:ignore ?>
					<span class="kd-sr"><?php esc_html_e( 'جستجو', 'kudino' ); ?></span>
					<input type="search" name="s" id="kd-search-input" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'نام کود یا عنصر…', 'kudino' ); ?>">
					<kbd aria-hidden="true">/</kbd>
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<input type="hidden" name="post_type" value="product">
					<?php endif; ?>
				</label>
			</form>
			<a class="kd-tool" href="<?php echo esc_url( kudino_account_url() ); ?>" aria-label="<?php esc_attr_e( 'حساب کاربری', 'kudino' ); ?>">
				<?php echo kudino_icon( 'user', 20 ); // phpcs:ignore ?>
				<span class="kd-tool__label"><?php echo is_user_logged_in() ? esc_html__( 'حساب من', 'kudino' ) : esc_html__( 'ورود', 'kudino' ); ?></span>
			</a>
			<a class="kd-tool" href="<?php echo esc_url( kudino_cart_url() ); ?>">
				<?php echo kudino_icon( 'bag', 20 ); // phpcs:ignore ?>
				<span class="kd-tool__label"><?php esc_html_e( 'سبد', 'kudino' ); ?></span>
				<?php echo kudino_cart_count_html(); // phpcs:ignore ?>
			</a>
		</div>
	</div>
</header>

<div class="kd-drawer" id="kd-drawer" aria-hidden="true">
	<div class="kd-drawer__scrim" data-kd-drawer-close></div>
	<div class="kd-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منو', 'kudino' ); ?>">
		<div class="kd-drawer__head">
			<?php kudino_logo(); ?>
			<button class="kd-tool" type="button" data-kd-drawer-close aria-label="<?php esc_attr_e( 'بستن منو', 'kudino' ); ?>"><?php echo kudino_icon( 'close', 22 ); // phpcs:ignore ?></button>
		</div>
		<div class="kd-drawer__body">
			<form class="kd-searchform" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="padding-block:12px">
				<label class="kd-control"><?php echo kudino_icon( 'search', 18 ); // phpcs:ignore ?><span class="kd-sr"><?php esc_html_e( 'جستجو', 'kudino' ); ?></span><input type="search" name="s" placeholder="<?php esc_attr_e( 'نام کود یا عنصر…', 'kudino' ); ?>"></label>
			</form>
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
		<div class="kd-drawer__foot">
			<p class="kd-note"><?php esc_html_e( 'مشاوره رایگان خرید', 'kudino' ); ?></p>
			<p dir="ltr" style="font-size:1.25rem;font-weight:700;text-align:end"><?php echo esc_html( kudino_mod( 'phone' ) ); ?></p>
		</div>
	</div>
</div>

<main id="kd-main" class="kd-main">
