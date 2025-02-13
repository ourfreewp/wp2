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

    private $option_name = 'wp2_studio_instances';


    private $directories = [];

    public function __construct()
    {
        add_action('init', [$this, 'initialize_instances'], 100);
    }

    public function get_directory_data()
    {
        $option = get_option($this->option_name);

        return $option ? $option : [];
    }

    public function add_new_directory(string $dir)
    {
        $directories = $this->get_directory_data();

        if (!in_array($dir, $directories)) {
            $directories[] = $dir;

            // ensure unique values
            $directories = array_unique($directories);
            update_option($this->option_name, $directories);
        }
    }

    public function register_directories(array $directories)
    {
        foreach ($directories as $dir) {
            $this->register_directory($dir);
        }
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

        // Optionally check if directory exists now; if it doesn't exist, you might choose to register anyway.
        if (!is_dir($dir)) {
            error_log("[WP2 Instance] Directory '{$dir}' does not exist. Registration skipped.");
            return false;
        }

        // Add the directory to the list of registered directories.
        $this->add_new_directory($dir);

        // Return true to indicate successful registration.

        return true;
    }

    /**
     * Initialize Blockstudio for each registered directory.
     *
     * This method is hooked into the WordPress init action.
     */
    public function initialize_instances()
    {
        $directories = $this->get_directory_data();

        do_action('qm/debug', 'WP2 Studio Instances ' . print_r($directories, true));

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

new Controller();
