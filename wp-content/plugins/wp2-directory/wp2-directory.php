<?php

/**
 * Plugin Name: WP2 Directory
 * Description: The directory system for WP2. Extends Blockstudio with a modular bootstrapping experience.
 * Version: 1.0
 * Author: WP2S
 *
 * @package WP2_Directory
 */

namespace WP2_Directory;

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

use WP2_Daemon\WP2_Studio\Handlers\Instance\Controller as StudioController;

/**
 * Class Plugin
 *
 * Main class for the WP2 Directory plugin. This class defines required constants,
 * registers directories for block types, and initializes the integration with Blockstudio.
 *
 * @package WP2_Directory
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
        WP2_DIRECTORY_DIR . '/src/Blocks/Namespaces/wp2-directory',
        WP2_DIRECTORY_DIR . '/src/Catalogs/Plugins',
        WP2_DIRECTORY_DIR . '/src/Helpers',
        WP2_DIRECTORY_DIR . '/src/Types',
        WP2_DIRECTORY_DIR . '/src/Syncs',
    ];

    /**
     * Studio controller instance.
     *
     * @var StudioController|null
     */
    private $studio_controller;

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        // Set constant values using plugin_dir_path and get_template_directory functions.
        $this->constants['WP2_DIRECTORY_DIR'] = plugin_dir_path(__FILE__);
        $this->constants['WP2_DIRECTORY_URL'] = plugin_dir_url(__FILE__);
        $this->constants['WP2_THEME_DIR']     = get_template_directory();
        $this->constants['WP2_THEME_URL']     = get_template_directory_uri();

        // Define the constants.
        $this->define_constants();

        // Initialize the plugin functionality.
        add_action('init', [$this, 'init_module'], 100);
    }


    //

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
        // Instantiate the Studio Controller.
        $this->studio_controller = new StudioController();

        // Register directories with the Studio Controller.
        $this->studio_controller->register_directories($this->directories);
    }
}

new Module();
