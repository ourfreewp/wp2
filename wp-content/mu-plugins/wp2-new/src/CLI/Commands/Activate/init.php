<?php
// Path: wp-content/mu-plugins/wp2-new/src/CLI/Commands/Activate/init.php
/**
 * Activate Command
 *
 * Implements the WP-CLI command to activate the plugin.
 * - Clears cache before and after activation.
 * - Runs the activation process using WP-CLI.
 * - Sends a notification on success.
 *
 * @package WP2_Daemon\WP2_New\CLI\Commands\Activate
 */

namespace WP2_Daemon\WP2_New\CLI\Commands\Activate;

use WP2_Daemon\WP2_New\Services\Notification\NotificationService;

class Controller
{

    public function execute($args, $assoc_args)
    {
        \WP_CLI::log("Starting plugin activation...");

        // Clear cache before activation
        wp_cache_delete('alloptions', 'options');

        if (is_plugin_active(WP2_NEW_PLUGIN_SLUG)) {
            \WP_CLI::log("Plugin is already active.");
            return;
        }

        // Attempt to activate the plugin
        $result = \WP_CLI::runcommand(
            "plugin activate " . WP2_NEW_PLUGIN_SLUG,
            ['return' => true, 'exit_error' => false]
        );
        \WP_CLI::log("Activation command output: " . $result);

        // Clear cache after activation
        wp_cache_delete('alloptions', 'options');

        if (is_plugin_active(WP2_NEW_PLUGIN_SLUG)) {
            \WP_CLI::log("Plugin activated successfully.");
            $notification = new NotificationService();
            $notification->send([
                'event'   => 'plugin_activated',
                'message' => 'Plugin activated successfully.'
            ]);
        } else {
            \WP_CLI::log("Plugin activation failed.");
        }
    }
}
