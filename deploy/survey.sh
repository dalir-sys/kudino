#!/usr/bin/env bash
# Read-only summary of the server. Changes nothing.
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/config.sh"; . "$DIR/lib.sh"

# Before setup-key.sh has run, allow the existing key for this read-only look.
if [ ! -f "$SSH_KEY" ]; then warn "Project key missing; using the existing key for this read-only survey"; SSH_KEY="$SSH_KEY_EXISTING"; fi

remote 'bash -s' <<'REMOTE'
set -u
h() { printf '\n== %s ==\n' "$1"; }
h "System";    hostnamectl --static 2>/dev/null; . /etc/os-release; echo "$PRETTY_NAME"; uptime
h "Memory";    free -h; swapon --show
h "Disk";      df -h / /var 2>/dev/null | sort -u
h "Services";  for s in nginx php8.3-fpm mariadb certbot.timer; do printf '%-14s %s\n' "$s" "$(systemctl is-active "$s")"; done
h "Nginx sites (enabled)"; ls -l /etc/nginx/sites-enabled/
h "Nginx server_names"; grep -h "server_name" /etc/nginx/sites-enabled/* 2>/dev/null | sed 's/^[ \t]*//' | sort -u
h "PHP-FPM pools"; for f in /etc/php/8.3/fpm/pool.d/*.conf; do echo "-- $f"; grep -E '^\s*(\[|user|listen\s*=|pm(\.max_children)?\s*=)' "$f"; done
h "PHP extensions WordPress needs"; for e in mysqli curl gd mbstring xml dom zip intl openssl; do php -m | grep -qix "$e" && printf '%-9s ok\n' "$e" || printf '%-9s MISSING\n' "$e"; done
h "Databases"; mysql -N -e "SHOW DATABASES" 2>&1
h "/var/www";  ls -l /var/www
h "Certificates"; certbot certificates 2>/dev/null | grep -E "Certificate Name|Domains|Expiry" || echo "(certbot not readable)"
h "Shock Support health"; curl -s --max-time 10 https://shocksupport.com/api/health; echo
REMOTE
