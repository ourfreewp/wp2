<?php

/**
 * Path: wp-content/plugins/wp2/src/Helpers/Event/init-coda-automation.php
 *
 * Handles registration of the Coda Automation settings page and corresponding
 * meta boxes, allowing administrators to configure one or more webhook
 * destinations (name, URL, and token). Includes methods to retrieve destinations
 * from the database and send event notifications, with retry logic for transient
 * failures.
 *
 * @package WP2\Helpers\Event\CodaAutomation
 */

namespace WP2\Helpers\Event\CodaAutomation;

class Controller
{
    private $textdomain = 'wp2';
    private $namespace;
    private $key;
    private $page_id;
    private $fields_id;
    private $group_id;
    private $group_prefix;
    private $option_name; // wp2_coda_automation_options
    private $menu_title; // Coda Automation
    private $page_title; // Coda Automation
    private $fields_title; // Coda Automation Fields
    private $destinations_title; // Destinations
    private $capability; // manage_options

    /**
     * Constructor.
     *
     * @param string $namespace The namespace for the settings page.
     * @param string $key       The key for the settings page.
     */

    public function __construct(string $namespace, string $key)
    {
        $this->namespace = 'wp2';
        $this->key       = 'coda_automation';

        $this->page_id           = $this->namespace . '_' . $this->key . '_settings';
        $this->fields_id          = $this->namespace . '_' . $this->key . '_fields';
        $this->group_id           = $this->namespace . '_' . $this->key . '_group';
        $this->group_prefix       = $this->namespace . '_' . $this->key . '_';
        $this->option_name        = $page_id;
        $this->menu_title         = __('Coda Automation', $this->textdomain);
        $this->page_title         = __('Coda Automation', $this->textdomain);
        $this->fields_title        = __('Coda Automation Fields', $this->textdomain);
        $this->destinations_title  = __('Destinations', $this->textdomain);
        $this->capability          = 'manage_options';
    }


    /**
     * Retrieves a single destination from the options.
     * 
     * @param string $destination_name Name of the destination.
     * 
     * 
     */
    private function get_destination(string $destination_name): array
    {
        $destinations = $this->get_destinations();

        return $destinations[$destination_name] ?? [];
    }


    /**
     * Retrieve registered destinations from the options.
     *
     * Expects the destinations to be saved as a group in the option defined by $this->option_name.
     *
     * @return array Associative array of destinations keyed by destination name.
     */
    private function get_destinations(): array
    {
        $options      = get_option($this->option_name, []);
        $destinations = [];

        if (isset($options[$this->destinations]) && is_array($options[$this->destinations])) {
            foreach ($options[$this->destinations] as $destination_data) {
                if (
                    isset($destination_data[$this->destination . '_name']) &&
                    isset($destination_data[$this->destination . '_url']) &&
                    isset($destination_data[$this->destination . '_access_token'])
                ) {
                    $name  = sanitize_text_field($destination_data[$this->destination . '_name']);
                    $url   = esc_url_raw($destination_data[$this->destination . '_url']);
                    $token = sanitize_text_field($destination_data[$this->destination . '_access_token']);

                    $destinations[$name] = [
                        'name'         => $name,
                        'url'          => $url,
                        'access_token' => $token,
                    ];
                }
            }
        }

        return $destinations;
    }

    /**
     * Send an event notification to a destination by name.
     *
     * This method retrieves the destination from the settings and sends the event payload to the destination's URL.
     *
     * @param string $destination_name Name of the destination.
     * @param string $event            Event identifier.
     * @param string $message          Event message.
     * @param array  $data             Additional event data.
     * @param bool   $is_error         Whether the event represents an error.
     * @return void
     */
    public function send_event_to_destination(string $destination_name, string $event, string $message, array $data = [], bool $is_error = false): void
    {
        $destinations = $this->get_destinations();

        if (! isset($destinations[$destination_name])) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log(sprintf('CodaAutomation: Destination "%s" not found.', $destination_name));
            }
            return;
        }

        $destination = $destinations[$destination_name];
        $url         = $destination['url'];

        $payload = array_merge(
            [
                'site_url'  => get_site_url(),
                'site_name' => get_bloginfo('name'),
                'event'     => $event,
                'message'   => $message,
                'is_error'  => $is_error,
                'timestamp' => time(),
            ],
            $data
        );

        $this->send_notification_to_url($url, $payload);
    }

    /**
     * Send a notification payload to a specified URL.
     *
     * @param string $url  The destination URL.
     * @param array  $data The payload data.
     * @return void
     */
    private function send_notification_to_url(string $url, array $data): void
    {
        $args = [
            'body'    => wp_json_encode($data),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 15,
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            $this->retry_notification($url, $data);
        }
    }

    /**
     * Retry sending a notification with exponential backoff.
     *
     * @param string $url     The destination URL.
     * @param array  $data    The payload data.
     * @param int    $retries Number of retry attempts.
     * @return void
     */
    private function retry_notification(string $url, array $data, int $retries = 3): void
    {
        $delay = 2;

        for ($i = 0; $i < $retries; $i++) {
            $args = [
                'body'    => wp_json_encode($data),
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => 15,
            ];

            $response = wp_remote_post($url, $args);

            if (! is_wp_error($response)) {
                return;
            }

            sleep($delay);
            $delay *= 2; // Exponential backoff.
        }

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('CodaAutomation: Failed to send notification after retries. Payload: ' . wp_json_encode($data));
        }
    }
}
