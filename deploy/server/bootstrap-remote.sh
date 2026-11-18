#!/usr/bin/env bash
# Runs ON THE SERVER (as root), started by deploy/bootstrap.sh. Idempotent.
# Creates only Kudino's things: /var/www/<app>, its DB + DB user, its FPM pool, its Nginx site.
# Never touches Shock Support, nginx.conf, php.ini, MariaDB config, the firewall or SSH.
set -euo pipefail
umask 022

: "${APP:?}" "${APP_ROOT:?}" "${DOMAIN:?}" "${SERVER_NAMES:?}" "${PHP_VERSION:?}" "${POOL_USER:?}" "${POOL_MAX_CHILDREN:?}" "${DB_NAME:?}" "${DB_USER:?}" "${TABLE_PREFIX:?}" "${SHOCK_HEALTH_URL:?}"
TPL_DIR="$(cd "$(dirname "$0")" && pwd)"
DRY_RUN="${DRY_RUN:-0}"
CREATE_USER="${CREATE_USER:-0}"
STAMP="$(date +%Y%m%d-%H%M%S)"

say()  { printf '[server] %s\n' "$*"; }
die()  { printf '[server] ERROR: %s\n' "$*" >&2; exit 1; }
run()  { if [ "$DRY_RUN" = 1 ]; then printf '[dry-run] %s\n' "$*"; else "$@"; fi; }

# ---------------------------------------------------------------- validation
[ "$(id -u)" = 0 ] || die "must run as root"
[[ "$APP" =~ ^[a-z][a-z0-9_]{1,31}$ ]] || die "bad APP"
for other in shocksupport shock barcode; do
	[[ "$APP" != "$other"* && "$DB_NAME" != "$other"* && "$DB_USER" != "$other"* && "$POOL_USER" != "$other"* ]] || die "names may not start with '$other' (another site on this server)"
	[[ "$DOMAIN" != *"$other"* ]] || die "DOMAIN may not contain '$other'"
done
[[ "$POOL_USER" =~ ^[a-z][a-z0-9_-]{1,31}$ && "$POOL_USER" != root ]] || die "bad POOL_USER"
[[ "$APP_ROOT" == /var/www/"$APP" ]] || die "APP_ROOT must be /var/www/$APP"
[[ "$DOMAIN" =~ ^([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,}$ ]] || die "bad DOMAIN"
[[ "$DOMAIN" != *shocksupport.com ]] || die "DOMAIN may not be shocksupport.com"
[[ "$DB_NAME" =~ ^[a-z][a-z0-9_]{1,31}$ && "$DB_USER" =~ ^[a-z][a-z0-9_]{1,31}$ ]] || die "bad DB name/user"
[[ "$DB_NAME" != shock_support && "$DB_USER" != shock ]] || die "refusing Shock Support's DB/user"
[[ "$TABLE_PREFIX" =~ ^[a-z0-9_]+_$ ]] || die "bad TABLE_PREFIX"
[[ "$POOL_MAX_CHILDREN" =~ ^[1-4]$ ]] || die "POOL_MAX_CHILDREN must be 1-4"
[[ "$PHP_VERSION" =~ ^[0-9]\.[0-9]$ ]] || die "bad PHP_VERSION"

POOL_FILE="/etc/php/$PHP_VERSION/fpm/pool.d/$APP.conf"
SOCK="/run/php/php$PHP_VERSION-fpm-$APP.sock"
SITE_FILE="/etc/nginx/sites-available/$APP"
SITE_LINK="/etc/nginx/sites-enabled/$APP"
SHARED="$APP_ROOT/shared"

shock_ok() { curl -s --max-time 15 "$SHOCK_HEALTH_URL" | grep -q '"ok":true'; }
others_ok() { # every URL in OTHER_SITE_URLS must answer without a 5xx/timeout
	local u c ok=0
	for u in ${OTHER_SITE_URLS:-}; do
		c=$(curl -s --noproxy '*' -o /dev/null -w '%{http_code}' --max-time 15 "$u" || true)
		if [[ "$c" =~ ^[23] ]]; then say "Other site $u: HTTP $c"; else say "Other site $u: HTTP ${c:-000} (NOT OK)"; ok=1; fi
	done
	return $ok
}

fill() { # fill <template> -> stdout
	sed -e "s|__APP__|$APP|g" -e "s|__APP_ROOT__|$APP_ROOT|g" -e "s|__SERVER_NAMES__|$SERVER_NAMES|g" \
	    -e "s|__PHP_VERSION__|$PHP_VERSION|g" -e "s|__POOL_USER__|$POOL_USER|g" -e "s|__POOL_MAX_CHILDREN__|$POOL_MAX_CHILDREN|g" \
	    -e "s|__DB_NAME__|$DB_NAME|g" -e "s|__DB_USER__|$DB_USER|g" -e "s|__TABLE_PREFIX__|$TABLE_PREFIX|g" -e "s|__DOMAIN__|$DOMAIN|g" "$1"
}

# ---------------------------------------------------------------- 0. preflight
say "Preflight"
for s in nginx "php$PHP_VERSION-fpm" mariadb; do systemctl is-active --quiet "$s" || die "$s is not active"; done
shock_ok || die "Shock Support health check fails BEFORE any change — stopping, nothing touched"
others_ok || die "another site on this server is unhealthy BEFORE any change — stopping, nothing touched"
missing=()
for e in mysqli curl gd mbstring xml dom zip intl; do php -m | grep -qix "$e" || missing+=("$e"); done
if [ "${#missing[@]}" -gt 0 ]; then
	pk=""; for e in "${missing[@]}"; do case "$e" in mysqli) pk+=" php$PHP_VERSION-mysql";; xml|dom) pk+=" php$PHP_VERSION-xml";; *) pk+=" php$PHP_VERSION-$e";; esac; done
	die "PHP extensions missing: ${missing[*]}. Ask the owner, then: apt-get install$(printf '%s\n' $pk | sort -u | tr '\n' ' ')"
fi
if ! id "$POOL_USER" >/dev/null 2>&1; then
	[ "$CREATE_USER" = 1 ] || die "Linux user '$POOL_USER' does not exist. Re-run with --create-user only after the owner approved it."
	say "Creating system user $POOL_USER (no shell, no home login)"
	run useradd --system --user-group --no-create-home --home-dir "$APP_ROOT" --shell /usr/sbin/nologin "$POOL_USER"
fi
[ -e "$SITE_FILE" ] && ! grep -q "Kudino" "$SITE_FILE" && die "$SITE_FILE exists and is not ours"

# ---------------------------------------------------------------- 1. folders
say "Folders under $APP_ROOT"
run install -d -m 755 -o root -g root "$APP_ROOT" "$APP_ROOT/releases"
run install -d -m 755 -o root -g root "$SHARED"   # Nginx (www-data) must traverse it to serve uploads; secrets are protected per file
run install -d -m 755 -o "$POOL_USER" -g "$POOL_USER" "$SHARED/uploads"
run install -d -m 700 -o "$POOL_USER" -g "$POOL_USER" "$SHARED/tmp"
[ "$DRY_RUN" = 1 ] || { chmod 755 "$SHARED"; chown root:root "$SHARED"; }

# ---------------------------------------------------------------- 2. database + shared/wp-config.php
CFG="$SHARED/wp-config.php"
db_exists=$(mysql -N -e "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='$DB_NAME'")
if [ -f "$CFG" ]; then
	say "Config exists ($CFG) — keeping database, user and secrets as they are"
	[ "$db_exists" = 1 ] || die "config exists but database '$DB_NAME' does not"
else
	[ "$db_exists" = 0 ] || die "database '$DB_NAME' exists but $CFG does not — refusing to guess; check manually"
	user_exists=$(mysql -N -e "SELECT COUNT(*) FROM mysql.user WHERE User='$DB_USER'")
	[ "$user_exists" = 0 ] || die "DB user '$DB_USER' already exists — refusing to reset its password"
	say "Creating database $DB_NAME and user $DB_USER@localhost (password generated here, written only to $CFG)"
	if [ "$DRY_RUN" = 1 ]; then
		echo "[dry-run] CREATE DATABASE $DB_NAME; CREATE USER $DB_USER@localhost; GRANT ALL ON $DB_NAME.*; write $CFG"
	else
		pw="$(openssl rand -base64 48 | tr -dc 'A-Za-z0-9' | head -c 40)"
		[ "${#pw}" -ge 32 ] || die "password generation failed"
		mysql <<SQL
CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$pw';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL
		salts=""
		for k in AUTH_KEY SECURE_AUTH_KEY LOGGED_IN_KEY NONCE_KEY AUTH_SALT SECURE_AUTH_SALT LOGGED_IN_SALT NONCE_SALT; do
			salts+="define( '$k', '$(openssl rand -base64 64 | tr -dc 'A-Za-z0-9!#%*+,.:;=?@^_~' | head -c 64)' );"$'\n'
		done
		tmp="$(mktemp "$SHARED/.wp-config.XXXXXX")"
		fill "$TPL_DIR/wp-config.shared.php" | PW="$pw" SALTS="$salts" php -r '
			$s = stream_get_contents(STDIN);
			$s = str_replace("__DB_PASSWORD__", getenv("PW"), $s);
			$s = str_replace("__SALTS__", rtrim(getenv("SALTS")), $s);
			echo $s;' > "$tmp"
		unset pw salts
		php -l "$tmp" >/dev/null || { rm -f "$tmp"; die "generated wp-config.php has a syntax error"; }
		chown root:"$POOL_USER" "$tmp"; chmod 640 "$tmp"; mv "$tmp" "$CFG"
		say "Wrote $CFG (0640 root:$POOL_USER)"
	fi
fi

# ---------------------------------------------------------------- 3. PHP-FPM pool
say "PHP-FPM pool $POOL_FILE"
new_pool="$(mktemp)"; fill "$TPL_DIR/php-fpm-kudino.conf" > "$new_pool"
if [ -f "$POOL_FILE" ] && cmp -s "$new_pool" "$POOL_FILE"; then
	say "Pool unchanged"
else
	[ -f "$POOL_FILE" ] && run cp -p "$POOL_FILE" "/root/$APP-pool.conf.backup-$STAMP"
	run install -m 644 "$new_pool" "$POOL_FILE"
	if [ "$DRY_RUN" != 1 ]; then
		if ! "php-fpm$PHP_VERSION" -t >/dev/null 2>&1; then
			if [ -f "/root/$APP-pool.conf.backup-$STAMP" ]; then cp -p "/root/$APP-pool.conf.backup-$STAMP" "$POOL_FILE"; else rm -f "$POOL_FILE"; fi
			"php-fpm$PHP_VERSION" -t || true
			die "php-fpm config test failed — pool change undone"
		fi
		systemctl reload "php$PHP_VERSION-fpm"
		for _ in 1 2 3 4 5 6 7 8 9 10; do [ -S "$SOCK" ] && break; sleep 1; done
		[ -S "$SOCK" ] || die "socket $SOCK did not appear after reload"
		say "Pool active on $SOCK"
	fi
fi
rm -f "$new_pool"

# ---------------------------------------------------------------- 4. Nginx site
say "Nginx site $SITE_FILE"
if [ -f "$SITE_FILE" ]; then
	say "Site file exists — NOT overwriting (Certbot may have added HTTPS to it). Delete it by hand to regenerate."
else
	run install -m 644 <(fill "$TPL_DIR/nginx-kudino.conf") "$SITE_FILE"
fi
[ -L "$SITE_LINK" ] || run ln -s "$SITE_FILE" "$SITE_LINK"
if [ "$DRY_RUN" != 1 ]; then
	if ! nginx -t 2>/tmp/$APP-nginx-test.log; then
		cat /tmp/$APP-nginx-test.log >&2
		rm -f "$SITE_LINK"
		nginx -t >/dev/null 2>&1 && say "Disabled our site again; nginx config is valid without it"
		die "nginx -t failed — our site link removed, nothing reloaded"
	fi
	systemctl reload nginx
	say "Nginx reloaded"
fi

# ---------------------------------------------------------------- 5. verify
if [ "$DRY_RUN" != 1 ]; then
	shock_ok && say "Shock Support health: OK" || die "Shock Support health check FAILED after our changes — investigate now"
	others_ok || die "another site's check FAILED after our changes — investigate now"
	code=$(curl -s --noproxy '*' -o /dev/null -w '%{http_code}' -H "Host: $DOMAIN" http://127.0.0.1/ || true)
	say "Kudino over HTTP (no release deployed yet, 404 is expected): $code"
fi
say "Bootstrap finished."
