<?php

namespace WP2\New;

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
                error_log('[WP2 New] Uninstall completed successfully.');
            }
        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[WP2 New] Uninstall error: ' . $e->getMessage());
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
        self::make_final_mods_fathom_analytics();
        self::make_final_mods_ws_form();
        self::make_final_mods_meta_box();
        self::make_final_mods_admin_columns();
        self::make_final_mods_edd();
        self::make_final_mods_fullpagejs();
        self::make_final_mods_user();

        self::make_final_plugin_uninstalls();
    }

    /**
     * Make final uninstalls
     * 
     * @return void
     */
    private static function make_final_plugin_uninstalls()
    {
        $plugins = [
            'wp2-demo/wp2-demo.php',
        ];

        foreach ($plugins as $plugin) {
            self::uninstall_wp_plugin($plugin);
        }
    }

    /**
     * Make final mods for user
     * 
     * @return void
     */
    private static function make_final_mods_user()
    {
        $user_id = 1;
        $user = get_user_by('id', $user_id);

        // force upsert random username for user 1 in all places
        if ($user) {
            $new_username = wp_generate_password(8, false);
            $user->user_login = $new_username;
            $user->user_nicename = $new_username;
            wp_update_user($user);
        }

        global $wpdb;

        $wpdb->update(
            $wpdb->users,
            [
                'user_login' => $new_username,
                'user_nicename' => $new_username,
            ],
            ['ID' => $user_id]
        );

        // flush user cache
        wp_cache_delete($user_id, 'users');
    }

    /**
     * Admin Columns Pro Options
     * 
     * @return void
     */
    private static function make_final_mods_admin_columns()
    {
        $vars = [
            'ACP_LICENSE_KEY',
        ];

        foreach ($vars as $var) {
            if (defined($var)) {
                self::remove_wp_config_option($var);
            }
        }
    }

    /**
     * Meta Box Options
     * 
     * @return void
     */
    private static function make_final_mods_meta_box()
    {
        $vars = [
            'META_BOX_KEY',
        ];

        foreach ($vars as $var) {
            if (defined($var)) {
                self::remove_wp_config_option($var);
            }
        }
    }

    /**
     * FullPagejs Options
     * 
     * @return void
     */
    private static function make_final_mods_fullpagejs()
    {
        // WP2_FULLPAGEJS_LICENSE_KEY
        $vars = [
            'WP2_FULLPAGEJS_LICENSE_KEY',
        ];
        foreach ($vars as $var) {
            if (defined($var)) {
                self::remove_wp_config_option($var);
            }
        }
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

        if (defined('WP2_NEW_WEBHOOK_URL')) {
            self::remove_wp_config_option('WP2_NEW_WEBHOOK_URL');
        }

        delete_option('blockstudio_license_key');
        delete_option('blockstudio_license_key_const');
        delete_option('blockstudio_license_status');
    }

    /**
     * Fathom Analytics Options
     * 
     * @return void
     */
    private static function make_final_mods_fathom_analytics()
    {
        delete_option('fathom_site_id');
    }

    /**
     * WS Form Options
     * 
     * @return void
     */
    private static function make_final_mods_ws_form()
    {
        $vars = [
            'WSF_LICENSE_KEY',
            'WSF_ACTION_INSTAWPV2_LICENSE_KEY',
            'WSF_ACTION_INSTAWPV2_API_KEY',
            'WSF_ACTION_KLAVIYOV2_LICENSE_KEY',
            'WSF_ACTION_KLAVIYOV2_API_KEY_PRIVATE',
            'WSF_ACTION_OPENAIV1_LICENSE_KEY',
            'WSF_ACTION_OPENAIV1_API_KEY',
            'WSF_ACTION_OPENAIV1_API_ORG_ID',
            'WSF_ACTION_PAYPALCHECKOUT_LICENSE_KEY',
            'WSF_ACTION_PAYPALCHECKOUT_ENVIRONMENT',
            'WSF_ACTION_PAYPALCHECKOUT_SANDBOX_CLIENT_ID',
            'WSF_ACTION_PAYPALCHECKOUT_LIVE_CLIENT_ID',
            'WSF_ACTION_PDF_LICENSE_KEY',
            'WSF_ACTION_POST_LICENSE_KEY',
            'WSF_ACTION_SLACK_LICENSE_KEY',
            'WSF_ACTION_STRIPEELEMENTS_LICENSE_KEY',
            'WSF_ACTION_STRIPEELEMENTS_ENVIRONMENT',
            'WSF_ACTION_STRIPEELEMENTS_TEST_PUBLISHABLE_KEY',
            'WSF_ACTION_STRIPEELEMENTS_TEST_SECRET_KEY',
            'WSF_ACTION_STRIPEELEMENTS_LIVE_PUBLISHABLE_KEY',
            'WSF_ACTION_STRIPEELEMENTS_LIVE_SECRET_KEY',
            'WSF_ACTION_USER_LICENSE_KEY',
            'WSF_ACTION_COMMENT_LICENSE_KEY',
            'WS_FORM_ENCRYPTION_KEY',
        ];
        foreach ($vars as $var) {
            if (defined($var)) {
                self::remove_wp_config_option($var);
            }
        }

        $options = [
            'ws_form',
        ];

        foreach ($options as $option) {
            delete_option($option);
        }

        $table_patterns = [
            'wsf_%',
        ];

        self::drop_tables_by_patterns($table_patterns);
    }

    /**
     * Clear EDD Software Licensing options.
     * 
     * @return void
     */

    private static function make_final_mods_edd()
    {
        $patterns = [
            'edd_sl_%',
        ];

        self::delete_table_options_by_patterns($patterns);
    }

    /**
     * Uninstall a plugin by deactivating, running its uninstall process, and deleting it.
     *
     * @param string $plugin_file The main plugin file (e.g., 'plugin-folder/plugin-file.php').
     * @return void
     */
    private static function uninstall_wp_plugin($plugin_file)
    {
        if (!function_exists('deactivate_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugin_dir = WP_CONTENT_DIR . '/plugins/' . dirname($plugin_file);
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

        // Check if the plugin is active and deactivate it.
        if (is_plugin_active($plugin_file)) {
            deactivate_plugins($plugin_file);
        }

        // Run the plugin's uninstall process if it has an uninstall.php file.
        $uninstall_file = $plugin_dir . '/uninstall.php';
        if (file_exists($uninstall_file)) {
            include_once $uninstall_file;
        }

        // Delete the plugin files.
        if (file_exists($plugin_path)) {
            if (!self::delete_directory($plugin_dir)) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("[WP2 New] Failed to delete plugin directory: {$plugin_dir}");
                }
            }
        }
    }

    /**
     * Recursively delete a directory and its contents using WP_Filesystem API.
     *
     * @param string $dir Path to the directory.
     * @return bool True on success, false on failure.
     */
    private static function delete_directory($dir)
    {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        WP_Filesystem(); // Initialize the filesystem

        if (!isset($wp_filesystem) || !is_object($wp_filesystem)) {
            return false;
        }

        return $wp_filesystem->delete($dir, true); // Recursive delete
    }

    /**
     * Clear all transients from the database.
     * 
     * @return void
     */
    private static function clear_transients()
    {
        $patterns = [
            '_transient_%',
            '_transient_timeout_%',
        ];

        self::delete_table_options_by_patterns($patterns);
    }

    /**
     * Delete options matching any of the provided LIKE patterns.
     *
     * @param array $patterns Array of LIKE patterns.
     * @return int|false Number of rows affected, or false on error.
     */
    private static function delete_table_options_by_patterns(array $patterns)
    {
        global $wpdb;

        if (! isset($wpdb) || ! is_object($wpdb)) {
            return false;
        }

        $table      = $wpdb->options;
        $conditions = [];
        $args       = [];

        foreach ($patterns as $pattern) {
            $conditions[] = 'option_name LIKE %s';
            $args[]       = $pattern;
        }

        $where = implode(' OR ', $conditions);
        $sql   = $wpdb->prepare("DELETE FROM {$table} WHERE {$where}", ...$args);
        return $wpdb->query($sql);
    }

    /**
     * Drop tables whose names match any of the provided LIKE patterns.
     *
     * Patterns that don’t include a SQL wildcard (i.e. '%') will be automatically
     * wrapped with '%' so they match anywhere in the table name. Additionally, 
     * the WordPress database prefix will be automatically prepended to patterns 
     * that do not already include it.
     *
     * @param array $patterns Array of pattern strings.
     * @return int|false Number of tables dropped, or false on error.
     */
    private static function drop_tables_by_patterns(array $patterns)
    {
        global $wpdb;

        if (! isset($wpdb) || ! is_object($wpdb)) {
            return false;
        }

        $dropped      = 0;
        $tablesToDrop = [];

        foreach ($patterns as $pattern) {
            // If no SQL wildcard is provided, assume the pattern should match anywhere.
            if (strpos($pattern, '%') === false) {
                $pattern = '%' . $pattern . '%';
            }

            // Ensure the pattern is prefixed with the WordPress DB prefix if it's not already included.
            if (strpos($pattern, $wpdb->prefix) !== 0) {
                $pattern = $wpdb->prefix . $pattern;
            }

            // Retrieve tables matching the current pattern.
            $query  = $wpdb->prepare("SHOW TABLES LIKE %s", $pattern);
            $tables = $wpdb->get_col($query);

            if (! empty($tables)) {
                $tablesToDrop = array_merge($tablesToDrop, $tables);
            }
        }

        // Remove duplicate table names.
        $tablesToDrop = array_unique($tablesToDrop);

        foreach ($tablesToDrop as $table) {
            // Escape backticks in the table name.
            $tableEscaped = '`' . str_replace('`', '``', $table) . '`';
            $result       = $wpdb->query("DROP TABLE IF EXISTS {$tableEscaped}");

            if (false !== $result) {
                $dropped++;
            }
        }

        return $dropped;
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
                error_log("[WP2 New] Cannot modify wp-config.php. Check file permissions.");
            }
            return false;
        }

        $contents = file_get_contents($config_path);
        if ($contents === false) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("[WP2 New] Failed to read wp-config.php.");
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
                error_log("[WP2 New] Failed to write changes to wp-config.php.");
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
                error_log("[WP2 New] Regex replacement failed for {$constant}.");
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
