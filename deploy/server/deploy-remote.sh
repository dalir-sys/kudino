#!/usr/bin/env bash
# Runs ON THE SERVER (as root), fed by deploy/deploy.sh over SSH.
#   MODE=deploy   RELEASE=<ts> TARBALL=/tmp/<app>-<ts>.tar.gz
#   MODE=rollback [RELEASE=<ts>]   (default: the release before current)
#   MODE=list
set -euo pipefail
umask 022
: "${APP:?}" "${APP_ROOT:?}" "${DOMAIN:?}" "${PHP_VERSION:?}" "${POOL_USER:?}" "${KEEP_RELEASES:?}" "${SHOCK_HEALTH_URL:?}" "${MODE:?}"
[[ "$APP_ROOT" == /var/www/"$APP" && "$APP" != shocksupport* && "$APP" != barcode* && "$APP" != velingo* ]] || { echo "bad APP_ROOT" >&2; exit 1; }

R="$APP_ROOT/releases"; CUR="$APP_ROOT/current"; SHARED="$APP_ROOT/shared"
WP="$APP_ROOT/bin/wp-cli.phar"
say() { printf '[server] %s\n' "$*"; }
die() { printf '[server] ERROR: %s\n' "$*" >&2; exit 1; }

cd "$APP_ROOT"   # WP-CLI spawns subprocesses; the pool user cannot enter /root
exec 9>"$APP_ROOT/.deploy.lock"
flock -n 9 || die "another deploy is running"

wp() { # wp <args…> — run WP-CLI as the pool user against a release dir in $WPPATH
	runuser -u "$POOL_USER" -- env HOME="$SHARED/tmp" WP_CLI_CACHE_DIR="$SHARED/tmp/wp-cli-cache" \
		php -d memory_limit=512M "$WP" --path="$WPPATH" "$@"
}

health() { # prints HTTP status of the homepage through the local nginx
	local code
	if [ -d "/etc/letsencrypt/live/$APP" ]; then
		code=$(curl -s --noproxy '*' -o /tmp/$APP-health.html -w '%{http_code}' --max-time 30 --resolve "$DOMAIN:443:127.0.0.1" "https://$DOMAIN/" || true)
	else
		code=$(curl -s --noproxy '*' -o /tmp/$APP-health.html -w '%{http_code}' --max-time 30 -H "Host: $DOMAIN" "http://127.0.0.1/" || true)
	fi
	if grep -qi "error establishing a database connection\|critical error" /tmp/$APP-health.html 2>/dev/null; then code="500"; fi
	rm -f /tmp/$APP-health.html
	echo "$code"
}
healthy() { case "$1" in 200|301|302) return 0;; *) return 1;; esac; }
shock_ok() { curl -s --max-time 15 "$SHOCK_HEALTH_URL" | grep -q '"ok":true'; }
others_ok() {
	local u c ok=0
	for u in ${OTHER_SITE_URLS:-}; do
		c=$(curl -s --noproxy '*' -o /dev/null -w '%{http_code}' --max-time 15 "$u" || true)
		if [[ "$c" =~ ^[23] ]]; then say "Other site $u: HTTP $c"; else say "Other site $u: HTTP ${c:-000} (NOT OK)"; ok=1; fi
	done
	return $ok
}

switch_to() { # atomic symlink swap + FPM reload
	ln -sfn "releases/$1" "$APP_ROOT/.current.tmp"
	mv -Tf "$APP_ROOT/.current.tmp" "$CUR"
	systemctl reload "php$PHP_VERSION-fpm"
}

current_release() { [ -L "$CUR" ] && basename "$(readlink "$CUR")" || true; }

list() {
	local cur; cur="$(current_release)"
	for d in $(ls -1 "$R" 2>/dev/null | sort); do
		printf '%s %s %s\n' "$([ "$d" = "$cur" ] && echo '*' || echo ' ')" "$d" "$(cat "$R/$d/.kudino-release" 2>/dev/null | head -1)"
	done
}

case "$MODE" in
list) list; exit 0 ;;

rollback)
	cur="$(current_release)"
	target="${RELEASE:-$(ls -1 "$R" | sort | awk -v c="$cur" '$0 < c' | tail -1)}"
	[ -n "$target" ] || die "no release older than the current one ($cur) to roll back to"
	[[ "$target" =~ ^[0-9]{14}$ ]] && [ -d "$R/$target" ] || die "release '$target' not found"
	say "Rolling back: $cur -> $target"
	switch_to "$target"
	code=$(health); say "Kudino health after rollback: HTTP $code"
	shock_ok && say "Shock Support health: OK" || die "Shock Support health FAILED"
	healthy "$code" || die "Kudino still unhealthy after rollback"
	exit 0 ;;

deploy)
	: "${RELEASE:?}" "${TARBALL:?}"
	[[ "$RELEASE" =~ ^[0-9]{14}$ ]] || die "bad RELEASE"
	[ -f "$SHARED/wp-config.php" ] || die "run deploy/bootstrap.sh first ($SHARED/wp-config.php missing)"
	[ -S "/run/php/php$PHP_VERSION-fpm-$APP.sock" ] || die "FPM pool socket missing — run deploy/bootstrap.sh"
	DEST="$R/$RELEASE"; [ ! -e "$DEST" ] || die "$DEST already exists"

	say "Unpacking release $RELEASE"
	mkdir -p "$DEST"
	DISCARD=1   # a release that fails before the switch is removed again
	trap '[ "${DISCARD:-0}" = 1 ] && rm -rf -- "$DEST" && echo "[server] Removed unfinished release $RELEASE"; rm -f -- "$TARBALL"' EXIT
	tar -xzf "$TARBALL" -C "$DEST" --no-same-owner --no-same-permissions
	chown -R root:root "$DEST"
	find "$DEST" -type d -exec chmod 755 {} +
	find "$DEST" -type f -exec chmod 644 {} +

	# Tools travel with the release but live outside the web root.
	if [ -f "$DEST/_deploy/wp-cli.phar" ]; then install -d -m 755 "$APP_ROOT/bin"; install -m 755 "$DEST/_deploy/wp-cli.phar" "$WP"; fi
	rm -rf "$DEST/_deploy"

	# Shared, persistent pieces.
	rm -rf "$DEST/wp-content/uploads"
	ln -s ../../../shared/uploads "$DEST/wp-content/uploads"
	php -l "$DEST/wp-config.php" >/dev/null || die "wp-config stub has a syntax error"

	# "Migrations": WordPress and WooCommerce database updates, run against the NEW code.
	WPPATH="$DEST"
	if wp core is-installed 2>/dev/null; then
		say "Running database updates"
		wp core update-db --quiet || die "wp core update-db failed (current release untouched)"
		if wp plugin is-active woocommerce 2>/dev/null; then
			if wp help wc update >/dev/null 2>&1; then
				wp wc update --quiet || die "WooCommerce DB update failed (current release untouched)"
			else
				say "WooCommerce CLI not available — WooCommerce will offer its DB update in wp-admin if one is needed"
			fi
		fi
	else
		say "WordPress is not installed yet — finish at https://$DOMAIN/wp-admin/install.php right after this deploy"
	fi

	prev="$(current_release)"
	say "Switching current -> $RELEASE (previous: ${prev:-none})"
	DISCARD=0
	switch_to "$RELEASE"

	code=$(health)
	if healthy "$code"; then
		say "Kudino health: HTTP $code"
	else
		if [ -n "$prev" ]; then
			say "Kudino health: HTTP $code — ROLLING BACK to $prev"
			switch_to "$prev"
			say "Rolled back. Kudino now: HTTP $(health)"
		fi
		die "deploy failed health check (HTTP $code); release kept at $DEST for inspection"
	fi
	shock_ok && say "Shock Support health: OK" || die "Shock Support health FAILED after deploy — investigate now"
	others_ok || die "another site's check FAILED after deploy — investigate now"

	# Keep the newest $KEEP_RELEASES (never the current one).
	cur="$(current_release)"
	ls -1 "$R" | sort | head -n -"$KEEP_RELEASES" | while read -r old; do
		[[ "$old" =~ ^[0-9]{14}$ ]] && [ "$old" != "$cur" ] && rm -rf -- "${R:?}/$old" && say "Pruned release $old"
	done
	say "Deployed $RELEASE"
	;;
*) die "unknown MODE $MODE" ;;
esac
