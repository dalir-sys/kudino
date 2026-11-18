# ShelfItem

A product on the ruled shelf. No card, no shadow: cells are separated by `kd-rule` hairlines. PHP: `kudino_product_card( $data )`, and WooCommerce uses the same markup through `woocommerce/content-product.php`.

- Provide: `title`, `url`, `cat`, `weight`, `npk` or `spec`, `price`, `old_price`, `rating`, `stock`, `image` or `pack`, `add_url`.
- The image well is `kd-paper-2` at 1:1. Without a photo, a flat print-style pack drawing shows instead (`kudino_pack_svg`).
- The whole cell is clickable (the title stretches over it); the `+` button and the save heart sit above it.
- Price uses Persian digits with the «٬» separator, then «تومان» in `kd-ink-3`.
