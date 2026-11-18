#!/usr/bin/env bash
# Step 1 — create a dedicated SSH key for Kudino and authorize it on the server.
# Uses the existing Shock Support key exactly once (to append the new public key).
# Does not touch ~/.ssh/config, does not change or remove any existing key.
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/config.sh"; . "$DIR/lib.sh"

[ -f "$SSH_KEY_EXISTING" ] || die "Existing key $SSH_KEY_EXISTING not found"

if [ -f "$SSH_KEY" ]; then
	say "Key already exists: $SSH_KEY (keeping it)"
else
	say "Creating $SSH_KEY (ed25519, no passphrase)"
	ssh-keygen -q -t ed25519 -N "" -C "kudino-deploy" -f "$SSH_KEY"
fi
PUB="$(cat "$SSH_KEY.pub")"

say "Appending the public key to /root/.ssh/authorized_keys (backup first, no duplicates)"
printf '%s\n' "$PUB" | ssh -i "$SSH_KEY_EXISTING" -o IdentitiesOnly=yes -o BatchMode=yes -o ConnectTimeout=15 "$SSH_USER@$SERVER_IP" '
	set -eu
	umask 077
	read -r key
	case "$key" in ssh-ed25519\ *) ;; *) echo "not an ed25519 public key" >&2; exit 1 ;; esac
	f=/root/.ssh/authorized_keys
	cp -p "$f" "/root/authorized_keys.backup-$(date +%Y%m%d-%H%M%S)"
	if grep -qxF "$key" "$f"; then echo "already present"; else printf "%s\n" "$key" >> "$f"; echo "added"; fi
'

say "Testing login with the NEW key only"
remote 'echo "logged in as $(whoami) on $(hostname)"'
say "Done. All other scripts use $SSH_KEY only."
