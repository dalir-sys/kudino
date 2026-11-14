<?php
/**
 * Kudino release stub — ships inside every release as wp-config.php.
 * Secrets live in <app root>/shared/wp-config.php (outside releases, outside git).
 * Releases are at <app root>/releases/<timestamp>/, so shared/ is two levels up.
 */
require dirname( __DIR__, 2 ) . '/shared/wp-config.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
