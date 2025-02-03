<?php

namespace WP2\Bootstrap\Template;

class Uninstall
{
    /**
     * Main handler for the uninstallation process.
     *
     * @return void
     * @throws \Exception If any error occurs during uninstallation.
     */
    public static function handle()
    {
        try {
            self::clean_wp_config_options();

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 Bootstrap Template] Uninstall completed successfully.');
            }
        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 Bootstrap Template] Uninstall error: ' . $e->getMessage());
            }
            throw $e; // Rethrow the exception for logging by the uninstall file.
        }
    }

    /**
     * Clean up any options stored in the wp-config.php file.
     *
     * @return void
     */
    private static function clean_wp_config_options()
    {
        // Blockstudio Options
        self::unset_blockstudio_wp_config_options();
    }

    /**
     * Blockstudio Options
     * 
     * @return void
     */
    private static function unset_blockstudio_wp_config_options()
    {
        if (defined('WP2_BLOCKSTUDIO_LICENSE')) {
            self::remove_wp_config_option('WP2_BLOCKSTUDIO_LICENSE');
        }

        if (defined('WP2_BOOTSTRAP_WEBHOOK_URL')) {
            self::remove_wp_config_option('WP2_BOOTSTRAP_WEBHOOK_URL');
        }
    }

    /**
     * Remove a constant from the wp-config.php file.
     *
     * @param string $constant The name of the constant to remove.
     * @return void
     */
    private static function remove_wp_config_option($constant)
    {
        $config_path = ABSPATH . 'wp-config.php';

        // Ensure the file exists and is writable
        if (!file_exists($config_path) || !is_writable($config_path)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 Bootstrap Template] Cannot modify wp-config.php. Check file permissions.");
            }
            return;
        }

        // Get the contents of the wp-config.php file
        $config_file = file_get_contents($config_path);

        if ($config_file === false) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 Bootstrap Template] Failed to read wp-config.php.");
            }
            return;
        }

        // Use preg_quote to safely escape the constant name
        $constant_safe = preg_quote($constant, '/');

        // Remove the constant definition from the file
        $config_file = preg_replace('/define\s*\(\s*[\'"]' . $constant_safe . '[\'"]\s*,\s*[^;]+;\s*\n?/m', '', $config_file);

        // Ensure the regex operation was successful
        if ($config_file === null) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 Bootstrap Template] Regex replacement failed for {$constant}.");
            }
            return;
        }

        // Write the updated contents back to the file
        if (file_put_contents($config_path, $config_file) === false) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 Bootstrap Template] Failed to write changes to wp-config.php.");
            }
        }
    }
}
