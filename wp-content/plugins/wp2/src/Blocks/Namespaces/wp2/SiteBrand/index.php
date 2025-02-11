<?php

/**
 * Site Brand Block Renderer
 *
 * Renders the site brand (logo or other branding elements).
 *
 * @package WP2\Blocks\SiteBrand
 */

namespace WP2\Blocks\WP2\SiteBrand;

use WP2\Helpers\Asset\Controller as AssetController;

// Ensure AssetController exists before proceeding.
if (! class_exists('WP2\Helpers\Asset')) {
    return;
}

// Define block attributes with fallbacks.
$prefix   = 'wp2';
$identity = $attributes['identity'] ?? 'site';
$kind     = $attributes['kind'] ?? 'logo';
$theme    = $attributes['theme'] ?? 'light';
$type     = $attributes['type'] ?? 'svg';

// Generate asset key.
$site_brand = "{$prefix}-{$identity}-{$kind}-{$theme}";

// Retrieve the asset payload.
$asset_payload = AssetController::get_asset_payload('images', $site_brand, $type);

// Generate classes
$classes = sprintf(
    'wp2-brand %s',
    esc_attr("wp2-brand--" . sanitize_title_with_dashes($identity) . "-" . sanitize_title_with_dashes($kind))
);

echo sprintf(
    '<div useBlockProps class="%s"><img src="%s" alt="%s"/></div>',
    $classes,
    esc_url($asset_payload['url'] ?? ''),
    esc_attr($asset_payload['alt'] ?? '')
);
