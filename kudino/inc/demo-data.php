<?php
/**
 * Store content: categories, nutrient table, crop almanac, demo products and posts.
 * Every list is filterable so a child theme or plugin can replace it.
 *
 * @package Kudino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fertilizer families (mapped to WooCommerce product_cat by slug when present).
 */
function kudino_categories() {
	$cats = array(
		array( 'slug' => 'chemical', 'short' => 'شیمیایی', 'name' => 'کودهای شیمیایی', 'desc' => 'اوره، NPK و سولفات‌ها؛ اثر سریع با دوز دقیق', 'count' => 64 ),
		array( 'slug' => 'organic', 'short' => 'آلی و دامی', 'name' => 'کودهای آلی و دامی', 'desc' => 'ورمی‌کمپوست، کود مرغی و گاوی پوسیده', 'count' => 38 ),
		array( 'slug' => 'bio', 'short' => 'زیستی', 'name' => 'کودهای زیستی', 'desc' => 'باکتری‌های تثبیت نیتروژن و حل‌کننده فسفات', 'count' => 21 ),
		array( 'slug' => 'liquid', 'short' => 'محلول‌پاشی', 'name' => 'محلول‌پاشی و کود مایع', 'desc' => 'جذب از راه برگ برای روزهای حساس رشد', 'count' => 47 ),
		array( 'slug' => 'micro', 'short' => 'ریزمغذی', 'name' => 'ریزمغذی‌ها و کلات‌ها', 'desc' => 'آهن، روی، بُر و منگنز برای رفع کمبود', 'count' => 29 ),
		array( 'slug' => 'houseplant', 'short' => 'آپارتمانی', 'name' => 'گیاهان آپارتمانی', 'desc' => 'فرمول‌های رقیق و بی‌بو برای گلدان', 'count' => 33 ),
	);

	foreach ( $cats as &$c ) {
		$c['url'] = kudino_search_url( $c['name'] );
		if ( taxonomy_exists( 'product_cat' ) ) {
			$term = get_term_by( 'slug', $c['slug'], 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				$c['url']   = get_term_link( $term );
				$c['count'] = (int) $term->count;
			}
		}
	}
	unset( $c );
	return apply_filters( 'kudino_categories', $cats );
}

/**
 * Plant nutrients with their visible deficiency sign — the "shop by symptom" table.
 * [symbol, atomic number, Persian name, sign, data colour].
 */
function kudino_elements() {
	$els = array(
		array( 'N', 7, 'نیتروژن', 'زردی یکنواخت برگ‌های پیر', 'var(--kd-n)' ),
		array( 'P', 15, 'فسفر', 'ارغوانی شدن برگ، ریشه ضعیف', 'var(--kd-p)' ),
		array( 'K', 19, 'پتاسیم', 'سوختگی حاشیه برگ، میوه ریز', 'var(--kd-k)' ),
		array( 'Ca', 20, 'کلسیم', 'پوسیدگی گلگاه گوجه و فلفل', '' ),
		array( 'Mg', 12, 'منیزیم', 'زردی بین رگبرگ برگ‌های پیر', '' ),
		array( 'S', 16, 'گوگرد', 'زردی برگ‌های جوان، ساقه باریک', '' ),
		array( 'Fe', 26, 'آهن', 'زردی بین رگبرگ برگ‌های جوان', '' ),
		array( 'Zn', 30, 'روی', 'برگ‌های کوچک و روزت‌شده', '' ),
		array( 'Mn', 25, 'منگنز', 'لکه‌های زرد ریز بین رگبرگ', '' ),
		array( 'B', 5, 'بُر', 'ریزش گل، ترک‌خوردگی میوه', '' ),
	);
	return apply_filters( 'kudino_elements', $els );
}

/**
 * Crop almanac. Months are Jalali (1 = فروردین … 12 = اسفند).
 * Each step: [month, stage, product, nutrient code].
 */
function kudino_crop_guides() {
	$guides = array(
		'orchard'    => array(
			'label' => 'باغ میوه',
			'note'  => 'بیشترین نیاز درخت از گلدهی تا درشت شدن میوه است. کود دامی را زمستان در چالکود بدهید و پتاسیم را از خرداد بالا ببرید.',
			'steps' => array(
				array( 10, 'چالکود زمستانه', 'کود دامی پوسیده + گوگرد', 'O' ),
				array( 12, 'بیدار شدن جوانه', 'اوره ۴۶٪', 'N' ),
				array( 1, 'گلدهی', 'NPK 10-52-10', 'P' ),
				array( 3, 'تشکیل میوه', 'سولفات پتاسیم', 'K' ),
				array( 4, 'زردی برگ', 'کلات آهن EDDHA', 'Fe' ),
				array( 8, 'پس از برداشت', 'هیومیک اسید', 'O' ),
			),
		),
		'pistachio'  => array(
			'label' => 'پسته',
			'note'  => 'باغ‌های پسته اغلب در خاک شور و آهکی‌اند. روی و آهن را جدی بگیرید و کود ازته را پیش از آبیاری بدهید تا شوری بالا نرود.',
			'steps' => array(
				array( 11, 'چالکود', 'کود دامی + گوگرد کشاورزی', 'O' ),
				array( 12, 'تورم جوانه', 'اوره ۴۶٪', 'N' ),
				array( 2, 'محلول‌پاشی', 'سولفات روی', 'Zn' ),
				array( 3, 'مغز بستن', 'سولفات پتاسیم', 'K' ),
				array( 4, 'پر شدن مغز', 'نیترات کلسیم', 'Ca' ),
				array( 7, 'پس از برداشت', 'NPK 20-20-20', 'N' ),
			),
		),
		'wheat'      => array(
			'label' => 'گندم و جو',
			'note'  => 'غلات به نیتروژن تقسیط‌شده بهتر جواب می‌دهند: یک‌سوم اوره هنگام کاشت، بقیه در پنجه‌زنی و ساقه‌دهی.',
			'steps' => array(
				array( 7, 'پیش از کاشت', 'سوپرفسفات تریپل', 'P' ),
				array( 8, 'کاشت', 'اوره (یک‌سوم)', 'N' ),
				array( 12, 'پنجه‌زنی', 'اوره + سولفات روی', 'N' ),
				array( 1, 'ساقه‌دهی', 'اوره (باقی‌مانده)', 'N' ),
				array( 2, 'پر شدن دانه', 'محلول‌پاشی پتاسیم', 'K' ),
			),
		),
		'rice'       => array(
			'label' => 'برنج',
			'note'  => 'کود را روی آب راکد پخش کنید و تا ۴۸ ساعت آب را خارج نکنید تا کود شسته نشود.',
			'steps' => array(
				array( 12, 'خزانه', 'کود دامی + فسفات', 'O' ),
				array( 2, 'نشاکاری', 'اوره (نیمی)', 'N' ),
				array( 3, 'پنجه‌زنی', 'سولفات پتاسیم', 'K' ),
				array( 4, 'آبستنی', 'اوره (باقی‌مانده)', 'N' ),
				array( 5, 'دانه‌بندی', 'محلول‌پاشی پتاسیم', 'K' ),
			),
		),
		'vegetable'  => array(
			'label' => 'گوجه و صیفی',
			'note'  => 'گوجه و خیار به کلسیم و پتاسیم حساس‌اند. محلول‌پاشی را صبح زود یا عصر انجام دهید تا برگ نسوزد.',
			'steps' => array(
				array( 12, 'آماده‌سازی بستر', 'ورمی‌کمپوست', 'O' ),
				array( 1, 'نشاکاری', 'هیومیک اسید', 'O' ),
				array( 2, 'رشد رویشی', 'NPK 20-20-20', 'N' ),
				array( 3, 'گلدهی', 'NPK 10-52-10', 'P' ),
				array( 4, 'میوه‌دهی', 'کلسیم + بُر', 'Ca' ),
				array( 5, 'درشت شدن میوه', 'پتاسیم بالا 12-12-36', 'K' ),
			),
		),
		'houseplant' => array(
			'label' => 'گلدان',
			'note'  => 'بهار و تابستان هر دو هفته، پاییز و زمستان ماهی یک‌بار. کود گلدان را همیشه رقیق‌تر از دستور روی بسته بدهید.',
			'steps' => array(
				array( 12, 'تعویض گلدان', 'خاک برگ + ورمی', 'O' ),
				array( 1, 'شروع رشد', 'کود مایع 7-7-7', 'N' ),
				array( 3, 'برگ‌دهی', 'کود مایع 7-7-7', 'N' ),
				array( 5, 'گیاهان گل‌دار', 'کود فسفر بالا', 'P' ),
				array( 8, 'آماده‌سازی استراحت', 'پتاسیم رقیق', 'K' ),
			),
		),
	);
	return apply_filters( 'kudino_crop_guides', $guides );
}

/**
 * Index of the step whose month is now or next, relative to the current Jalali month.
 */
function kudino_next_step_index( $steps, $month ) {
	$best = 0;
	$dist = 13;
	foreach ( $steps as $i => $s ) {
		$d = ( $s[0] - $month + 12 ) % 12;
		if ( $d < $dist ) {
			$dist = $d;
			$best = $i;
		}
	}
	return $best;
}

/**
 * What a grower should be doing this month (almanac strip).
 */
function kudino_month_task( $month ) {
	$tasks = array(
		1  => 'کود گلدهی درختان و تقسیط دوم اوره گندم',
		2  => 'محلول‌پاشی روی و پتاسیم در باغ و مزرعه',
		3  => 'پتاسیم برای تشکیل میوه',
		4  => 'کلسیم و آهن؛ مراقب زردی برگ باشید',
		5  => 'محلول‌پاشی پتاسیم برای درشت شدن میوه',
		6  => 'آزمایش خاک پیش از کشت پاییزه',
		7  => 'فسفر پیش از کاشت گندم',
		8  => 'کود پس از برداشت باغ و اوره کاشت گندم',
		9  => 'آماده‌سازی کود دامی برای چالکود',
		10 => 'چالکود زمستانه درختان',
		11 => 'گوگرد و کود دامی پای درخت',
		12 => 'اوره بیدار شدن جوانه و پنجه‌زنی گندم',
	);
	return isset( $tasks[ $month ] ) ? $tasks[ $month ] : '';
}

/**
 * Demo products (used until WooCommerce has products).
 */
function kudino_demo_products() {
	$products = array(
		array( 'group' => 'chemical', 'title' => 'کود کامل NPK ۲۰-۲۰-۲۰ پودری محلول', 'cat' => 'شیمیایی', 'weight' => '۱ کیلوگرم', 'npk' => array( 20, 20, 20 ), 'price' => 285000, 'old_price' => 320000, 'rating' => '4.8', 'pack' => array( 'pouch', '#3c7a57', '20-20-20', 'کود کامل' ) ),
		array( 'group' => 'organic', 'title' => 'ورمی‌کمپوست غنی‌شده با هیومیک', 'cat' => 'آلی', 'weight' => '۱۰ کیلوگرم', 'npk' => array( 2, 1, 1 ), 'price' => 190000, 'rating' => '4.9', 'pack' => array( 'sack', '#6a5741', 'VERMI', 'ورمی‌کمپوست' ), 'badge' => 'ارگانیک' ),
		array( 'group' => 'liquid', 'title' => 'کود مایع جلبک دریایی', 'cat' => 'محلول‌پاشی', 'weight' => '۱ لیتر', 'npk' => array( 1, 0, 17 ), 'price' => 260000, 'rating' => '4.7', 'pack' => array( 'jug', '#4b6a82', 'SEAWEED', 'عصاره جلبک' ), 'stock' => 'low', 'stock_qty' => 3 ),
		array( 'group' => 'organic', 'title' => 'هیومیک اسید ۸۵٪ پودری', 'cat' => 'اصلاح خاک', 'weight' => '۱ کیلوگرم', 'spec' => 'هیومیک ۸۵٪ · فولویک ۱۵٪ · محلول در آب', 'price' => 340000, 'old_price' => 395000, 'rating' => '4.6', 'pack' => array( 'pouch', '#6a5741', 'HUMIC 85', 'هیومیک اسید' ) ),
		array( 'group' => 'chemical', 'title' => 'کود فسفر بالا ۱۰-۵۲-۱۰ برای گلدهی', 'cat' => 'شیمیایی', 'weight' => '۱ کیلوگرم', 'npk' => array( 10, 52, 10 ), 'price' => 310000, 'rating' => '4.7', 'pack' => array( 'pouch', '#a87720', '10-52-10', 'گلدهی' ) ),
		array( 'group' => 'liquid', 'title' => 'کود مایع گیاهان آپارتمانی ۷-۷-۷', 'cat' => 'آپارتمانی', 'weight' => '۵۰۰ میلی‌لیتر', 'npk' => array( 7, 7, 7 ), 'price' => 98000, 'rating' => '4.9', 'pack' => array( 'jug', '#3c7a57', '7-7-7', 'گلدان' ), 'badge' => 'تازه' ),
		array( 'group' => 'chemical', 'title' => 'سولفات پتاسیم ۵۰٪ گرانوله', 'cat' => 'شیمیایی', 'weight' => '۲۵ کیلوگرم', 'npk' => array( 0, 0, 50 ), 'price' => 1450000, 'old_price' => 1600000, 'rating' => '4.5', 'pack' => array( 'sack', '#9b4630', 'K2O 50%', 'سولفات پتاسیم' ) ),
		array( 'group' => 'organic', 'title' => 'کود زیستی فسفات بارور ۲', 'cat' => 'زیستی', 'weight' => '۱۰۰ گرم', 'spec' => 'باکتری حل‌کننده فسفات · برای ۱ هکتار', 'price' => 85000, 'rating' => '4.4', 'pack' => array( 'pouch', '#1f4a33', 'BIO-P', 'فسفات بارور' ) ),
	);
	foreach ( $products as &$p ) {
		$p['url'] = kudino_search_url( $p['title'] );
	}
	unset( $p );
	return apply_filters( 'kudino_demo_products', $products );
}

/**
 * Demo journal entries when the blog is empty.
 */
function kudino_demo_posts() {
	return array(
		array( 'title' => 'سه عدد روی بسته کود چه می‌گویند؟', 'cat' => 'آموزش', 'time' => '۴ دقیقه', 'excerpt' => 'عدد اول نیتروژن است، دوم فسفر و سوم پتاسیم. یاد بگیرید هر مرحله رشد کدام را بیشتر می‌خواهد و چرا ۱۰-۵۲-۱۰ کود گلدهی است.' ),
		array( 'title' => 'کود شیمیایی یا آلی؟ برای باغ کوچک کدام بهتر است', 'cat' => 'راهنمای خرید', 'time' => '۶ دقیقه', 'excerpt' => 'سرعت اثر، قیمت هر هکتار و ماندگاری در خاک را کنار هم گذاشتیم.' ),
		array( 'title' => 'تقویم چالکود: کی، چقدر و با چه کودی', 'cat' => 'تقویم باغبانی', 'time' => '۵ دقیقه', 'excerpt' => 'چالکود زمستانه ذخیره غذایی درخت برای بهار است. فاصله از تنه و عمق چاله را اشتباه نکنید.' ),
	);
}

/**
 * The product shown as a printed analysis label in the hero.
 * Picks the first product with a substantial N-P-K analysis.
 */
function kudino_featured_label( $products ) {
	$pick = null;
	foreach ( $products as $p ) {
		if ( ! empty( $p['npk'] ) && array_sum( $p['npk'] ) > 30 ) {
			$pick = $p;
			break;
		}
	}
	if ( ! $pick ) {
		$pick = kudino_demo_products()[0];
	}
	$npk   = array_values( $pick['npk'] );
	$label = array(
		'name'      => 'کود کامل متعادل',
		'sub'       => 'پودری، ۱۰۰٪ محلول در آب · برای کودآبیاری و محلول‌پاشی',
		'npk'       => $npk,
		'sku'       => 'KD-' . implode( '', $npk ) . '-1KG',
		'dose'      => array(
			array( 'محلول‌پاشی', '۲ تا ۳ گرم در هر لیتر آب' ),
			array( 'کودآبیاری', '۵ تا ۱۰ کیلوگرم در هکتار' ),
			array( 'دفعات', 'هر ۱۰ تا ۱۴ روز، تا ۴ نوبت' ),
		),
		'price'     => isset( $pick['price'] ) ? $pick['price'] : 0,
		'old_price' => isset( $pick['old_price'] ) ? $pick['old_price'] : 0,
		'weight'    => isset( $pick['weight'] ) ? $pick['weight'] : '',
		'add_url'   => ( isset( $pick['add_url'] ) && '#' !== $pick['add_url'] ) ? $pick['add_url'] : ( isset( $pick['url'] ) ? $pick['url'] : '#' ),
	);
	return apply_filters( 'kudino_featured_label', $label, $pick );
}
