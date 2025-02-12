<?php

/**
 * Plugin Name: WP2 Daemon
 * Description: Autoloader and Initializer for WP2 Daemon Modules
 * Version: 1.0
 * Author: WP2S
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


if (!defined('WP2_MU_PLUGIN_DIR')) {
    define('WP2_MU_PLUGIN_DIR', WPMU_PLUGIN_DIR);
}

if (!defined('WP2_CORE_DIR')) {
    define('WP2_CORE_DIR', WP_CONTENT_DIR . '/plugins/wp2');
}

if (!defined('WP2_NEW_DIR')) {
    define('WP2_NEW_DIR', WP_CONTENT_DIR . '/plugins/wp2-new');
}

if (!defined('WP2_WIKI_DIR')) {
    define('WP2_WIKI_DIR', WP_CONTENT_DIR . '/plugins/wp2-wiki');
}

if (!defined('WP2_WORK_DIR')) {
    define('WP2_WORK_DIR', WP_CONTENT_DIR . '/plugins/wp2-work');
}

class WP2_Daemon
{

    public function __construct()
    {
        $this->define_constants();
        add_filter('blockstudio/settings/users/ids', [$this, 'filter_user_ids']);
        $this->autoload_classes();
        $this->load_module_initializers();
    }

    private function define_constants()
    {
        if (!defined('WP2_NAMESPACE')) {
            define('WP2_NAMESPACE', 'wp2');
            define('WP2_PREFIX', 'wp2_');
            define('WP2_TEXTDOMAIN', 'wp2');

            // Generate site-specific plugin directory
            $site_domain = defined('WP_SITEURL')
                ? parse_url(WP_SITEURL, PHP_URL_HOST)
                : parse_url(site_url(), PHP_URL_HOST);
            $site_domain = sanitize_title($site_domain);
        }
    }

    public function filter_user_ids($user_ids)
    {
        return array_merge($user_ids, defined('WP2_BLOCKSTUDIO_USERS') ? WP2_BLOCKSTUDIO_USERS : []);
    }

    private function autoload_classes()
    {
        spl_autoload_register(function ($class) {
            $namespace_prefix = 'WP2_Daemon\\WP2_';

            if (strpos($class, $namespace_prefix) === 0) {
                $relative_class = substr($class, strlen($namespace_prefix));
                $relative_path = str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $relative_class);

                foreach (glob(WP2_MU_PLUGIN_DIR . '/wp2-*', GLOB_ONLYDIR) as $module_dir) {
                    $file = $module_dir . '/src/' . $relative_path . '.php';
                    if (file_exists($file)) {
                        require_once $file;
                        return;
                    }
                }

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("[WP2 Autoloader Error] Unable to load class '{$class}'. Searched in wp2-* modules.");
                }
            }
        });
    }

    private function load_module_initializers()
    {
        $modules = glob(WP2_MU_PLUGIN_DIR . '/wp2-*', GLOB_ONLYDIR);
        foreach ($modules as $module_dir) {
            $directory = new RecursiveDirectoryIterator($module_dir . '/src');
            $iterator  = new RecursiveIteratorIterator($directory);
            foreach ($iterator as $file) {
                if ($file->isFile() && stripos($file->getFilename(), 'init') === 0) {
                    require_once $file->getPathname();
                }
            }
        }
    }
}

new WP2_Daemon();
