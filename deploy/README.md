# Deploying Kudino to the shared VPS

Kudino (WordPress + WooCommerce + the `kudino` theme) runs next to Shock Support on
`46.249.107.236`. Everything is built on the laptop and uploaded. Nothing is built on the server.
Run every command from the repository root in **Git Bash**.

## What gets created on the server (and nothing else)

| Thing | Where |
| --- | --- |
| Releases, `current` symlink, shared files | `/var/www/kudino/{releases,current,shared,bin}` |
| Secrets (DB password, salts) | `/var/www/kudino/shared/wp-config.php`, mode `0640 root:www-data`, generated on the server |
| Media uploads (survive deploys) | `/var/www/kudino/shared/uploads` |
| Database / user | `kudino` / `kudino@localhost`, granted only on `kudino.*` |
| PHP-FPM pool | `/etc/php/8.3/fpm/pool.d/kudino.conf`, socket `/run/php/php8.3-fpm-kudino.sock`, `ondemand`, 3 children |
| Nginx site | `/etc/nginx/sites-available/kudino` plus a symlink in `sites-enabled` |
| HTTPS | certificate `kudino`, issued by `certbot --nginx` for Kudino's names only |

Never touched: `/var/www/shocksupport`, the `shocksupport` Nginx site, `nginx.conf`, `php.ini`,
`/etc/mysql`, the `shock_support` database and `shock` user, the firewall, SSH settings, existing keys.

## First-time setup

```bash
# DOMAIN is kudino.ir (deploy/config.sh). Overrides go in deploy/config.local.sh (git-ignored).
```

1. **Key.** Creates `~/.ssh/kudino_vps_ed25519` and appends its public key. It backs up `authorized_keys` first, uses the Shock Support key only for this one step, then tests the new key.
   ```bash
   bash deploy/setup-key.sh
   ```
2. **Look first.** This is a read-only summary: memory, disk, sites, pools, databases, PHP extensions, certificates, and Shock Support health.
   ```bash
   bash deploy/survey.sh
   ```
3. **Server setup.** Folders, database and user, pool, Nginx site. It's idempotent.
   ```bash
   bash deploy/bootstrap.sh --dry-run
   ```
   ```bash
   bash deploy/bootstrap.sh
   ```
   - It stops before changing anything if Shock Support is unhealthy or a WordPress PHP extension is missing. It never installs packages; it prints the `apt` line for you to approve.
   - It runs `php-fpm8.3 -t` and `nginx -t` before each reload, and undoes its own change if a test fails.
   - It never overwrites an existing `kudino` site file, because Certbot edits that file.
   - A dedicated Linux user is created only with `--create-user`, set `POOL_USER` first and only after you approved it.
4. **DNS (ArvanCloud).** Add A records `@` and `www` pointing to `46.249.107.236`, with the CDN/cloud proxy off for now. Then:
   ```bash
   bash deploy/certbot.sh
   ```
   It checks from the server that both names resolve to the VPS and stops if not. It uses the existing Let's Encrypt account and stops instead of creating a new one. It backs up the site file, runs `nginx -t`, reloads, and checks Shock Support.
5. **First deploy, then install immediately.**
   ```bash
   bash deploy/deploy.sh
   ```
   Then open `https://your-domain/wp-admin/install.php` and create the admin account (choose the password yourself). In wp-admin:
   - **Appearance → Themes:** activate *Kudino*.
   - **Plugins:** activate *WooCommerce*.
   - **Settings → General:** set the site language to فارسی.

## Day to day

Build, upload, unpack, run DB updates, switch, and health-check both sites:
```bash
bash deploy/deploy.sh
```
Show releases on the server (`*` marks the current one):
```bash
bash deploy/deploy.sh --list
```
Switch back to the previous release:
```bash
bash deploy/deploy.sh --rollback
```
Switch to a specific release:
```bash
bash deploy/deploy.sh --rollback 20261114150000
```
Just build `deploy/.build/kudino-<ts>.tar.gz`:
```bash
bash deploy/deploy.sh --build-only
```

How a deploy runs:
1. **Build** on the laptop:
   - WordPress `WP_VERSION`, SHA-1 checked against wordpress.org.
   - WooCommerce `WC_VERSION`. `latest-stable` is resolved to an exact version and printed; pin it.
   - Persian language packs, the theme from this repo, the `wp-config.php` stub, and WP-CLI (SHA-512 checked).
2. **Upload:** `scp` to `/tmp`, then unpack into `releases/<timestamp>`. Files are `root:root`, so PHP can't modify code. `uploads` is linked to `shared/uploads`.
3. **Database updates:** `wp core update-db` and `wp wc update` run against the new code before the switch. If they fail, the new release is deleted and the site stays as it was.
4. **Switch and check:** atomic `current` symlink swap, then `systemctl reload php8.3-fpm`, which is graceful and clears OPcache. Then the health checks:
   - **Kudino:** if the homepage isn't 200/301/302, or shows a database or critical error, it switches back to the previous release automatically.
   - **Shock Support:** `/api/health` must contain `"ok":true`.
5. **Prune:** the last 5 releases are kept, and the current one is never pruned.

Plugins and themes are only ever changed by a deploy (`DISALLOW_FILE_MODS`). To add a plugin, add it to
`build()` in `deploy.sh` with a pinned version.

## Settings

`deploy/config.sh` holds the defaults, and `deploy/config.local.sh` overrides them (git-ignored): `DOMAIN`, `WITH_WWW`,
`POOL_USER`, `WP_VERSION`, `WC_VERSION`, `KEEP_RELEASES`, and so on. No secrets live in either file.

## How this was tested

In a disposable Ubuntu 24.04 container with Nginx, PHP 8.3-FPM and MariaDB 10.11, with a stand-in
`shock_support` database and a mock `/api/health`:
- **Bootstrap:** dry run, real run, and re-run.
- **Deploy:** the real `deploy.sh` build, the first deploy, the WordPress install, three more deploys with DB updates and pruning, and a rollback.
- **Broken release:** a deliberate PHP syntax error caused HTTP 500, which triggered an automatic rollback to 200.
- **Blocked paths:** `wp-config.php`, dotfiles, `xmlrpc.php` and PHP in uploads all return 403.
- **Isolation:** the `shock` grants were unchanged throughout.

Not tested there: Certbot and real DNS (the container has no public name) and Windows Git Bash itself. The scripts use only `bash`, `ssh`, `scp`, `tar`, `curl` and `sha*sum`, plus `unzip` or PowerShell.
