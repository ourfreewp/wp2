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
        $this->define_constants();
        add_action('init', [$this, 'initialize_blockstudio']);
    }

    private function define_constants()
    {
        if (!defined('WP2_NAMESPACE')) {
            define('WP2_NAMESPACE', 'wp2');
            define('WP2_PREFIX', 'wp2_');
            define('WP2_TEXTDOMAIN', 'wp2');

            define('WP2_MU_PLUGIN_NAME', 'wp2');
            define('WP2_MU_PLUGIN_DIR', WP2_PLUGIN_DIR . WP2_MU_PLUGIN_NAME);

            // Generate site-specific plugin directory
            $site_domain = defined('WP_SITEURL')
                ? parse_url(WP_SITEURL, PHP_URL_HOST)
                : parse_url(site_url(), PHP_URL_HOST);
            $site_domain = sanitize_title($site_domain);

            define('WP2_STD_PLUGIN_NAME', WP2_MU_PLUGIN_NAME . '-' . $site_domain);
            define('WP2_STD_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins/' . WP2_STD_PLUGIN_NAME);
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
