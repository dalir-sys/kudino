<?php
/**
 * Inline SVG icon set (24px grid, 1.75 stroke, round caps).
 *
 * @package Kudino
 */

defined( 'ABSPATH' ) || exit;

function kudino_icon_paths() {
	return array(
		'layers'   => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/><path d="m3 17.5 9 5 9-5"/>',
		'whatsapp' => '<path d="M3.5 20.5 5 16a8.5 8.5 0 1 1 3.2 3.2Z"/><path d="M9 8.5c0 3.5 3 6.5 6.5 6.5l1-1.6-2-1-1 1c-1.2-.4-2.5-1.7-2.9-2.9l1-1-1-2Z"/>',
		'percent'  => '<path d="M19 5 5 19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
		'calendar' => '<rect x="3" y="4.5" width="18" height="16.5" rx="2"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/><path d="M7.5 13.5h2M11 13.5h2M14.5 13.5h2M7.5 17h2M11 17h2"/>',
		'card'     => '<rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19M6 15h4"/>',
		'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>',
		'bag'      => '<path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'leaf'     => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10Z"/><path d="M2 21c0-3 1.9-5.4 5.2-6.1C9.5 14.4 12 13 13 12"/>',
		'sprout'   => '<path d="M7 20h10"/><path d="M12 20v-8"/><path d="M12 12c0-3.5-2.5-6-7-6 0 3.5 2.5 6 7 6Z"/><path d="M12 10c0-3.3 2.2-6 7-6 0 3.3-2.2 6-7 6Z"/>',
		'flask'    => '<path d="M9 3h6"/><path d="M10 3v6.5L4.6 18.4A1.7 1.7 0 0 0 6.1 21h11.8a1.7 1.7 0 0 0 1.5-2.6L14 9.5V3"/><path d="M7.5 15h9"/>',
		'droplet'  => '<path d="M12 21.5a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5S12.5 5 12 2.5C11.5 5 10 7.4 8 9s-3 3.5-3 5.5a7 7 0 0 0 7 7Z"/>',
		'recycle'  => '<path d="M7 19H4.8a1.8 1.8 0 0 1-1.6-2.7L7.4 9"/><path d="M11 19h8.2a1.8 1.8 0 0 0 1.6-2.7l-1.2-2.1"/><path d="m14 16-3 3 3 3"/><path d="M8.3 13.6 7.2 9.4 3 10.5"/><path d="m9.3 5.6 1.1-1.8a1.8 1.8 0 0 1 3.1 0l3.9 6.5"/><path d="m13.4 9.9 4.2 1.1L18.7 6.8"/>',
		'microbe'  => '<circle cx="12" cy="12" r="5"/><circle cx="10.5" cy="11" r=".9"/><circle cx="13.6" cy="13.2" r=".7"/><path d="M12 7V3m0 18v-4M7 12H3m18 0h-4M8.5 8.5 6 6m12 12-2.5-2.5m0-7L18 6M6 18l2.5-2.5"/>',
		'flower'   => '<circle cx="12" cy="9" r="2.5"/><path d="M12 6.5V4a2.5 2.5 0 0 1 2.5 2.5A2.5 2.5 0 0 1 17 9a2.5 2.5 0 0 1-2.5 2.5A2.5 2.5 0 0 1 12 14a2.5 2.5 0 0 1-2.5-2.5A2.5 2.5 0 0 1 7 9a2.5 2.5 0 0 1 2.5-2.5A2.5 2.5 0 0 1 12 4"/><path d="M12 14v7"/><path d="M12 18c-1.5-1.5-3.5-2-5-1.5.5 1.5 2.5 2.5 5 1.5Z"/><path d="M8 21h8"/>',
		'atom'     => '<circle cx="12" cy="12" r="1.5"/><ellipse cx="12" cy="12" rx="9.5" ry="4"/><ellipse cx="12" cy="12" rx="9.5" ry="4" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="9.5" ry="4" transform="rotate(120 12 12)"/>',
		'wheat'    => '<path d="M12 22V9"/><path d="M12 13c-2.2 0-4-1.8-4-4 2.2 0 4 1.8 4 4Zm0 0c2.2 0 4-1.8 4-4-2.2 0-4 1.8-4 4Z"/><path d="M12 9c-2.2 0-4-1.8-4-4 2.2 0 4 1.8 4 4Zm0 0c2.2 0 4-1.8 4-4-2.2 0-4 1.8-4 4Z"/><path d="M12 17c-2.2 0-4-1.8-4-4 2.2 0 4 1.8 4 4Zm0 0c2.2 0 4-1.8 4-4-2.2 0-4 1.8-4 4Z"/><path d="M12 5V2"/>',
		'truck'    => '<path d="M14 17V6a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h1"/><path d="M14 9h4l4 4v3a1 1 0 0 1-1 1h-1"/><circle cx="7" cy="17.5" r="2"/><circle cx="17" cy="17.5" r="2"/><path d="M9 17.5h6"/>',
		'shield'   => '<path d="M12 22s8-3.5 8-10V5l-8-3-8 3v7c0 6.5 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
		'headset'  => '<path d="M3 14v-2a9 9 0 0 1 18 0v2"/><path d="M21 15a2 2 0 0 1-2 2h-1a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h3Z"/><path d="M3 15a2 2 0 0 0 2 2h1a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1H3Z"/><path d="M19 17v1a3 3 0 0 1-3 3h-3"/>',
		'lock'     => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
		'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
		'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
		'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
		'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
		'arrow-left'    => '<path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>',
		'star'     => '<path d="M12 2.8 14.8 8.6l6.3.9-4.6 4.4 1.1 6.3L12 17.2l-5.6 3 1.1-6.3L2.9 9.5l6.3-.9Z"/>',
		'plus'     => '<path d="M12 5v14M5 12h14"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h10"/>',
		'close'    => '<path d="M18 6 6 18M6 6l12 12"/>',
		'home'     => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>',
		'grid'     => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
		'heart'    => '<path d="M19 14c1.5-1.5 3-3.2 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.8 0-3 .5-4.5 2-1.5-1.5-2.7-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4 3 5.5l7 7Z"/>',
		'check'    => '<path d="M20 6 9 17l-5-5"/>',
		'badge'    => '<path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/>',
		'gift'     => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5C10 3 12 8 12 8s2-5 4.5-5a2.5 2.5 0 0 1 0 5"/>',
		'mail'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
		'pin'      => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
		'send'     => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>',
		'filter'   => '<path d="M3 6h18M7 12h10M10 18h4"/>',
	);
}

/**
 * Print / return an icon.
 *
 * @param string $name  Icon key.
 * @param int    $size  Pixel size.
 * @param string $class Extra class.
 */
function kudino_icon( $name, $size = 22, $class = '' ) {
	$paths = kudino_icon_paths();
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	$fill = false !== strpos( $class, 'is-filled' ) ? 'currentColor' : 'none';
	return sprintf(
		'<svg class="kd-icon %4$s" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="%3$s" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		(int) $size,
		$paths[ $name ],
		$fill,
		esc_attr( $class )
	);
}
