<?php
// Path: wp-content/mu-plugins/wp2-new/src/CLI/Commands/Deactivate/init.php
/**
 * Deactivate Command
 *
 * Implements the WP-CLI command to deactivate the plugin.
 * This command clears caches, adjusts the active plugins list, and sends notifications upon completion.
 *
 * @package WP2_Daemon\WP2_New\CLI\Commands\Deactivate
 */

namespace WP2_Daemon\WP2_New\CLI\Commands\Deactivate;

use WP2_Daemon\WP2_New\Services\Notification\NotificationService;

class Controller
{

    public function execute($args, $assoc_args)
    {
        \WP_CLI::log("Starting plugin deactivation...");

        // Attempt deactivation via WP-CLI command
        $result = \WP_CLI::runcommand(
            "plugin deactivate " . WP2_NEW_PLUGIN_SLUG,
            ['return' => true, 'exit_error' => false]
        );
        \WP_CLI::log("Deactivation command output: " . $result);

        // Clear cache and adjust the active_plugins option if needed
        wp_cache_delete('alloptions', 'options');
        $active_plugins = get_option('active_plugins', []);
        if (in_array(WP2_NEW_PLUGIN_SLUG, $active_plugins, true)) {
            $active_plugins = array_diff($active_plugins, [WP2_NEW_PLUGIN_SLUG]);
            update_option('active_plugins', $active_plugins);
            wp_cache_delete('alloptions', 'options');
            \WP_CLI::log("Plugin forcibly removed from active_plugins.");
        }

        if (! is_plugin_active(WP2_NEW_PLUGIN_SLUG)) {
            \WP_CLI::log("Plugin deactivated successfully.");
            $notification = new NotificationService();
            $notification->send([
                'event'   => 'plugin_deactivated',
                'message' => 'Plugin deactivated successfully.'
            ]);
        } else {
            \WP_CLI::log("Plugin deactivation failed.");
        }
    }
}
