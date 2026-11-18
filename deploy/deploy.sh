#!/usr/bin/env bash
# Kudino deploy — run from the repository root in Git Bash on the laptop.
#   bash deploy/deploy.sh               build locally → upload → unpack release → DB updates → switch → health checks
#   bash deploy/deploy.sh --build-only  just build the tarball into deploy/.build/
#   bash deploy/deploy.sh --list        show releases on the server (* = current)
#   bash deploy/deploy.sh --rollback [<release>]   switch back (default: the previous release)
# Nothing is built on the server.
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$DIR/.." && pwd)"
. "$DIR/config.sh"; . "$DIR/lib.sh"

CACHE="$DIR/.cache"; BUILD="$DIR/.build"
mkdir -p "$CACHE" "$BUILD"

remote_mode() { # remote_mode <MODE> [extra env…]
	remote "$(remote_env) $* bash -s" < "$DIR/server/deploy-remote.sh"
}

fetch() { # fetch <url> <file> — cached download
	[ -s "$2" ] && return 0
	say "Downloading $(basename "$2")"
	curl -fsSL --retry 3 --connect-timeout 20 -o "$2.part" "$1" || { rm -f "$2.part"; return 1; }
	mv "$2.part" "$2"
}

unzip_to() { # unzip_to <zip> <dir>
	mkdir -p "$2"
	if command -v unzip >/dev/null 2>&1; then
		unzip -q -o "$1" -d "$2"
	elif command -v powershell.exe >/dev/null 2>&1; then
		powershell.exe -NoProfile -Command "Expand-Archive -Force -LiteralPath '$(cygpath -w "$1")' -DestinationPath '$(cygpath -w "$2")'"
	else
		die "need unzip or PowerShell to extract $1"
	fi
}

build() {
	TS="$(date +%Y%m%d%H%M%S)"
	local rel="$BUILD/release" tmp="$BUILD/tmp"
	rm -rf "$rel" "$tmp"; mkdir -p "$rel" "$tmp"

	# --- WordPress core (checksum-verified; "latest" resolves to the newest stable release)
	if [ "$WP_VERSION" = "latest" ]; then
		WP_VERSION="$(curl -fsSL https://api.wordpress.org/core/version-check/1.7/ | tr "," "\n" | sed -n 's/.*"current":"\([0-9.]*\)".*/\1/p' | head -1)"
		[ -n "$WP_VERSION" ] || die "could not resolve the latest WordPress version"
		warn "WordPress latest = $WP_VERSION (pin WP_VERSION=\"$WP_VERSION\" for repeatable builds)"
	fi
	local wp_tgz="$CACHE/wordpress-$WP_VERSION.tar.gz"
	fetch "https://wordpress.org/wordpress-$WP_VERSION.tar.gz" "$wp_tgz"
	local want; want="$(curl -fsSL "https://wordpress.org/wordpress-$WP_VERSION.tar.gz.sha1")"
	[ "$(sha1sum "$wp_tgz" | cut -d' ' -f1)" = "$want" ] || { rm -f "$wp_tgz"; die "WordPress checksum mismatch"; }
	tar -xzf "$wp_tgz" -C "$tmp"
	cp -R "$tmp/wordpress/." "$rel/"
	rm -rf "$rel/wp-content/plugins/akismet" "$rel/wp-content/plugins/hello.php" "$rel/wp-config-sample.php" "$rel/readme.html" "$rel/license.txt"
	find "$rel/wp-content/themes" -mindepth 1 -maxdepth 1 -type d -name 'twenty*' ! -name 'twentytwentyfive' -exec rm -rf {} +

	# --- WooCommerce (pinned, or resolve latest-stable to an exact version)
	local wc="$WC_VERSION"
	if [ "$wc" = "latest-stable" ]; then
		wc="$(curl -fsSL https://api.wordpress.org/plugins/info/1.0/woocommerce.json | sed -n 's/.*"version":"\([0-9.]*\)".*/\1/p' | head -1)"
		[ -n "$wc" ] || die "could not resolve WooCommerce latest-stable"
		warn "WooCommerce latest-stable = $wc (pin WC_VERSION=\"$wc\" in config.local.sh for repeatable builds)"
	fi
	local wc_zip="$CACHE/woocommerce.$wc.zip"
	fetch "https://downloads.wordpress.org/plugin/woocommerce.$wc.zip" "$wc_zip"
	unzip_to "$wc_zip" "$rel/wp-content/plugins"

	# --- Persian language packs (optional; the site works without them)
	if [ "$INCLUDE_FA" = "1" ]; then
		mkdir -p "$rel/wp-content/languages/plugins"
		if fetch "https://downloads.wordpress.org/translation/core/$WP_VERSION/fa_IR.zip" "$CACHE/core-$WP_VERSION-fa_IR.zip"; then
			unzip_to "$CACHE/core-$WP_VERSION-fa_IR.zip" "$rel/wp-content/languages"
		else warn "No fa_IR core pack for $WP_VERSION — skipping"; fi
		if fetch "https://downloads.wordpress.org/translation/plugin/woocommerce/$wc/fa_IR.zip" "$CACHE/woocommerce-$wc-fa_IR.zip"; then
			unzip_to "$CACHE/woocommerce-$wc-fa_IR.zip" "$rel/wp-content/languages/plugins"
		else warn "No fa_IR WooCommerce pack for $wc — skipping"; fi
	fi

	# --- Kudino theme (from this repo) + wp-config stub
	cp -R "$ROOT/kudino" "$rel/wp-content/themes/kudino"
	cp "$DIR/server/wp-config.release.php" "$rel/wp-config.php"
	rm -rf "$rel/wp-content/uploads"

	# --- WP-CLI (checksum-verified), shipped in _deploy/ and moved outside the web root on the server
	local phar="$CACHE/wp-cli-$WPCLI_VERSION.phar"
	fetch "https://github.com/wp-cli/wp-cli/releases/download/v$WPCLI_VERSION/wp-cli-$WPCLI_VERSION.phar" "$phar"
	want="$(curl -fsSL "https://github.com/wp-cli/wp-cli/releases/download/v$WPCLI_VERSION/wp-cli-$WPCLI_VERSION.phar.sha512" | cut -d' ' -f1)"
	[ "$(sha512sum "$phar" | cut -d' ' -f1)" = "$want" ] || { rm -f "$phar"; die "WP-CLI checksum mismatch"; }
	mkdir -p "$rel/_deploy"; cp "$phar" "$rel/_deploy/wp-cli.phar"

	# --- release stamp (hidden; Nginx denies dotfiles)
	local rev; rev="$(git -C "$ROOT" rev-parse --short HEAD 2>/dev/null || echo unknown)"
	git -C "$ROOT" diff --quiet 2>/dev/null || { rev="$rev-dirty"; warn "Uncommitted changes are included in this build"; }
	printf 'git %s · WordPress %s · WooCommerce %s · built %s\n' "$rev" "$WP_VERSION" "$wc" "$(date -u +%FT%TZ)" > "$rel/.kudino-release"

	TARBALL="$BUILD/$APP-$TS.tar.gz"
	tar -C "$rel" -czf "$TARBALL" .
	rm -rf "$tmp"
	say "Built $(basename "$TARBALL") ($(du -h "$TARBALL" | cut -f1)) — $(cat "$rel/.kudino-release")"
}

case "${1:-}" in
--list)     require_domain; remote_mode MODE=list ;;
--rollback) require_domain; remote_mode MODE=rollback ${2:+RELEASE=$2} ;;
--build-only) build ;;
"")
	require_domain
	build
	say "Uploading to $SERVER_IP:/tmp"
	push "$TARBALL" "/tmp/$APP-$TS.tar.gz"
	remote_mode MODE=deploy RELEASE="$TS" TARBALL="/tmp/$APP-$TS.tar.gz"
	say "Checking from the laptop"
	printf '  %-28s %s\n' "https://$DOMAIN/" "$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "https://$DOMAIN/" || echo fail)"
	printf '  %-28s %s\n' "$SHOCK_HEALTH_URL" "$(curl -s --max-time 20 "$SHOCK_HEALTH_URL" | head -c 120)"
	ls -1t "$BUILD"/*.tar.gz 2>/dev/null | tail -n +4 | xargs -r rm -f
	;;
*) die "unknown option ${1}" ;;
esac
