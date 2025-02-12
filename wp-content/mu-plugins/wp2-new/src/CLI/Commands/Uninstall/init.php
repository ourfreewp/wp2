<?php
// Path: wp-content/mu-plugins/wp2-new/src/CLI/Commands/Uninstall/init.php
/**
 * Uninstall Command
 *
 * Implements the WP-CLI command to uninstall the plugin.
 * It ensures that the plugin is deactivated before running the uninstallation and sends notifications upon completion.
 *
 * @package WP2_Daemon\WP2_New\CLI\Commands\Uninstall
 */

namespace WP2_Daemon\WP2_New\CLI\Commands\Uninstall;

use WP2_Daemon\WP2_New\Services\Notification\NotificationService;

class Controller
{

    public function execute($args, $assoc_args)
    {
        \WP_CLI::log("Starting plugin uninstallation...");

        // Ensure the plugin is deactivated first
        if (is_plugin_active(WP2_NEW_PLUGIN_SLUG)) {
            \WP_CLI::log("Plugin is active, attempting deactivation...");
            \WP_CLI::runcommand("wp2-new deactivate", ['return' => true, 'exit_error' => false]);
        }

        // Attempt to uninstall the plugin
        $result = \WP_CLI::runcommand(
            "plugin uninstall " . WP2_NEW_PLUGIN_SLUG,
            ['return' => true, 'exit_error' => false]
        );
        if ($result) {
            \WP_CLI::log("Plugin uninstalled successfully.");
            $notification = new NotificationService();
            $notification->send([
                'event'   => 'plugin_uninstalled',
                'message' => 'Plugin uninstalled successfully.'
            ]);
        } else {
            \WP_CLI::log("Plugin uninstallation failed.");
        }
    }
}
