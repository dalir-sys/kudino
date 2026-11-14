#!/usr/bin/env bash
# Step 4 — HTTPS for Kudino's domain(s) only, after DNS points at the server.
# Uses the server's EXISTING Let's Encrypt account; refuses to create a new one.
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/config.sh"; . "$DIR/lib.sh"
require_domain

NAMES="$(server_names)"
args=""; for n in $NAMES; do args+=" -d $n"; done

remote "set -euo pipefail
	for n in $NAMES; do
		ip=\$(getent ahostsv4 \"\$n\" | awk 'NR==1{print \$1}')
		if [ \"\$ip\" != '$SERVER_IP' ]; then echo \"DNS: \$n -> \${ip:-<none>} (want $SERVER_IP). Not ready; try again later.\" >&2; exit 2; fi
		echo \"DNS: \$n -> \$ip ok\"
	done
	ls /etc/letsencrypt/accounts/*/directory/* >/dev/null 2>&1 || { echo 'No existing Let’s Encrypt account on the server — stopping (ask the owner before creating one).' >&2; exit 3; }
	cp -p /etc/nginx/sites-available/$APP /root/$APP-nginx.backup-\$(date +%Y%m%d-%H%M%S)
	certbot --nginx --non-interactive --redirect --keep-until-expiring --cert-name $APP $args
	nginx -t
	systemctl reload nginx
	curl -s --max-time 15 '$SHOCK_HEALTH_URL' | grep -q '\"ok\":true' && echo 'Shock Support health: OK' || { echo 'Shock Support health FAILED' >&2; exit 4; }"
say "HTTPS ready for: $NAMES. Next: bash deploy/deploy.sh"
