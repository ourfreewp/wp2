<?php

namespace WP2\BLUEPRINT\Template;

/**
 * Handles plugin deactivation logic.
 */
class Deactivate
{
    /**
     * Registers the deactivation hook.
     *
     * @return void
     */
    public static function register_deactivation_hook()
    {
        // Register the deactivation hook to trigger the handle method
        register_deactivation_hook(__FILE__, [self::class, 'handle']);
    }

    /**
     * Handles the deactivation process.
     * This method will be called when the plugin is deactivated.
     *
     * @return void
     */
    public static function handle()
    {
        try {

            // Log successful deactivation
            \WP_CLI::log('Plugin deactivated successfully.');
        } catch (\Exception $e) {
            // Log any errors encountered during deactivation
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 BLUEPRINT Template] Deactivation error: ' . $e->getMessage());
            }
            \WP_CLI::error('Error during plugin deactivation: ' . $e->getMessage());
        }
    }
}

// Register deactivation logic when the class is included
Deactivate::register_deactivation_hook();
