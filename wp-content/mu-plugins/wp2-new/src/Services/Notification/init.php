<?php
// Path: wp-content/mu-plugins/wp2-new/src/Services/Notification/init.php
/**
 * Notification Service
 *
 * Implements the logic to send notifications via a configured webhook URL.
 * Supports retry logic in case of failures.
 *
 * @package WP2_Daemon\WP2_New\Services\Notification
 */

namespace WP2_Daemon\WP2_New\Services\Notification;

use WP2_Daemon\WP2_New\Helpers\Webhook\WebhookHelper;

class Controller
{

    protected $webhook_url;

    public function __construct()
    {
        $this->webhook_url = defined('WP2_NEW_WEBHOOK_URL') && WP2_NEW_WEBHOOK_URL
            ? WP2_NEW_WEBHOOK_URL
            : '';
    }
    /**
     * Send a notification.
     *
     * @param array $data The data to send.
     */
    public function send(array $data)
    {
        if (empty($this->webhook_url)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 New] Webhook URL is not defined.');
            }
            return;
        }

        $args = [
            'body'    => json_encode($data),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 15,
        ];

        $response = wp_remote_post($this->webhook_url, $args);

        if (is_wp_error($response)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 New] Notification Error: ' . $response->get_error_message());
            }
            $this->retry($data);
        } else {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 New] Notification sent successfully.');
            }
        }
    }
    /**
     * Retry sending the notification with exponential backoff.
     *
     * @param array $data The data to send.
     * @param int $retries Number of retry attempts.
     */
    protected function retry(array $data, $retries = 3)
    {
        $delay = 2;
        for ($i = 0; $i < $retries; $i++) {
            $response = wp_remote_post($this->webhook_url, [
                'body'    => json_encode($data),
                'headers' => ['Content-Type' => 'application/json'],
            ]);
            if (! is_wp_error($response)) {
                return;
            }
            sleep($delay);
            $delay *= 2; // Exponential backoff
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[WP2 New] Retry failed, notification not sent.');
        }
    }
}
