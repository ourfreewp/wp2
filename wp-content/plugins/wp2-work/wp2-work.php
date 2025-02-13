<?php

/**
 * Plugin Name: WP2 Work
 * Description: 
 * Version: 1.0
 * Author: WP2S
 *
 * @package WP2_Work
 */

namespace WP2_Work;

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

use WP2_Daemon\WP2_Studio\Handlers\Instance\Controller as StudioController;

/**
 * Class Plugin
 *
 * Main class for the WP2 Work plugin. This class defines required constants,
 * registers directories for block types, and initializes the integration with Blockstudio.
 *
 * @package WP2_Work
 */
class Module
{

    /**
     * Associative array of constants to define.
     *
     * @var array
     */
    private $constants = [];

    /**
     * Directories to be registered with the Studio Controller.
     *
     * @var array
     */
    private $directories = [
        WP2_WORK_DIR . '/src/Helpers',
        WP2_WORK_DIR . '/src/Types',
    ];

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        // Set constant values using plugin_dir_path and get_template_directory functions.
        $this->constants['WP2_WORK_DIR'] = plugin_dir_path(__FILE__);
        $this->constants['WP2_WORK_DIR'] = plugin_dir_url(__FILE__);
        $this->constants['WP2_THEME_DIR'] = get_template_directory();
        $this->constants['WP2_THEME_URL'] = get_template_directory_uri();

        // Define the constants.
        $this->define_constants();

        // Initialize the plugin functionality.
        add_action('init', [$this, 'init_module'], 100);
    }

    /**
     * Defines required constants.
     *
     * @return void
     */
    private function define_constants(): void
    {
        foreach ($this->constants as $name => $value) {
            if (! defined($name)) {
                define($name, $value);
            }
        }
    }

    /**
     * Initializes the plugin functionality.
     *
     * This method instantiates the Studio Controller and registers directories
     * containing block definitions. It is called on the 'init' hook.
     *
     * @return void
     */
    public function init_module(): void
    {
        $studio_controller = new StudioController();

        $studio_controller->register_directories($this->directories);
    }
}
new Module();
