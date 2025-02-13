<?php
// Path: wp-content/mu-plugins/wp2-studio/src/Handlers/Instance/init.php
/**
 * Instance Handler for WP2 Studio.
 * This handler is responsible for managing the initialization of Blockstudio instances.
 * 
 */

namespace WP2_Daemon\WP2_Studio\Handlers\Instance;

class Controller
{
    /**
     * @var array Registered directories.
     */

    private $option_name = 'wp2_studio_instances';


    private $directories = [];

    public function __construct()
    {
        add_action('init', [$this, 'initialize_instances'], 100);
    }

    public function get_directory_data()
    {
        $option = get_option($this->option_name);

        return $option ? $option : [];
    }

    public function add_new_directory(string $dir)
    {
        $directories = $this->get_directory_data();

        if (!in_array($dir, $directories)) {
            $directories[] = $dir;

            // ensure unique values
            $directories = array_unique($directories);
            update_option($this->option_name, $directories);
        }
    }

    public function register_directories(array $directories)
    {
        foreach ($directories as $dir) {
            $this->register_directory($dir);
        }
    }

    /**
     * Registers a directory for Blockstudio initialization.
     *
     * @param string $dir Full filesystem path to the directory.
     * @return bool True if the directory was successfully registered, false otherwise.
     */
    public function register_directory(string $dir): bool
    {
        // Sanitize the directory path.
        $dir = trailingslashit($dir);

        // Validate: non-empty string.
        if (empty($dir)) {
            error_log('[WP2 Instance] Empty directory passed for registration.');
            return false;
        }

        // Optionally check if directory exists now; if it doesn't exist, you might choose to register anyway.
        if (!is_dir($dir)) {
            error_log("[WP2 Instance] Directory '{$dir}' does not exist. Registration skipped.");
            return false;
        }

        // Add the directory to the list of registered directories.
        $this->add_new_directory($dir);

        // Return true to indicate successful registration.

        return true;
    }

    /**
     * Returns directories that belong to disabled plugins.
     *
     * @param array $directories List of directories to check.
     * @return array Directories from within WP_PLUGIN_DIR that do not belong to active plugins.
     */
    private function plugin_filter(array $directories): array
    {
        // Retrieve active plugins (defaults to an empty array).
        $active_plugins = get_option('active_plugins', []);

        // Convert each active plugin to its base directory.
        $active_plugin_dirs = array_map(function ($plugin) {
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin;
            return trailingslashit(dirname($plugin_path));
        }, $active_plugins);

        // Identify directories in the plugins folder that do NOT belong to an active plugin.
        return array_filter($directories, function ($dir) use ($active_plugin_dirs) {
            // Only consider directories that are in the plugins folder.
            if (strpos($dir, WP_PLUGIN_DIR) !== 0) {
                return false;
            }

            // Ensure the directory has a trailing slash.
            $dir_trail = trailingslashit($dir);

            // If the directory starts with any active plugin directory, it's active.
            foreach ($active_plugin_dirs as $active_dir) {
                if (strpos($dir_trail, $active_dir) === 0) {
                    return false;
                }
            }

            // Otherwise, it belongs to a disabled plugin.
            return true;
        });
    }

    /**
     * Returns directories that belong to inactive themes.
     *
     * @param array $directories List of directories to check.
     * @return array Directories from within the themes folder that do not belong to an active theme.
     */
    private function theme_filter(array $directories): array
    {
        // Get the active theme (child theme if available) and parent theme.
        $active_stylesheet = get_option('stylesheet');
        $active_template   = get_option('template');

        $active_theme_dirs = [];
        if ($active_stylesheet) {
            $active_theme_dirs[] = trailingslashit(WP_CONTENT_DIR . '/themes/' . $active_stylesheet);
        }
        if ($active_template && $active_template !== $active_stylesheet) {
            $active_theme_dirs[] = trailingslashit(WP_CONTENT_DIR . '/themes/' . $active_template);
        }

        $themes_base = WP_CONTENT_DIR . '/themes/';

        // Identify directories in the themes folder that are not the active theme.
        return array_filter($directories, function ($dir) use ($active_theme_dirs, $themes_base) {
            if (strpos($dir, $themes_base) !== 0) {
                return false;
            }

            $dir_trail = trailingslashit($dir);
            foreach ($active_theme_dirs as $active_dir) {
                if (strpos($dir_trail, $active_dir) === 0) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Filters out directories that belong to disabled plugins or inactive themes.
     *
     * @param array $directories List of directories to filter.
     * @return array The filtered list of directories.
     */
    private function filter_disabled_directories(array $directories): array
    {
        // Build exclusion lists from both plugin and theme filters.
        $disabled_plugin_dirs = $this->plugin_filter($directories);
        $disabled_theme_dirs  = $this->theme_filter($directories);

        // Merge the two exclusion lists.
        $exclusions = array_merge($disabled_plugin_dirs, $disabled_theme_dirs);
        $exclusions = array_unique($exclusions);

        // Return directories that do NOT match any exclusion.
        return array_filter($directories, function ($dir) use ($exclusions) {
            $dir_trail = trailingslashit($dir);
            foreach ($exclusions as $exclude) {
                if (strpos($dir_trail, $exclude) === 0) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Initialize Blockstudio for each registered directory.
     *
     * This method is hooked into the WordPress init action.
     */
    public function initialize_instances()
    {
        $directories = $this->get_directory_data();

        // filter out directories that belong to disabled plugins
        $directories = $this->filter_disabled_directories($directories);

        do_action('qm/debug', 'WP2 Studio Instances ' . print_r($directories, true));

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                \Blockstudio\Build::init([
                    'dir' => $dir,
                ]);
            } else {
                error_log("[WP2 Instance] Registered directory '{$dir}' no longer exists.");
            }
        }
    }
}

new Controller();
