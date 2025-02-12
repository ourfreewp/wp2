<?php
// Path: wp-content/plugins/wp2-work/wp2-work.php
/**
 * Plugin Name: WP2 Work
 * Description: The work related functionality for the WP2.
 * Version: 1.0
 **/

namespace WP2_Work;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WP2_PLUGIN_DIR')) {
    define('WP2_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('WP2_PLUGIN_URL')) {
    define('WP2_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('WP2_THEME_DIR')) {
    define('WP2_THEME_DIR', get_template_directory());
}

if (!defined('WP2_THEME_URL')) {
    define('WP2_THEME_URL', get_template_directory_uri());
}
/**
 * WP2 Work Plugin
 */
class Init
{

    public function __construct()
    {
        add_action('init', [$this, 'initialize_blockstudio']);
    }

    public function initialize_blockstudio()
    {
        if (defined('BLOCKSTUDIO')) {
            $directories = $this->get_plugin_directories();
            $this->initialize_directories($directories);
        }
    }

    private function get_plugin_directories()
    {
        return [
            WP2_PLUGIN_DIR . '/src/Helpers',
            WP2_PLUGIN_DIR . '/src/Types',
        ];
    }

    private function initialize_directories($directories)
    {
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                \Blockstudio\Build::init([
                    'dir' => $dir,
                ]);
            }
        }
    }
}
/**
 * Initialize the plugin
 */

new Init();
