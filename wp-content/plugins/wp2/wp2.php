<?php
// Path: wp-content/plugins/wp2/wp2.php
/**
 * Plugin Name: WP2
 * Description: The core plugin for the WP2 website.
 * Version: 1.0
 **/

namespace WP2;

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
 * WP2 Core Plugin
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
            WP2_CORE_DIR . '/src/Assets',
            WP2_CORE_DIR . '/src/Blocks/Namespaces/core',
            WP2_CORE_DIR . '/src/Blocks/Namespaces/wp2',
            WP2_CORE_DIR . '/src/Blocks/Settings',
            WP2_CORE_DIR . '/src/Elements',
            WP2_CORE_DIR . '/src/Helpers',
            WP2_CORE_DIR . '/src/Templates',
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
