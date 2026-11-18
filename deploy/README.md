# Deploying Kudino to the shared VPS

Kudino (WordPress + WooCommerce + the `kudino` theme) runs next to Shock Support and the Barcode
site on `46.249.107.236`. Everything is built elsewhere (GitHub Actions or the laptop) and uploaded.
Nothing is built on the server.

There are two ways to run the same scripts:
- **GitHub Actions (recommended):** the *Kudino server* workflow. See the next section.
- **Laptop:** run them from the repository root in **Git Bash**. See *Running from the laptop* further down.

## GitHub Actions

### One-time setup (needs the laptop once, about 2 minutes)

The new key has to be authorized with the existing Shock Support key, which only exists on the laptop.

1. In Git Bash in the repository, create `~/.ssh/kudino_vps_ed25519` and authorize it on the server:
   ```bash
   bash deploy/setup-key.sh
   ```
2. Add two **repository secrets** (Settings → Secrets and variables → Actions → New repository secret):
   - **`VPS_SSH_KEY`:** the whole content of `~/.ssh/kudino_vps_ed25519` (the private key). Copy it with:
     ```bash
     cat ~/.ssh/kudino_vps_ed25519 | clip
     ```
   - **`VPS_KNOWN_HOSTS`:** the server's host key, taken from the laptop that already trusts it:
     ```bash
     ssh-keygen -F 46.249.107.236 | grep -v '^#' | clip
     ```
   With the GitHub CLI, the same two secrets in one go:
   ```bash
   gh secret set VPS_SSH_KEY < ~/.ssh/kudino_vps_ed25519
   ```
   ```bash
   ssh-keygen -F 46.249.107.236 | grep -v '^#' | gh secret set VPS_KNOWN_HOSTS
   ```
3. Optional **repository variables**:
   - `ADMIN_EMAIL`: the WordPress admin email.
   - `OTHER_SITE_URLS`: the Barcode site's URL, so every run checks it too.
   - `AUTO_DEPLOY=true`: deploy on every push to `main` that changes `kudino/` or `deploy/`.

The workflow must be on the default branch (`main`) for the **Run workflow** button to appear.

### Going live (Actions → Kudino server → Run workflow), in this order

| Task | What it does |
| --- | --- |
| `survey` | Read-only summary. Check it shows the other sites before going on. |
| `bootstrap-dry-run` | Shows what bootstrap would do. |
| `bootstrap` | Creates the `kudino` Linux user, the DB and DB user, the pool and the Nginx site. |
| `certbot` | HTTPS for `kudino.ir` and `www.kudino.ir`, using the existing Let's Encrypt account. |
| `deploy` | Builds on the runner, uploads and switches the release. |
| `install-wp` | Installs WordPress and sets Kudino's defaults (theme, WooCommerce, Persian, Tehran time, Toman). |

Day to day there are `deploy`, `list` and `rollback` (with an optional `rollback_to` release).
Only one job runs at a time, and the SSH key is deleted from the runner at the end of each job.

**The WordPress admin password** is generated on the server and saved only in
`/root/kudino-wp-admin.txt` (root-only). It's never printed in the Actions log. Read it over SSH,
change it in wp-admin, then delete the file.

If the runner can't reach the server (some Iranian hosts filter traffic from abroad), the `survey` task
fails at the SSH step. Use the laptop route below in that case.

## What gets created on the server (and nothing else)

| Thing | Where |
| --- | --- |
| Releases, `current` symlink, shared files | `/var/www/kudino/{releases,current,shared,bin}` |
| Linux user | `kudino` (system user, no login shell). The PHP pool runs as this user. |
| Secrets (DB password, salts) | `/var/www/kudino/shared/wp-config.php`, mode `0640 root:kudino`, generated on the server |
| WordPress admin credentials | `/root/kudino-wp-admin.txt`, root-only, generated on the server |
| Media uploads (survive deploys) | `/var/www/kudino/shared/uploads` |
| Database / user | `kudino` / `kudino@localhost`, granted only on `kudino.*` |
| PHP-FPM pool | `/etc/php/8.3/fpm/pool.d/kudino.conf`, socket `/run/php/php8.3-fpm-kudino.sock`, `ondemand`, 3 children |
| Nginx site | `/etc/nginx/sites-available/kudino` plus a symlink in `sites-enabled` |
| HTTPS | certificate `kudino`, issued by `certbot --nginx` for Kudino's names only |

Never touched: `/var/www/shocksupport` and its Nginx site, the `shock_support` database and `shock` user;
the **Barcode** site, its `barcode` Linux user and anything named `barcode*`; `nginx.conf`, `php.ini`, `/etc/mysql`,
the firewall, SSH settings, and existing keys. The scripts refuse any app, DB, DB user, pool user or domain
name that starts with `shock` or `barcode`. Shock Support's `/api/health` and every URL in `OTHER_SITE_URLS`
must be healthy before and after each change, or the script stops.

## Running from the laptop

All steps in one go, asking before each step that changes the server:

```bash
bash deploy/go-live.sh
```

Or step by step:

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
   bash deploy/bootstrap.sh --dry-run --create-user
   ```
   ```bash
   bash deploy/bootstrap.sh --create-user
   ```
   - It stops before changing anything if Shock Support is unhealthy or a WordPress PHP extension is missing. It never installs packages; it prints the `apt` line for you to approve.
   - It runs `php-fpm8.3 -t` and `nginx -t` before each reload, and undoes its own change if a test fails.
   - It never overwrites an existing `kudino` site file, because Certbot edits that file.
   - `--create-user` creates the `kudino` system user (approved). Without the flag, bootstrap refuses to run if the user is missing.
4. **HTTPS.** DNS is already done: `kudino.ir` and `www` point to `46.249.107.236` (checked on 2026-11-18).
   ```bash
   bash deploy/certbot.sh
   ```
   It checks from the server that both names resolve to the VPS and stops if not. It uses the existing Let's Encrypt account and stops instead of creating a new one. It backs up the site file, runs `nginx -t`, reloads, and checks Shock Support.
5. **First deploy, then install WordPress right away.**
   ```bash
   bash deploy/deploy.sh
   ```
   ```bash
   bash deploy/install-wp.sh
   ```
   `install-wp.sh` installs WordPress with a generated admin password (saved only in `/root/kudino-wp-admin.txt`).
   It activates the Kudino theme and WooCommerce, and sets Persian (if the language pack is bundled), Tehran time,
   Toman prices and `/%postname%/` permalinks. Re-running it never changes users or passwords.

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

Also tested: a dedicated `kudino` pool user (bootstrap refuses without `--create-user`), uploads served by Nginx with that user,
`install-wp` run from `/root` like on the server, and the guards rejecting `barcode` names.

Not tested there: Certbot and real DNS (the container has no public name) and Windows Git Bash itself. The scripts use only `bash`, `ssh`, `scp`, `tar`, `curl` and `sha*sum`, plus `unzip` or PowerShell.
