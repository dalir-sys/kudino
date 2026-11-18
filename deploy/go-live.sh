#!/usr/bin/env bash
# Everything, in order, stopping at the first error and asking before each server-changing step:
#   key → survey → bootstrap (creates the kudino user) → certbot → deploy → WordPress install
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/config.sh"; . "$DIR/lib.sh"
require_domain
ask() { read -r -p "$1 [y/N] " a; [ "$a" = y ] || [ "$a" = Y ] || die "stopped by you"; }

say "1/6 SSH key";               bash "$DIR/setup-key.sh"
say "2/6 Server survey (read-only)"; bash "$DIR/survey.sh"
ask "Continue to the bootstrap dry run?"
say "3/6 Bootstrap (dry run)";   bash "$DIR/bootstrap.sh" --dry-run --create-user
ask "Apply the bootstrap (creates user '$POOL_USER', DB '$DB_NAME', pool, Nginx site)?"
bash "$DIR/bootstrap.sh" --create-user
ask "Request the Let's Encrypt certificate for $(server_names) with the existing account?"
say "4/6 HTTPS";                 bash "$DIR/certbot.sh"
say "5/6 Deploy";                bash "$DIR/deploy.sh"
say "6/6 WordPress install";     bash "$DIR/install-wp.sh"
say "Kudino is live: https://$DOMAIN/"
