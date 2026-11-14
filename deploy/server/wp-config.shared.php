<?php
/**
 * Kudino — __APP_ROOT__/shared/wp-config.php
 * Written ONCE by bootstrap on the server. Holds the DB password and salts.
 * Mode 0640 root:__POOL_USER__ — never copied off the server, never in git.
 * Placeholders are replaced by the bootstrap script.
 */

define( 'DB_NAME', '__DB_NAME__' );
define( 'DB_USER', '__DB_USER__' );
define( 'DB_PASSWORD', '__DB_PASSWORD__' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

__SALTS__

$table_prefix = '__TABLE_PREFIX__';

define( 'WP_HOME', 'https://__DOMAIN__' );
define( 'WP_SITEURL', 'https://__DOMAIN__' );
define( 'FORCE_SSL_ADMIN', true );

// Behind a reverse proxy/CDN (e.g. ArvanCloud) that terminates TLS.
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

// Code comes only from deploys: no editing, installing or auto-updating from wp-admin.
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );


define( 'WP_MEMORY_LIMIT', '128M' );
define( 'WP_MAX_MEMORY_LIMIT', '256M' );
define( 'WP_POST_REVISIONS', 10 );
define( 'EMPTY_TRASH_DAYS', 14 );

define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_ENVIRONMENT_TYPE', 'production' );
