<?php

/**
 * Plugin Name: WP2 Blueprint — CLI
 * Description: 
 * Version: 1.0
 * Author: WP2S
 */

namespace WP2\Blueprint\Daemon;

// Define the plugin slug for activation checks
if (!defined('WP2_BLUEPRINT_TEMPLATE_SLUG')) {
    define('WP2_BLUEPRINT_TEMPLATE_SLUG', 'wp2-blueprint/wp2-blueprint.php');
}

class Controller
{
    private static $command_prefix;
    private static $webhook_url;
    private static $initialized = false;

    public static function init()
    {

        // Check if WP_CLI is defined and available
        if (!defined('WP_CLI') || !WP_CLI) {
            return; // Exit if not in WP-CLI context
        }

        if (self::$initialized) {
            return;
        }

        self::$command_prefix = 'wp2-blueprint';

        self::$webhook_url = defined('WP2_BLUEPRINT_WEBHOOK_URL') && WP2_BLUEPRINT_WEBHOOK_URL
            ? WP2_BLUEPRINT_WEBHOOK_URL
            : '';

        self::$webhook_url = self::sanitize_webhook_url(self::$webhook_url);
        if (!self::$webhook_url) {
            \WP_CLI::log('No valid webhook URL defined. Notifications will not be sent.');
        }

        self::register_commands();

        self::$initialized = true;

        \WP_CLI::log('CLI commands initialized successfully for: ' . self::$command_prefix);
    }

    // Added helper method to sanitize the webhook URL
    private static function sanitize_webhook_url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    private static function register_commands()
    {
        \WP_CLI::add_command(self::get_command_name('run'), [self::class, 'run']);
        \WP_CLI::add_command(self::get_command_name('activate'), [self::class, 'activate']);
        \WP_CLI::add_command(self::get_command_name('deactivate'), [self::class, 'deactivate']);
        \WP_CLI::add_command(self::get_command_name('uninstall'), [self::class, 'uninstall']);
    }

    private static function get_command_name($command)
    {
        return self::$command_prefix . ' ' . $command;
    }

    public static function run()
    {
        self::handle_event('plugin_lifecycle_started', 'Starting full plugin lifecycle...');

        // Attempt activation
        $activated = self::activate();

        if (!$activated) {
            self::handle_event('plugin_lifecycle_failed', 'Activation failed, aborting lifecycle at activation step.');
            \WP_CLI::log('Plugin lifecycle aborted: activation failed.');
            return; // Stop the lifecycle if activation fails
        }

        // Attempt deactivation
        $deactivated = self::deactivate();

        if (!$deactivated) {
            self::handle_event('plugin_lifecycle_failed', 'Deactivation failed, aborting lifecycle at deactivation step.');
            \WP_CLI::log('Plugin lifecycle aborted: deactivation failed.');
            return; // Stop the lifecycle if deactivation fails
        }

        // Ensure plugin is fully deactivated
        if (is_plugin_active(WP2_BLUEPRINT_TEMPLATE_SLUG)) {
            \WP_CLI::log("Plugin is still active after deactivation. Cannot proceed with uninstallation.");
            self::handle_event('plugin_lifecycle_failed', 'Plugin still active after deactivation.', [], true);
            return;
        }

        // Attempt uninstallation
        $uninstalled = self::uninstall();

        if (!$uninstalled) {
            self::handle_event('plugin_lifecycle_failed', 'Uninstallation failed, lifecycle completed with errors.');
            \WP_CLI::log('Plugin lifecycle completed with errors at uninstallation step.');
            return; // Stop further actions if uninstall fails
        }

        self::handle_event('plugin_lifecycle_completed', 'Plugin lifecycle completed successfully.');
        \WP_CLI::log('Plugin lifecycle completed successfully.');
    }

    public static function activate()
    {
        \WP_CLI::log("Starting plugin activation for: " . WP2_BLUEPRINT_TEMPLATE_SLUG);

        // Clear cache before starting
        wp_cache_delete('alloptions', 'options');
        \WP_CLI::log("Cache cleared before activation.");

        $is_active = is_plugin_active(WP2_BLUEPRINT_TEMPLATE_SLUG);
        \WP_CLI::log("Initial is_plugin_active() returned: " . ($is_active ? "true" : "false"));

        if ($is_active) {
            \WP_CLI::log("Plugin is already active.");
            return true;
        }

        \WP_CLI::log("Plugin is inactive. Attempting to activate...");
        $result = \WP_CLI::runcommand(
            "plugin activate " . WP2_BLUEPRINT_TEMPLATE_SLUG,
            ['return' => true, 'exit_error' => false]
        );

        \WP_CLI::log("Activation command output: " . $result);

        wp_cache_delete('alloptions', 'options'); // Force cache refresh
        \WP_CLI::log("Cache cleared after activation command.");

        global $wpdb;
        $active_plugins_option = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'active_plugins'");
        $active_plugins = maybe_unserialize($active_plugins_option);
        $is_active_direct = in_array(WP2_BLUEPRINT_TEMPLATE_SLUG, $active_plugins, true);
        \WP_CLI::log("Direct database check for plugin active state returned: " . ($is_active_direct ? "true" : "false"));

        $is_active_after = is_plugin_active(WP2_BLUEPRINT_TEMPLATE_SLUG);
        \WP_CLI::log("Final is_plugin_active() returned: " . ($is_active_after ? "true" : "false"));

        if ($is_active_after || $is_active_direct) {
            \WP_CLI::log("Plugin activation confirmed.");
            return true;
        } else {
            \WP_CLI::log("Plugin activation failed. Retrying...");
            return self::retry_plugin_activation();
        }
    }

    private static function retry_plugin_activation()
    {
        $retries = 3;
        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            \WP_CLI::log("Retry attempt {$attempt}/{$retries}...");

            // Force deactivation before retrying activation
            \WP_CLI::runcommand("plugin deactivate " . WP2_BLUEPRINT_TEMPLATE_SLUG, ['return' => true, 'exit_error' => false]);
            \WP_CLI::log("Plugin deactivated before retry.");

            $result = \WP_CLI::runcommand("plugin activate " . WP2_BLUEPRINT_TEMPLATE_SLUG, ['return' => true, 'exit_error' => false]);
            \WP_CLI::log("Retry activation result: " . $result);

            wp_cache_delete('alloptions', 'options'); // Refresh cache
            \WP_CLI::log("Cache cleared after retry activation.");

            global $wpdb;
            $active_plugins_option = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'active_plugins'");
            $active_plugins = maybe_unserialize($active_plugins_option);
            $is_active_direct = in_array(WP2_BLUEPRINT_TEMPLATE_SLUG, $active_plugins, true);
            $is_active_after = is_plugin_active(WP2_BLUEPRINT_TEMPLATE_SLUG);

            \WP_CLI::log("Retry is_plugin_active() returned: " . ($is_active_after ? "true" : "false"));
            \WP_CLI::log("Retry direct database check returned: " . ($is_active_direct ? "true" : "false"));

            if ($is_active_after || $is_active_direct) {
                \WP_CLI::log("Plugin activation confirmed on retry {$attempt}.");
                return true;
            }

            sleep(2); // Delay between retries
        }

        \WP_CLI::log("All retries exhausted. Plugin activation failed.");
        return false;
    }

    public static function deactivate()
    {
        \WP_CLI::log("Attempting to deactivate plugin: " . WP2_BLUEPRINT_TEMPLATE_SLUG);

        // Run deactivation command
        $result = \WP_CLI::runcommand("plugin deactivate " . WP2_BLUEPRINT_TEMPLATE_SLUG, ['return' => true, 'exit_error' => false]);

        if ($result) {
            \WP_CLI::log("Deactivation command output: " . $result);

            // Forcefully remove the plugin from the active_plugins array
            wp_cache_delete('alloptions', 'options'); // Clear cache
            $active_plugins = get_option('active_plugins', []);
            if (in_array(WP2_BLUEPRINT_TEMPLATE_SLUG, $active_plugins, true)) {
                $active_plugins = array_diff($active_plugins, [WP2_BLUEPRINT_TEMPLATE_SLUG]);
                update_option('active_plugins', $active_plugins);
                wp_cache_delete('alloptions', 'options'); // Ensure changes are propagated
                \WP_CLI::log("Plugin forcibly removed from active_plugins array.");
            }

            // Recheck active state
            $is_active = is_plugin_active(WP2_BLUEPRINT_TEMPLATE_SLUG);
            if (!$is_active) {
                \WP_CLI::log("Plugin successfully deactivated.");
                self::handle_event('plugin_deactivation_completed', 'Plugin deactivated successfully.');
                return true;
            } else {
                \WP_CLI::log("Plugin is still active after deactivation attempt.");
                self::handle_event('plugin_deactivation_failed', 'Plugin is still active after deactivation.', [], true);
                return false;
            }
        } else {
            \WP_CLI::log("Deactivation command failed.");
            self::handle_event('plugin_deactivation_failed', 'Deactivation command failed.', [], true);
            return false;
        }
    }

    public static function uninstall()
    {
        // Ensure plugin is deactivated before attempting uninstallation
        if (is_plugin_active(WP2_BLUEPRINT_TEMPLATE_SLUG)) {
            \WP_CLI::log("Plugin is active. Attempting to deactivate before uninstallation...");

            $deactivated = self::deactivate();

            if (!$deactivated) {
                \WP_CLI::log("Failed to deactivate the plugin. Cannot proceed with uninstallation.");
                self::handle_event('plugin_uninstallation_failed', 'Failed to deactivate the plugin before uninstallation.', [], true);
                return false;
            }
        }

        \WP_CLI::log("Proceeding with uninstallation...");

        // Run the uninstall command
        $result = \WP_CLI::runcommand("plugin uninstall " . WP2_BLUEPRINT_TEMPLATE_SLUG, ['return' => true]);

        if ($result) {
            self::handle_event('plugin_uninstallation_completed', 'Plugin uninstalled successfully.');
            return true;
        } else {
            self::handle_event('plugin_uninstallation_failed', 'Plugin uninstallation failed.', [], true);
            return false;
        }
    }

    private static function has_critical_dependencies()
    {
        // Placeholder for actual dependency check
        return false;
    }

    // In the handle_event method, I added error and success event handling
    private static function handle_event($event, $message, $data = [], $is_error = false)
    {
        $is_error ? \WP_CLI::log("Error: {$message}") : \WP_CLI::log($message);

        $site_data = [
            'site_url'  => get_site_url(),
            'site_name' => get_bloginfo('name'),
            'event'     => $event,
            'timestamp' => time(),
        ];

        self::send_notification(array_merge($site_data, $data));
    }

    private static function send_notification($data)
    {
        if (empty(self::$webhook_url)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 CLI Blueprint] Webhook URL is not defined.');
            }
            return;
        }

        $args = [
            'body'    => json_encode($data),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 15,
        ];

        $response = wp_remote_post(self::$webhook_url, $args);

        if (is_wp_error($response)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 CLI Blueprint] Notification Error: ' . $response->get_error_message());
            }
            // Implement retry logic
            self::retry_notification($data);
        } else {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 CLI Blueprint] Notification sent successfully.');
            }
        }
    }

    private static function retry_notification($data, $retries = 3)
    {
        $delay = 2;
        for ($i = 0; $i < $retries; $i++) {
            $response = wp_remote_post(self::$webhook_url, ['body' => json_encode($data), 'headers' => ['Content-Type' => 'application/json']]);
            if (!is_wp_error($response)) {
                return;
            }
            sleep($delay);
            $delay *= 2; // Exponential backoff
        }

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[WP2 CLI Blueprint] Retry failed, notification not sent.');
        }
    }
}

Controller::init();
