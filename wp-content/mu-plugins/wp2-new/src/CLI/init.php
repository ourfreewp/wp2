<?php
// Path: wp-content/mu-plugins/wp2-new/src/CLI/init.php
/**
 * CLI Initialization
 *
 * Registers all WP-CLI commands for the WP2 New plugin.
 * Commands include activation, deactivation, uninstallation, and a full lifecycle cycle.
 */

namespace WP2_Daemon\WP2_New\CLI;

if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command(
        'wp2-new activate',
        [new \WP2_Daemon\WP2_New\CLI\Commands\Activate\ActivateCommand(), 'execute']
    );
    \WP_CLI::add_command(
        'wp2-new deactivate',
        [new \WP2_Daemon\WP2_New\CLI\Commands\Deactivate\DeactivateCommand(), 'execute']
    );
    \WP_CLI::add_command(
        'wp2-new uninstall',
        [new \WP2_Daemon\WP2_New\CLI\Commands\Uninstall\UninstallCommand(), 'execute']
    );
    \WP_CLI::add_command(
        'wp2-new cycle',
        [new \WP2_Daemon\WP2_New\CLI\Commands\Cycle\CycleCommand(), 'execute']
    );
}
