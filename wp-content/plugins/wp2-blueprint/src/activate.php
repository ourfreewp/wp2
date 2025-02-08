<?php

namespace WP2\BLUEPRINT\Template;

class Activate
{
    /**
     * Registers the activation hook for the plugin.
     *
     * This method will be called when the plugin is activated.
     *
     * @return void
     */
    public static function register_activation_hook()
    {
        register_activation_hook(__FILE__, [self::class, 'handle']);
    }

    /**
     * Main handler for the activation process.
     *
     * @return void
     * @throws \Exception If any error occurs during activation.
     */
    public static function handle()
    {
        try {

            // Log successful activation
            \WP_CLI::log('Plugin activation completed.');
        } catch (\Exception $e) {
            // Log the error if an exception occurs
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 BLUEPRINT Template] Activation error: ' . $e->getMessage());
            }
            throw $e; // Propagate the exception for higher-level handling
        }
    }
}

// Register the activation hook when the class is included
Activate::register_activation_hook();
