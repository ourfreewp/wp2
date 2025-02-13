<?php
// Path: wp-content/mu-plugins/wp2.php
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

$constants = [
    'WP2_MU_PLUGIN_DIR'  => WPMU_PLUGIN_DIR,
    'WP2_CORE_DIR'       => WP_CONTENT_DIR . '/plugins/wp2',
    'WP2_NEW_DIR'        => WP_CONTENT_DIR . '/plugins/wp2-new',
    'WP2_WIKI_DIR'       => WP_CONTENT_DIR . '/plugins/wp2-wiki',
    'WP2_WORK_DIR'       => WP_CONTENT_DIR . '/plugins/wp2-work',
    'WP2_DIRECTORY_DIR'  => WP_CONTENT_DIR . '/plugins/wp2-directory',
];

foreach ($constants as $name => $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

if (!defined('WP2_NAMESPACE')) {
    define('WP2_NAMESPACE', 'wp2');
    define('WP2_PREFIX', 'wp2_');
    define('WP2_TEXTDOMAIN', 'wp2');
}

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
            error_log("[WP2 Daemon] Unable to load class '{$class}'. Searched in wp2-* modules.");
        }
    }
});


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
