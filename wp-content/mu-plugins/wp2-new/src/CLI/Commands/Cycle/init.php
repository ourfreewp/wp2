<?php
// Path: wp-content/mu-plugins/wp2-new/src/CLI/Commands/Cycle/init.php
/**
 * Cycle Command
 *
 * Implements the WP-CLI command that runs the full plugin lifecycle.
 * This command sequentially triggers the activation, deactivation, and uninstallation commands.
 *
 * @package WP2_Daemon\WP2_New\CLI\Commands\Cycle
 */

namespace WP2_Daemon\WP2_New\CLI\Commands\Cycle;

class Controller
{

    public function execute($args, $assoc_args)
    {
        \WP_CLI::log("Starting full plugin lifecycle (cycle)...");

        // Activate
        $activate = \WP_CLI::runcommand(
            "wp2-new activate",
            ['return' => true, 'exit_error' => false]
        );
        if (false === $activate) {
            \WP_CLI::log("Activation failed during lifecycle.");
            return;
        }

        // Deactivate
        $deactivate = \WP_CLI::runcommand(
            "wp2-new deactivate",
            ['return' => true, 'exit_error' => false]
        );
        if (false === $deactivate) {
            \WP_CLI::log("Deactivation failed during lifecycle.");
            return;
        }

        // Uninstall
        $uninstall = \WP_CLI::runcommand(
            "wp2-new uninstall",
            ['return' => true, 'exit_error' => false]
        );
        if (false === $uninstall) {
            \WP_CLI::log("Uninstallation failed during lifecycle.");
            return;
        }

        \WP_CLI::log("Plugin lifecycle cycle completed successfully.");
    }
}
