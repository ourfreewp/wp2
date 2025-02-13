<?php
// Path: wp-content/mu-plugins/wp2-studio/src/Handlers/Instance/init.php
/**
 * Instance Handler for WP2 Studio.
 * This handler is responsible for managing the initialization of Blockstudio instances.
 * 
 */

namespace WP2_Daemon\WP2_Studio\Handlers\Instance;

class Controller
{
    /**
     * @var array Registered directories.
     */
    private $directories = [];

    public function __construct()
    {

        add_action('init', [$this, 'initialize_instances'], PHP_INT_MAX);
    }

    /**
     * Registers a directory for Blockstudio initialization.
     *
     * @param string $dir Full filesystem path to the directory.
     * @return bool True if the directory was successfully registered, false otherwise.
     */
    public function register_directory(string $dir): bool
    {
        // Sanitize the directory path.
        $dir = trailingslashit($dir);

        // Validate: non-empty string.
        if (empty($dir)) {
            error_log('[WP2 Instance] Empty directory passed for registration.');
            return false;
        }

        // Check if directory is already registered.
        if (in_array($dir, $this->directories, true)) {
            return false;
        }

        // Optionally check if directory exists now; if it doesn't exist, you might choose to register anyway.
        if (!is_dir($dir)) {
            error_log("[WP2 Instance] Directory '{$dir}' does not exist. Registration skipped.");
            return false;
        }

        // Register the directory.
        $this->directories[] = $dir;

        return true;
    }

    /**
     * Initialize Blockstudio for each registered directory.
     *
     * This method is hooked into the WordPress init action.
     */
    public function initialize_instances(): void
    {

        $directories = $this->directories;

        if (defined('BLOCKSTUDIO')) {

            foreach ($directories as $dir) {
                if (is_dir($dir)) {
                    \Blockstudio\Build::init([
                        'dir' => $dir,
                    ]);
                } else {
                    error_log("[WP2 Instance] Registered directory '{$dir}' no longer exists.");
                }
            }
        }
    }
}
