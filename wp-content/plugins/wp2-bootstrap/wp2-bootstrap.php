<?php

/**
 * Plugin Name: WP2 Bootstrap — Template
 * Description: A utility plugin for setting up WordPress sites with predefined templates, settings, and roles.
 */

namespace WP2\Bootstrap;

defined('ABSPATH') || exit;

// Define constants
if (!defined('WP2_BOOTSTRAP_TEMPLATE_VERSION')) {
    define('WP2_BOOTSTRAP_TEMPLATE_VERSION', '1.0');
}
if (!defined('WP2_BOOTSTRAP_TEMPLATE_PATH')) {
    define('WP2_BOOTSTRAP_TEMPLATE_PATH', plugin_dir_path(__FILE__));
}
if (!defined('WP2_BOOTSTRAP_TEMPLATE_URL')) {
    define('WP2_BOOTSTRAP_TEMPLATE_URL', plugin_dir_url(__FILE__));
}
if (!defined('WP2_BOOTSTRAP_TEMPLATE_SLUG')) {
    define('WP2_BOOTSTRAP_TEMPLATE_SLUG', basename(dirname(__FILE__)));
}

// Register the autoloader
spl_autoload_register(function ($class) {
    $namespace = 'WP2\\Bootstrap\\';
    if (strpos($class, $namespace) === 0) {
        $relative_class = substr($class, strlen($namespace));
        $relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $relative_class);
        $relative_path = strtolower($relative_path);
        $file = WP2_BOOTSTRAP_TEMPLATE_PATH . 'src/' . $relative_path . '.php';

        if (file_exists($file)) {
            require_once $file;
        } elseif (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("[WP2 Bootstrap Template] Autoloader Error: Unable to load class '{$class}'. Expected path: {$file}");
        }
    }
});
