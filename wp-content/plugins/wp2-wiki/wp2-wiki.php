<?php
// Path: wp-content/plugins/wp2/wp2-wiki.php
/**
 * Plugin Name: WP2 Wiki
 * Description: The wiki plugin for the WP2 website.
 * Version: 1.0
 **/

namespace WP2_Wiki;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WP2_WIKI_DIR')) {
    define('WP2_WIKI_DIR', plugin_dir_path(__FILE__));
}

if (!defined('WP2_WIKI_URL')) {
    define('WP2_WIKI_URL', plugin_dir_url(__FILE__));
}

if (!defined('WP2_THEME_DIR')) {
    define('WP2_THEME_DIR', get_template_directory());
}

if (!defined('WP2_THEME_URL')) {
    define('WP2_THEME_URL', get_template_directory_uri());
}

if (!defined('WP2_CORE_DIR')) {
    define('WP2_CORE_DIR', WP_CONTENT_DIR . '/plugins/wp2');
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


    public function filter_user_ids($user_ids)
    {
        return array_merge($user_ids, defined('WP2_BLOCKSTUDIO_USERS') ? WP2_BLOCKSTUDIO_USERS : []);
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
            WP2_WIKI_DIR . '/src/Assets',
            WP2_WIKI_DIR . '/src/Blocks/Namespaces/wp2-wiki',
            WP2_WIKI_DIR . '/src/Helpers',
            WP2_WIKI_DIR . '/src/Templates',
            WP2_WIKI_DIR . '/src/Types',
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
