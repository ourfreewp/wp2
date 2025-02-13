<?php

/**
 * Plugin Name: WP2 New
 * Description: A utility plugin for setting up WordPress sites with predefined templates, settings, and roles.
 */

namespace WP2\New;

defined('ABSPATH') || exit;

// Define constants
if (!defined('WP2_NEW_VERSION')) {
    define('WP2_NEW_VERSION', '1.0');
}
if (!defined('WP2_NEW_PATH')) {
    define('WP2_NEW_PATH', plugin_dir_path(__FILE__));
}
if (!defined('WP2_NEW_URL')) {
    define('WP2_NEW_URL', plugin_dir_url(__FILE__));
}
if (!defined('WP2_NEW_SLUG')) {
    define('WP2_NEW_SLUG', basename(dirname(__FILE__)));
}


if (!defined('WP2_NEW_DIR')) {
    define('WP2_NEW_DIR', plugin_dir_path(__FILE__));
}

if (!defined('WP2_NEW_URL')) {
    define('WP2_NEW_URL', plugin_dir_url(__FILE__));
}

// Register the autoloader
spl_autoload_register(function ($class) {
    $namespace = 'WP2\\New\\';
    if (strpos($class, $namespace) === 0) {
        $relative_class = substr($class, strlen($namespace));
        $relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $relative_class);
        $relative_path = strtolower($relative_path);
        $file = WP2_NEW_PATH . 'src/' . $relative_path . '.php';

        if (file_exists($file)) {
            require_once $file;
        } elseif (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("[WP2 New] Autoloader Error: Unable to load class '{$class}'. Expected path: {$file}");
        }
    }
});
