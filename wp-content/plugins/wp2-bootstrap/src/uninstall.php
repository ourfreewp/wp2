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
            self::make_final_mods();

            // Clear all transients from the database.
            self::clear_transients();

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
    private static function make_final_mods()
    {
        // Blockstudio Options
        self::make_final_mods_blockstudio();
    }

    /**
     * Blockstudio Options
     * 
     * @return void
     */
    private static function make_final_mods_blockstudio()
    {
        if (defined('WP2_BLOCKSTUDIO_LICENSE')) {
            self::remove_wp_config_option('WP2_BLOCKSTUDIO_LICENSE');
        }

        if (defined('WP2_BOOTSTRAP_WEBHOOK_URL')) {
            self::remove_wp_config_option('WP2_BOOTSTRAP_WEBHOOK_URL');
        }

        delete_option('blockstudio_license_key');
        delete_option('blockstudio_license_key_const');
        delete_option('blockstudio_license_status');
    }

    /**
     * Clear all transients from the database.
     * 
     * @return void
     */
    private static function clear_transients()
    {
        global $wpdb;

        // Delete all transient values.
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");

        // Delete all transient timeout values.
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_%'");
    }


    /**
     * Get the contents of wp-config.php.
     *
     * @return string|false The contents of wp-config.php, or false on error.
     */
    private static function get_wp_config_contents()
    {
        $config_path = ABSPATH . 'wp-config.php';

        // Check file existence and writability.
        if (!file_exists($config_path) || !is_writable($config_path)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 Bootstrap Template] Cannot modify wp-config.php. Check file permissions.");
            }
            return false;
        }

        $contents = file_get_contents($config_path);
        if ($contents === false) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 Bootstrap Template] Failed to read wp-config.php.");
            }
        }
        return $contents;
    }

    /**
     * Write the updated contents back to wp-config.php.
     *
     * @param string $contents The updated file contents.
     * @return bool True on success, false on failure.
     */
    private static function write_wp_config_contents($contents)
    {
        $config_path = ABSPATH . 'wp-config.php';
        if (file_put_contents($config_path, $contents) === false) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 Bootstrap Template] Failed to write changes to wp-config.php.");
            }
            return false;
        }
        return true;
    }

    /**
     * Build a verbose regular expression pattern to match the constant definition.
     *
     * @param string $constant The constant name.
     * @return string The regex pattern.
     */
    private static function build_constant_pattern($constant)
    {
        // Escape the constant name to ensure it is safely matched.
        $constant_safe = preg_quote($constant, '/');

        // Build the verbose regex pattern.
        // Explanation:
        //   - "define" followed by optional whitespace.
        //   - An opening parenthesis "(" with optional whitespace.
        //   - A quoted constant name (single or double quotes).
        //   - A comma with optional surrounding whitespace.
        //   - A quoted constant value (any characters except the closing quote).
        //   - A closing parenthesis and a semicolon with optional whitespace.
        return '/
            define\s*                                   # Match "define" with optional whitespace.
            \(\s*                                       # Match an opening parenthesis with optional whitespace.
            [\'"]' . $constant_safe . '[\'"]\s*,\s*     # Match the constant name in quotes, followed by a comma.
            [\'"][^\'"]*[\'"]\s*                        # Match the constant value in quotes.
            \)\s*;                                      # Match a closing parenthesis and semicolon with optional whitespace.
        /x';
    }

    /**
     * Update the constant's value in the provided configuration content.
     *
     * @param string $config_file The contents of wp-config.php.
     * @param string $constant The constant name.
     * @param string $new_value The new value for the constant.
     * @return string|null The updated configuration, or null on regex failure.
     */
    private static function update_constant_value($config_file, $constant, $new_value)
    {
        $pattern = self::build_constant_pattern($constant);
        $replacement = 'define("' . $constant . '", "' . $new_value . '");';

        // Perform the regex replacement.
        $updated = preg_replace($pattern, $replacement, $config_file);

        if ($updated === null) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 Bootstrap Template] Regex replacement failed for {$constant}.");
            }
        }
        return $updated;
    }

    /**
     * Remove a constant from the wp-config.php file by setting its value to an empty string.
     *
     * @param string $constant The name of the constant to clear.
     * @return void
     */
    private static function remove_wp_config_option($constant)
    {
        // Fetch the current contents of wp-config.php.
        $config_file = self::get_wp_config_contents();
        if ($config_file === false) {
            return;
        }

        // Update the constant's value to an empty string.
        $updated_config = self::update_constant_value($config_file, $constant, '');
        if ($updated_config === null) {
            return;
        }

        // Write the updated contents back to wp-config.php.
        self::write_wp_config_contents($updated_config);
    }
}
