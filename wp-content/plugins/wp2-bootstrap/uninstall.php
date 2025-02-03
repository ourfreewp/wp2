<?php

namespace WP2\Bootstrap\Template;

defined('WP_UNINSTALL_PLUGIN') || exit; // Exit if accessed directly

// Include the Uninstall class and handle logic
if (file_exists(__DIR__ . '/src/uninstall.php')) {
    require_once __DIR__ . '/src/uninstall.php';
} else {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[WP2 Bootstrap Template] Uninstall file not found: src/uninstall.php');
    }
    exit('Uninstall failed: Required uninstall file is missing.');
}

try {
    if (class_exists(Uninstall::class)) {
        // Use the Uninstall class if available
        Uninstall::handle();
    } else {
        exit('Uninstall failed: Uninstall class not found.');
    }
} catch (\Exception $e) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[WP2 Bootstrap Template] Uninstall failed: ' . $e->getMessage());
    }
    exit('Uninstall failed: ' . $e->getMessage());
}
