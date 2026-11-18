#!/usr/bin/env bash
# Runs ON THE SERVER (as root). One-time WordPress install + Kudino defaults. Idempotent.
# The admin password is generated here and written ONLY to /root/<app>-wp-admin.txt (0600).
set -euo pipefail
: "${APP:?}" "${APP_ROOT:?}" "${DOMAIN:?}" "${POOL_USER:?}" "${ADMIN_USER:?}" "${ADMIN_EMAIL:?}"
[[ "$ADMIN_USER" =~ ^[a-z][a-z0-9_.-]{2,30}$ ]] || { echo "bad ADMIN_USER" >&2; exit 1; }
[[ "$ADMIN_EMAIL" =~ ^[^@[:space:]\']+@[^@[:space:]\']+\.[a-z]{2,}$ ]] || { echo "bad ADMIN_EMAIL" >&2; exit 1; }
say() { printf '[server] %s\n' "$*"; }
[ -L "$APP_ROOT/current" ] || { echo "deploy first (no current release)" >&2; exit 1; }

cd "$APP_ROOT"   # WP-CLI spawns subprocesses; the pool user cannot enter /root
wp() { runuser -u "$POOL_USER" -- env HOME="$APP_ROOT/shared/tmp" WP_CLI_CACHE_DIR="$APP_ROOT/shared/tmp/wp-cli-cache" \
	php -d memory_limit=512M "$APP_ROOT/bin/wp-cli.phar" --path="$APP_ROOT/current" "$@"; }

SECRET="/root/$APP-wp-admin.txt"
if wp core is-installed 2>/dev/null; then
	say "WordPress is already installed — not touching users or passwords"
else
	say "Installing WordPress for https://$DOMAIN (admin: $ADMIN_USER)"
	pw="$(openssl rand -base64 48 | tr -dc 'A-Za-z0-9' | head -c 24)"
	printf '%s' "$pw" | wp core install --url="https://$DOMAIN" --title="کودینو" \
		--admin_user="$ADMIN_USER" --admin_email="$ADMIN_EMAIL" --prompt=admin_password --skip-email >/dev/null
	( umask 077; printf 'WordPress admin for https://%s/wp-admin/\nuser: %s\npassword: %s\ncreated: %s\n' "$DOMAIN" "$ADMIN_USER" "$pw" "$(date -u +%FT%TZ)" > "$SECRET" )
	unset pw
	say "Admin credentials written to $SECRET (root only). Read them with: cat $SECRET — then change the password in wp-admin and delete the file."
fi

say "Applying Kudino defaults"
wp language core is-installed fa_IR 2>/dev/null && wp site switch-language fa_IR >/dev/null && say "Site language: fa_IR" || say "fa_IR pack not bundled — language left as is"
wp theme activate kudino >/dev/null && say "Theme: kudino"
if ! wp plugin is-active woocommerce 2>/dev/null; then
	wp plugin activate woocommerce >/dev/null && say "Plugin: woocommerce activated" || say "WARNING: WooCommerce could not be activated (see the message above)"
fi
wp option update timezone_string "Asia/Tehran" >/dev/null
wp option update blogdescription "فروشگاه تخصصی کود" >/dev/null
wp option update date_format "j F Y" >/dev/null
wp rewrite structure '/%postname%/' >/dev/null
wp option update woocommerce_default_country "IR" >/dev/null 2>&1 || true
wp option update woocommerce_currency "IRT" >/dev/null 2>&1 || true
wp option update woocommerce_price_thousand_sep "٬" >/dev/null 2>&1 || true
wp option update woocommerce_price_num_decimals "0" >/dev/null 2>&1 || true
case "${SEARCH_INDEXING:-0}" in
	1) wp option update blog_public 1 >/dev/null && say "Search engines: indexing ALLOWED" ;;
	*) wp option update blog_public 0 >/dev/null && say "Search engines: noindex (blog_public=0 + X-Robots-Tag)" ;;
esac
say "Done: $(wp option get home) — theme $(wp theme list --status=active --field=name)"
