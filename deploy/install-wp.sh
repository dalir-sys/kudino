#!/usr/bin/env bash
# Step 6 — install WordPress once and apply Kudino's defaults (theme, WooCommerce, fa_IR, Tehran time,
# Toman). The admin password is generated on the server and saved only to /root/kudino-wp-admin.txt.
#   bash deploy/install-wp.sh
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/config.sh"; . "$DIR/lib.sh"
require_domain
if [ -z "${ADMIN_EMAIL:-}" ]; then read -r -p "Admin email for WordPress (password-reset mails go here): " ADMIN_EMAIL; fi
remote "$(remote_env) ADMIN_USER=$(printf %q "$ADMIN_USER") ADMIN_EMAIL=$(printf %q "$ADMIN_EMAIL") SEARCH_INDEXING=$(printf %q "$SEARCH_INDEXING") bash -s" < "$DIR/server/install-wp-remote.sh"
say "Open https://$DOMAIN/wp-admin/ — credentials: ssh in and cat /root/$APP-wp-admin.txt"
