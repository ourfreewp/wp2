<?php

/**
 * Webhook Helper
 *
 * Provides functions for sanitizing and validating webhook URLs.
 *
 * @package WP2_Daemon\WP2_New\Helpers\Webhook
 */

namespace WP2_Daemon\WP2_New\Helpers\Webhook;

class WebhookHelper
{
    /**
     * Sanitize a webhook URL.
     *
     * @param string $url The URL to sanitize.
     * @return string Valid URL or empty string if invalid.
     */
    public static function sanitize($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
    }
}
