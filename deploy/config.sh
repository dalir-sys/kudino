# Kudino deploy settings (no secrets here — the DB password is generated on the server
# and lives only in /var/www/kudino/shared/wp-config.php).
# Override any value in deploy/config.local.sh (git-ignored) instead of editing this file.

SERVER_IP="46.249.107.236"
SSH_USER="root"
SSH_KEY="$HOME/.ssh/kudino_vps_ed25519"            # created by deploy/setup-key.sh
SSH_KEY_EXISTING="$HOME/.ssh/shocksupport_vps_ed25519"  # used ONLY by setup-key.sh

APP="kudino"
APP_ROOT="/var/www/kudino"
DOMAIN="kudino.ir"        # A records for kudino.ir and www.kudino.ir -> 46.249.107.236 (ArvanCloud, verified)
WITH_WWW=1                # 1 = also serve and certify www.$DOMAIN

PHP_VERSION="8.3"
POOL_USER="kudino"        # dedicated system user (approved 2026-11-18); bootstrap --create-user creates it
POOL_MAX_CHILDREN=3

DB_NAME="kudino"
DB_USER="kudino"
TABLE_PREFIX="kd_"

# Search engines: 0 = noindex (pre-launch: WordPress "discourage search engines" + X-Robots-Tag header),
# 1 = allow indexing. Applied by install-wp.sh (GitHub Actions task install-wp).
SEARCH_INDEXING="${SEARCH_INDEXING:-0}"

# WordPress admin (install-wp.sh). The password is generated on the server, never stored here.
ADMIN_USER="kudino-admin"
ADMIN_EMAIL="${ADMIN_EMAIL:-}"   # asked interactively if empty (GitHub Actions passes it in)

# Pinned build inputs (downloaded on the laptop, never on the server)
WP_VERSION="latest"         # resolved to the newest stable at build time (printed in the log); pin e.g. "7.0.1" if needed
WC_VERSION="latest-stable"   # pin to an exact version (e.g. 10.3.0) once you have tested it
WPCLI_VERSION="2.12.0"
INCLUDE_FA=1                 # bundle the Persian (fa_IR) language packs

KEEP_RELEASES=5
SHOCK_HEALTH_URL="https://shocksupport.com/api/health"
# Other live sites on this server (found by survey on 2026-11-18). Each must answer 2xx/3xx before and
# after every change, or the scripts stop. Space-separated.
OTHER_SITE_URLS="${OTHER_SITE_URLS:-https://velingo24.ir/}"

if [ -f "$(dirname "${BASH_SOURCE[0]}")/config.local.sh" ]; then . "$(dirname "${BASH_SOURCE[0]}")/config.local.sh"; fi
