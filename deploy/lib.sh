# Shared helpers for the laptop-side scripts. Source after config.sh.
set -euo pipefail

say()  { printf '\033[1;32m==>\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33m!! \033[0m %s\n' "$*" >&2; }
die()  { printf '\033[1;31mxx \033[0m %s\n' "$*" >&2; exit 1; }

require_domain() {
	[ -n "${DOMAIN:-}" ] || die "DOMAIN is empty. Put DOMAIN=\"your-domain\" in deploy/config.local.sh"
	printf '%s' "$DOMAIN" | grep -Eq '^([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,}$' || die "DOMAIN '$DOMAIN' is not a valid lowercase domain"
	case "$DOMAIN" in *shocksupport*|*barcode*) die "Refusing to use another site's name ($DOMAIN) for Kudino";; esac
}

server_names() {
	if [ "${WITH_WWW:-1}" = "1" ]; then printf '%s www.%s' "$DOMAIN" "$DOMAIN"; else printf '%s' "$DOMAIN"; fi
}

ssh_opts() { printf '%s\n' -i "$SSH_KEY" -o IdentitiesOnly=yes -o BatchMode=yes -o ConnectTimeout=15 -o ServerAliveInterval=20; }

remote() { # remote <command…>   (uses ONLY the project key)
	[ -f "$SSH_KEY" ] || die "Key $SSH_KEY not found. Run deploy/setup-key.sh first."
	local opts; mapfile -t opts < <(ssh_opts)
	ssh "${opts[@]}" "$SSH_USER@$SERVER_IP" "$@"
}

push() { # push <local> <remote-path>
	local opts; mapfile -t opts < <(ssh_opts)
	scp -q "${opts[@]}" "$1" "$SSH_USER@$SERVER_IP:$2"
}

# Environment passed to remote scripts (values are validated before use).
remote_env() {
	printf "APP=%q APP_ROOT=%q DOMAIN=%q SERVER_NAMES=%q PHP_VERSION=%q POOL_USER=%q POOL_MAX_CHILDREN=%q DB_NAME=%q DB_USER=%q TABLE_PREFIX=%q KEEP_RELEASES=%q SHOCK_HEALTH_URL=%q OTHER_SITE_URLS=%q" \
		"$APP" "$APP_ROOT" "$DOMAIN" "$(server_names)" "$PHP_VERSION" "$POOL_USER" "$POOL_MAX_CHILDREN" "$DB_NAME" "$DB_USER" "$TABLE_PREFIX" "$KEEP_RELEASES" "$SHOCK_HEALTH_URL" "${OTHER_SITE_URLS:-}"
}
