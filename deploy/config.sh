# Kudino deploy settings (no secrets here — the DB password is generated on the server
# and lives only in /var/www/kudino/shared/wp-config.php).
# Override any value in deploy/config.local.sh (git-ignored) instead of editing this file.

SERVER_IP="46.249.107.236"
SSH_USER="root"
SSH_KEY="$HOME/.ssh/kudino_vps_ed25519"            # created by deploy/setup-key.sh
SSH_KEY_EXISTING="$HOME/.ssh/shocksupport_vps_ed25519"  # used ONLY by setup-key.sh

APP="kudino"
APP_ROOT="/var/www/kudino"
DOMAIN=""                 # e.g. kudino.ir — required; set it in deploy/config.local.sh
WITH_WWW=1                # 1 = also serve and certify www.$DOMAIN

PHP_VERSION="8.3"
POOL_USER="www-data"      # dedicated user only after you approve creating it (bootstrap --create-user)
POOL_MAX_CHILDREN=3

DB_NAME="kudino"
DB_USER="kudino"
TABLE_PREFIX="kd_"

# Pinned build inputs (downloaded on the laptop, never on the server)
WP_VERSION="6.8.3"
WC_VERSION="latest-stable"   # pin to an exact version (e.g. 10.3.0) once you have tested it
WPCLI_VERSION="2.12.0"
INCLUDE_FA=1                 # bundle the Persian (fa_IR) language packs

KEEP_RELEASES=5
SHOCK_HEALTH_URL="https://shocksupport.com/api/health"

[ -f "$(dirname "${BASH_SOURCE[0]}")/config.local.sh" ] && . "$(dirname "${BASH_SOURCE[0]}")/config.local.sh"
