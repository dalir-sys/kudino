#!/usr/bin/env bash
# Step 3 — one-time server setup for Kudino (idempotent; safe to re-run).
#   bash deploy/bootstrap.sh --dry-run        show what would happen
#   bash deploy/bootstrap.sh                  do it
#   bash deploy/bootstrap.sh --create-user    also create POOL_USER (only after the owner approved)
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/config.sh"; . "$DIR/lib.sh"
require_domain

DRY=0; CREATE_USER=0
for a in "$@"; do case "$a" in --dry-run) DRY=1;; --create-user) CREATE_USER=1;; *) die "unknown option $a";; esac; done

say "Bootstrap $APP for: $(server_names)  →  $SSH_USER@$SERVER_IP:$APP_ROOT"
[ "$DRY" = 1 ] && warn "DRY RUN — nothing will be changed"

tar -C "$DIR/server" -czf - . | remote "set -e
	d=\$(mktemp -d /tmp/$APP-bootstrap.XXXXXX)
	trap 'rm -rf \"\$d\"' EXIT
	tar -xzf - -C \"\$d\"
	$(remote_env) DRY_RUN=$DRY CREATE_USER=$CREATE_USER bash \"\$d/bootstrap-remote.sh\""

say "Next: add the DNS A records, wait for them to resolve, then: bash deploy/certbot.sh"
