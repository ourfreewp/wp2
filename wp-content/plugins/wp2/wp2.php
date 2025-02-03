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

// Define core plugin constants
define('WP2_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP2_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP2_THEME_DIR', WP_CONTENT_DIR . '/themes/wp2/');
define('WP2_THEME_URL', WP_CONTENT_URL . '/themes/wp2/');

/**
 * WP2 Bootstrap Class
 */
class Bootstrap
{

    public function __construct()
    {
        $this->define_constants();
        add_filter('blockstudio/settings/users/ids', [$this, 'filter_user_ids']);
        add_action('init', [$this, 'initialize_blockstudio']);
    }

    private function define_constants()
    {
        if (!defined('WP2_NAMESPACE')) {
            define('WP2_NAMESPACE', 'wp2');
            define('WP2_PREFIX', 'wp2_');
            define('WP2_TEXTDOMAIN', 'wp2');

            define('WP2_MU_PLUGIN_NAME', 'wp2');
            define('WP2_MU_PLUGIN_DIR', WP2_PLUGIN_DIR . WP2_MU_PLUGIN_NAME . '/');

            // Generate site-specific plugin directory
            $site_domain = defined('WP_SITEURL')
                ? parse_url(WP_SITEURL, PHP_URL_HOST)
                : parse_url(site_url(), PHP_URL_HOST);
            $site_domain = sanitize_title($site_domain);

            define('WP2_STD_PLUGIN_NAME', WP2_MU_PLUGIN_NAME . '-' . $site_domain);
            define('WP2_STD_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins/' . WP2_STD_PLUGIN_NAME . '/');
        }
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
            WP2_PLUGIN_DIR . 'src/Assets',
            WP2_PLUGIN_DIR . 'src/BlockExtensions',
            WP2_PLUGIN_DIR . 'src/Blocks',
            WP2_PLUGIN_DIR . 'src/Filters',
            WP2_PLUGIN_DIR . 'src/Helpers',
            WP2_PLUGIN_DIR . 'src/Templates',
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

// Initialize the Bootstrap class
new Bootstrap();
